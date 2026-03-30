<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\DepositRefund;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositRefundController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        $this->authorize('payments.create');

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'refund_date' => 'required|date',
            'method' => 'required|in:eft,cash,card,other',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $booking->loadMissing(['customer', 'invoices']);

        $depositCharged = (float) ($booking->required_deposit ?? 0);
        $depositRefunded = (float) $booking->depositRefunds()->where('status', 'paid')->sum('amount');
        $remainingRefundable = max(0, $depositCharged - $depositRefunded);
        $availableCollectedAmount = max(0, (float) $booking->paid_amount);
        $maxRefundNow = min($remainingRefundable, $availableCollectedAmount);

        if ($maxRefundNow <= 0) {
            return redirect()->back()->with('error', 'No refundable deposit remains for this booking.');
        }

        $amount = (float) $validated['amount'];
        if ($amount > $maxRefundNow) {
            return redirect()->back()->with('error', 'Refund exceeds the allowed amount (N$' . number_format($maxRefundNow, 2) . ').');
        }

        DB::transaction(function () use ($validated, $booking, $amount) {
            $rentalInvoice = $booking->invoices()->where('type', 'rental')->first();

            $refund = DepositRefund::create([
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'invoice_id' => $rentalInvoice?->id,
                'amount' => $amount,
                'refund_date' => $validated['refund_date'],
                'method' => $validated['method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'paid',
                'processed_by' => auth()->id(),
            ]);

            // Refund reduces net collected cash for booking/invoice.
            $booking->paid_amount = max(0, (float) $booking->paid_amount - $amount);
            $booking->updateBalance();

            if ($rentalInvoice) {
                $rentalInvoice->paid_amount = max(0, (float) $rentalInvoice->paid_amount - $amount);
                $rentalInvoice->updateBalance();
            }

            \App\Models\ActivityLog::log('deposit.refunded', DepositRefund::class, $refund->id, [
                'booking_id' => $booking->id,
                'amount' => $amount,
            ]);
        });

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Deposit refund recorded successfully.');
    }

    public function show(DepositRefund $depositRefund)
    {
        $this->authorize('payments.view');
        $depositRefund->load(['booking.trailer', 'customer', 'invoice', 'processedBy']);

        return view('deposit-refunds.show', compact('depositRefund'));
    }

    public function download(DepositRefund $depositRefund)
    {
        $this->authorize('payments.view');
        $depositRefund->load(['booking.trailer', 'customer', 'invoice', 'processedBy']);

        $companyName = \App\Models\Setting::get('company_name', 'IronAxle Trailers');
        $companyAddress = \App\Models\Setting::get('company_address', '');
        $companyPhone = \App\Models\Setting::get('company_phone', '');
        $companyEmail = \App\Models\Setting::get('company_email', '');

        $pdf = Pdf::loadView('deposit-refunds.receipt-pdf', compact(
            'depositRefund',
            'companyName',
            'companyAddress',
            'companyPhone',
            'companyEmail'
        ));

        return $pdf->download("deposit-refund-{$depositRefund->id}.pdf");
    }
}
