<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Trailer;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingService
{
    public function createBooking(array $data, ?int $createdBy = null): Booking
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $trailer = Trailer::findOrFail($data['trailer_id']);
            $customer = Customer::findOrFail($data['customer_id']);

            // Check availability with lock
            $isAvailable = DB::transaction(function () use ($trailer, $data) {
                return $trailer->isAvailableForDates($data['start_date'], $data['end_date']);
            }, 5);

            if (!$isAvailable) {
                throw new \Exception('Trailer is not available for the selected dates.');
            }

            // Calculate days using a 24-hour-period model: same-day pickup
            // and return counts as 1 day (minimum), each additional
            // calendar day adds 1 more day.
            $startDate = \Carbon\Carbon::parse($data['start_date']);
            $endDate = \Carbon\Carbon::parse($data['end_date']);
            $totalDays = max(1, (int) $startDate->diffInDays($endDate));

            // Calculate costs
            $ratePerDay = $trailer->rate_per_day;
            $rentalCost = $totalDays * $ratePerDay;
            $requiredDeposit = $data['required_deposit'] ?? $trailer->required_deposit ?? \App\Models\Setting::get('default_deposit_amount', 1000);
            
            $deliveryFee = $data['delivery_fee'] ?? 0;
            $strapsFee = $data['straps_fee'] ?? 0;
            $damageWaiverFee = $data['damage_waiver_fee'] ?? 0;
            
            $subtotal = $rentalCost + $deliveryFee + $strapsFee + $damageWaiverFee;
            $totalAmount = $subtotal;
            $balance = $totalAmount;

            $booking = Booking::create([
                'trailer_id' => $trailer->id,
                'customer_id' => $customer->id,
                'created_by' => $createdBy ?? auth()->id(),
                'status' => $data['status'] ?? 'draft',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'pickup_time' => $data['pickup_time'] ?? null,
                'whatsapp_number' => $data['whatsapp_number'] ?? null,
                'total_days' => $totalDays,
                'rate_per_day' => $ratePerDay,
                'rental_cost' => $rentalCost,
                'required_deposit' => $requiredDeposit,
                'delivery_fee' => $deliveryFee,
                'straps_fee' => $strapsFee,
                'damage_waiver_fee' => $damageWaiverFee,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'balance' => $balance,
                'notes' => $data['notes'] ?? null,
            ]);

            // Create addons if provided
            if (isset($data['addons']) && is_array($data['addons'])) {
                foreach ($data['addons'] as $addon) {
                    $booking->addons()->create($addon);
                }
            }

            // Audit log
            AuditLog::log('booking.created', $booking, null, $booking->toArray(), "Booking created for trailer {$trailer->name}");

            // Automatically create/sync the rental invoice for the booking
            $this->syncRentalInvoice($booking->fresh());

            return $booking->fresh();
        });
    }

    /**
     * Create the rental invoice for a booking if it doesn't exist yet,
     * otherwise rebuild its standard line items (rental, fees, deposit)
     * from the booking's current values. Any custom items added later
     * (e.g. extensions, late return fees, manual entries) are preserved.
     */
    public function syncRentalInvoice(Booking $booking): Invoice
    {
        return DB::transaction(function () use ($booking) {
            $booking->load(['trailer', 'customer']);

            $invoice = Invoice::where('booking_id', $booking->id)
                ->where('type', 'rental')
                ->first();

            $isNew = false;
            if (!$invoice) {
                $invoice = Invoice::create([
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'type' => 'rental',
                    'invoice_date' => now(),
                    'due_date' => now()->addDays(14),
                    'subtotal' => 0,
                    'tax' => 0,
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'balance' => 0,
                    'status' => 'pending',
                    'notes' => "Rental invoice for booking {$booking->booking_number}",
                ]);
                $isNew = true;
            } else {
                // Remove standard items only; keep any manually-added or
                // post-creation items (extensions, late fees, etc.).
                $invoice->items()
                    ->where(function ($q) {
                        $q->where('description', 'like', 'Trailer Rental -%')
                          ->orWhere('description', 'Delivery Fee')
                          ->orWhere('description', 'Straps Fee')
                          ->orWhere('description', 'Damage Waiver Fee')
                          ->orWhere('description', 'Deposit (refundable)');
                    })
                    ->delete();
            }

            $invoice->items()->create([
                'description' => "Trailer Rental - {$booking->trailer->name} ({$booking->total_days} days @ N$" . number_format($booking->rate_per_day, 2) . "/day)",
                'quantity' => 1,
                'unit_price' => $booking->rental_cost,
                'total' => $booking->rental_cost,
            ]);

            if ($booking->delivery_fee > 0) {
                $invoice->items()->create([
                    'description' => 'Delivery Fee',
                    'quantity' => 1,
                    'unit_price' => $booking->delivery_fee,
                    'total' => $booking->delivery_fee,
                ]);
            }

            if ($booking->straps_fee > 0) {
                $invoice->items()->create([
                    'description' => 'Straps Fee',
                    'quantity' => 1,
                    'unit_price' => $booking->straps_fee,
                    'total' => $booking->straps_fee,
                ]);
            }

            if ($booking->damage_waiver_fee > 0) {
                $invoice->items()->create([
                    'description' => 'Damage Waiver Fee',
                    'quantity' => 1,
                    'unit_price' => $booking->damage_waiver_fee,
                    'total' => $booking->damage_waiver_fee,
                ]);
            }

            $deposit = (float) ($booking->required_deposit ?? 0);
            if ($deposit > 0) {
                $invoice->items()->create([
                    'description' => 'Deposit (refundable)',
                    'quantity' => 1,
                    'unit_price' => $deposit,
                    'total' => $deposit,
                ]);
            }

            $invoice->recalculateFromItems();

            Log::info(
                ($isNew ? 'Rental invoice created' : 'Rental invoice resynced')
                . " for booking {$booking->booking_number}",
                [
                    'booking_id' => $booking->id,
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                ]
            );

            return $invoice->fresh();
        });
    }

    public function confirmBooking(Booking $booking): bool
    {
        if (!$booking->canTransitionTo('confirmed')) {
            throw new \Exception("Booking cannot be confirmed from status: {$booking->status}");
        }

        return DB::transaction(function () use ($booking) {
            $oldStatus = $booking->status;
            $booking->transitionTo('confirmed');
            
            AuditLog::log('booking.confirmed', $booking, ['status' => $oldStatus], ['status' => $booking->status], "Booking {$booking->booking_number} confirmed");
            
            // Dispatch event for notifications
            // event(new BookingConfirmed($booking));
            
            return true;
        });
    }

    public function cancelBooking(Booking $booking, string $reason = null): bool
    {
        if (!$booking->canTransitionTo('cancelled')) {
            throw new \Exception("Booking cannot be cancelled from status: {$booking->status}");
        }

        return DB::transaction(function () use ($booking, $reason) {
            $oldStatus = $booking->status;
            $booking->cancellation_reason = $reason;
            $booking->transitionTo('cancelled');
            
            AuditLog::log('booking.cancelled', $booking, ['status' => $oldStatus], ['status' => $booking->status], "Booking {$booking->booking_number} cancelled: {$reason}");
            
            return true;
        });
    }
}
