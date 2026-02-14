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

            // Calculate days (inclusive)
            $startDate = \Carbon\Carbon::parse($data['start_date']);
            $endDate = \Carbon\Carbon::parse($data['end_date']);
            $totalDays = $startDate->diffInDays($endDate) + 1;

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

            // Automatically create invoice for the booking
            $this->createInvoiceForBooking($booking->fresh());

            return $booking->fresh();
        });
    }

    /**
     * Automatically create an invoice for a booking.
     */
    protected function createInvoiceForBooking(Booking $booking): Invoice
    {
        // Check if invoice already exists
        $existingInvoice = Invoice::where('booking_id', $booking->id)
            ->where('type', 'rental')
            ->first();

        if ($existingInvoice) {
            return $existingInvoice;
        }

        // Load relationships if not already loaded
        $booking->load(['trailer', 'customer']);

        $subtotal = $booking->total_amount;
        $taxRate = \App\Models\Setting::get('tax_rate', 0);
        $tax = $subtotal * ($taxRate / 100);
        $totalAmount = $subtotal + $tax;

        $invoice = Invoice::create([
            'booking_id' => $booking->id,
            'customer_id' => $booking->customer_id,
            'type' => 'rental',
            'invoice_date' => now(),
            'due_date' => now()->addDays(14),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'balance' => $totalAmount,
            'status' => 'pending',
            'notes' => "Rental invoice for booking {$booking->booking_number}",
        ]);

        // Add rental cost
        $invoice->items()->create([
            'description' => "Trailer Rental - {$booking->trailer->name} ({$booking->total_days} days @ N$" . number_format($booking->rate_per_day, 2) . "/day)",
            'quantity' => 1,
            'unit_price' => $booking->rental_cost,
            'total' => $booking->rental_cost,
        ]);

        // Add fees if any
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

        Log::info("Invoice automatically created for booking {$booking->booking_number}", [
            'booking_id' => $booking->id,
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
        ]);

        return $invoice;
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
