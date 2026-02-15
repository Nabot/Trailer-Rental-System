<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\WhatsAppService;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['booking.trailer', 'booking.customer', 'invoice.customer']);

        // Filter by booking
        if ($request->has('booking_id') && $request->booking_id !== '') {
            $query->where('booking_id', $request->booking_id);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('payment_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('payment_date', '<=', $request->end_date);
        }

        // Filter by method
        if ($request->has('method') && $request->method !== '') {
            $query->where('method', $request->method);
        }

        $payments = $query->latest('payment_date')->paginate(20);

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $bookingId = $request->get('booking_id');
        $invoiceId = $request->get('invoice_id');

        $booking = $bookingId ? Booking::findOrFail($bookingId) : null;
        $invoice = $invoiceId ? Invoice::findOrFail($invoiceId) : null;

        return view('payments.create', compact('booking', 'invoice'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required_without:invoice_id|exists:bookings,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'method' => 'required|in:eft,cash,card,other',
            'amount' => 'required|numeric|min:0.01',
            'reference_number' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $payment = DB::transaction(function () use ($validated) {
            // If booking_id but no invoice_id, link to booking's rental invoice so invoice reflects payment
            if (!empty($validated['booking_id']) && empty($validated['invoice_id'])) {
                $booking = Booking::with('invoices')->findOrFail($validated['booking_id']);
                $rentalInvoice = $booking->invoices->where('type', 'rental')->first();
                if ($rentalInvoice) {
                    $validated['invoice_id'] = $rentalInvoice->id;
                }
            }

            $payment = Payment::create([
                ...$validated,
                'recorded_by' => auth()->id(),
            ]);

            // Update booking paid amount
            if ($payment->booking_id) {
                $booking = Booking::findOrFail($payment->booking_id);
                $booking->paid_amount += $payment->amount;
                $booking->updateBalance();
            }

            // Update invoice paid amount
            if ($payment->invoice_id) {
                $invoice = Invoice::findOrFail($payment->invoice_id);
                $invoice->paid_amount += $payment->amount;
                $invoice->updateBalance();
            }

            return $payment;
        });

        $redirectRoute = $payment->booking_id 
            ? route('bookings.show', $payment->booking_id)
            : route('payments.index');

        return redirect($redirectRoute)
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['booking.trailer', 'booking.customer', 'invoice', 'recordedBy']);

        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        return view('payments.edit', compact('payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'method' => 'required|in:eft,cash,card,other',
            'amount' => 'required|numeric|min:0.01',
            'reference_number' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $oldAmount = $payment->amount;
        $newAmount = $validated['amount'];
        $difference = $newAmount - $oldAmount;

        DB::transaction(function () use ($payment, $validated, $difference) {
            $payment->update($validated);

            // Update booking paid amount
            if ($payment->booking_id) {
                $booking = Booking::findOrFail($payment->booking_id);
                $booking->paid_amount += $difference;
                $booking->updateBalance();
            }

            // Update invoice paid amount
            if ($payment->invoice_id) {
                $invoice = Invoice::findOrFail($payment->invoice_id);
                $invoice->paid_amount += $difference;
                $invoice->updateBalance();
            }
        });

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            $amount = $payment->amount;

            // Update booking paid amount
            if ($payment->booking_id) {
                $booking = Booking::findOrFail($payment->booking_id);
                $booking->paid_amount -= $amount;
                $booking->updateBalance();
            }

            // Update invoice paid amount
            if ($payment->invoice_id) {
                $invoice = Invoice::findOrFail($payment->invoice_id);
                $invoice->paid_amount -= $amount;
                $invoice->updateBalance();
            }

            $payment->delete();
        });

        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    /**
     * Download receipt as PDF.
     */
    public function download(Payment $payment)
    {
        $payment->load(['booking.trailer', 'booking.customer', 'invoice.customer']);

        $companyName = \App\Models\Setting::get('company_name', 'IronAxel Rentals');
        $companyAddress = \App\Models\Setting::get('company_address', '');
        $companyPhone = \App\Models\Setting::get('company_phone', '');
        $companyEmail = \App\Models\Setting::get('company_email', '');

        // Get customer from booking or invoice
        $customer = $payment->booking?->customer ?? $payment->invoice?->customer;

        $pdf = Pdf::loadView('payments.receipt-pdf', compact('payment', 'customer', 'companyName', 'companyAddress', 'companyPhone', 'companyEmail'));
        
        return $pdf->download("receipt-{$payment->id}.pdf");
    }

    /**
     * Send receipt via WhatsApp.
     */
    public function sendWhatsApp(Payment $payment, WhatsAppService $whatsAppService)
    {
        $payment->load(['booking.customer', 'invoice.customer']);
        $customer = $payment->booking?->customer ?? $payment->invoice?->customer;
        
        $whatsappNumber = request('whatsapp_number') 
            ?? $payment->booking?->whatsapp_number 
            ?? $customer?->phone;

        if (!$whatsappNumber) {
            return redirect()->back()
                ->with('error', 'No WhatsApp number found. Please provide a WhatsApp number.');
        }

        $success = $whatsAppService->sendReceipt($payment, $whatsappNumber);

        if ($success) {
            return redirect()->back()
                ->with('success', 'Receipt sent via WhatsApp successfully.');
        }

        return redirect()->back()
            ->with('error', 'Failed to send receipt via WhatsApp. Please check your WhatsApp configuration.');
    }
}
