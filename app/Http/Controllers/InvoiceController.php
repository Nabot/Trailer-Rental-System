<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['booking.trailer', 'customer']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // For customer role, only show their invoices
        if (auth()->user()->isCustomer() && auth()->user()->customer_id) {
            $query->where('customer_id', auth()->user()->customer_id);
        }

        $invoices = $query->latest('invoice_date')->paginate(20);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $bookingId = $request->get('booking_id');
        $booking = $bookingId ? Booking::with(['trailer', 'customer'])->findOrFail($bookingId) : null;
        
        $bookings = Booking::with(['trailer', 'customer'])->latest()->get();
        $customers = Customer::all();

        // Prepare booking data for JSON (for JavaScript auto-population)
        $bookingData = null;
        if ($booking) {
            $bookingData = [
                'id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'rental_cost' => (float) $booking->rental_cost,
                'delivery_fee' => (float) $booking->delivery_fee,
                'straps_fee' => (float) $booking->straps_fee,
                'damage_waiver_fee' => (float) $booking->damage_waiver_fee,
                'total_days' => $booking->total_days,
                'rate_per_day' => (float) $booking->rate_per_day,
                'trailer_name' => $booking->trailer->name,
            ];
        }

        return view('invoices.create', compact('booking', 'bookings', 'customers', 'bookingData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required_without:customer_id|exists:bookings,id',
            'customer_id' => 'required_without:booking_id|exists:customers,id',
            'type' => 'required|in:rental,damage,other',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($validated) {
            // Calculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $taxRate = $validated['tax_rate'] ?? \App\Models\Setting::get('tax_rate', 0);
            $tax = $subtotal * ($taxRate / 100);
            $totalAmount = $subtotal + $tax;

            // Get customer from booking if not provided
            $customerId = $validated['customer_id'] ?? null;
            if (!$customerId && isset($validated['booking_id'])) {
                $booking = Booking::findOrFail($validated['booking_id']);
                $customerId = $booking->customer_id;
            }

            $invoice = Invoice::create([
                'booking_id' => $validated['booking_id'] ?? null,
                'customer_id' => $customerId,
                'type' => $validated['type'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'balance' => $totalAmount,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create invoice items
            foreach ($validated['items'] as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice created successfully.');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load([
            'booking.trailer',
            'customer',
            'items',
            'payments'
        ]);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        if ($invoice->status !== 'pending') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Only pending invoices can be edited.');
        }

        $invoice->load(['items', 'booking.trailer', 'customer']);

        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'pending') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Only pending invoices can be edited.');
        }

        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($validated, $invoice) {
            // Delete existing items
            $invoice->items()->delete();

            // Calculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $taxRate = $validated['tax_rate'] ?? \App\Models\Setting::get('tax_rate', 0);
            $tax = $subtotal * ($taxRate / 100);
            $totalAmount = $subtotal + $tax;

            $invoice->update([
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Recalculate balance
            $invoice->updateBalance();

            // Create new invoice items
            foreach ($validated['items'] as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice updated successfully.');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending invoices can be deleted.');
        }

        $invoice->items()->delete();
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    /**
     * Download invoice as PDF.
     */
    public function download(Invoice $invoice)
    {
        $invoice->load([
            'booking.trailer',
            'customer',
            'items'
        ]);

        $companyName = \App\Models\Setting::get('company_name', 'IronAxel Rentals');
        $companyAddress = \App\Models\Setting::get('company_address', 'Kransneus, Namibia');
        $companyPhone = \App\Models\Setting::get('company_phone', '');
        $companyEmail = \App\Models\Setting::get('company_email', 'bookings@ironaxelrentals.com');
        $bankAccountName = \App\Models\Setting::get('bank_account_name', 'IronAxel Rentals');
        $bankName = \App\Models\Setting::get('bank_name', 'First National Bank Namibia');
        $bankAccountNumber = \App\Models\Setting::get('bank_account_number', '62114687059');
        $bankBranchName = \App\Models\Setting::get('bank_branch_name', 'Private Clients');
        $bankBranchCode = \App\Models\Setting::get('bank_branch_code', '280174');

        $pdf = Pdf::loadView('invoices.pdf', compact(
            'invoice', 
            'companyName', 
            'companyAddress', 
            'companyPhone', 
            'companyEmail',
            'bankAccountName',
            'bankName',
            'bankAccountNumber',
            'bankBranchName',
            'bankBranchCode'
        ));
        
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Generate invoice for a booking.
     */
    public function generateForBooking(Booking $booking)
    {
        // Check if invoice already exists
        $existingInvoice = Invoice::where('booking_id', $booking->id)
            ->where('type', 'rental')
            ->first();

        if ($existingInvoice) {
            return redirect()->route('invoices.show', $existingInvoice)
                ->with('info', 'Invoice already exists for this booking.');
        }

        return DB::transaction(function () use ($booking) {
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

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice generated successfully.');
        });
    }

    /**
     * Get booking data for invoice auto-population.
     */
    public function getBookingInvoiceData(Booking $booking)
    {
        $booking->load(['trailer', 'customer']);

        $items = [];

        // Always include rental cost (even if 0, so user can see the breakdown)
        $items[] = [
            'description' => "Trailer Rental - {$booking->trailer->name} ({$booking->total_days} days @ N$" . number_format($booking->rate_per_day, 2) . "/day)",
            'quantity' => $booking->total_days > 0 ? $booking->total_days : 1,
            'unit_price' => (float) ($booking->rate_per_day > 0 ? $booking->rate_per_day : $booking->rental_cost),
        ];

        // Delivery fee
        if ($booking->delivery_fee > 0) {
            $items[] = [
                'description' => 'Delivery Fee',
                'quantity' => 1,
                'unit_price' => (float) $booking->delivery_fee,
            ];
        }

        // Straps fee
        if ($booking->straps_fee > 0) {
            $items[] = [
                'description' => 'Straps Fee',
                'quantity' => 1,
                'unit_price' => (float) $booking->straps_fee,
            ];
        }

        // Damage waiver fee
        if ($booking->damage_waiver_fee > 0) {
            $items[] = [
                'description' => 'Damage Waiver',
                'quantity' => 1,
                'unit_price' => (float) $booking->damage_waiver_fee,
            ];
        }

        return response()->json([
            'customer_id' => $booking->customer_id,
            'items' => $items,
        ]);
    }
}
