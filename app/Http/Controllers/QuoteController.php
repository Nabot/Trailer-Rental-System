<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Inquiry;
use App\Models\InquiryActivity;
use App\Models\Customer;
use App\Models\Trailer;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    public function __construct(
        private BookingService $bookingService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Quote::with(['inquiry', 'customer', 'trailer', 'createdBy']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by inquiry
        if ($request->has('inquiry_id') && $request->inquiry_id !== '') {
            $query->where('inquiry_id', $request->inquiry_id);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

            $quotes = $query->latest()->paginate(20);
            $statuses = ['draft', 'sent', 'accepted', 'expired', 'converted'];

            return view('quotes.index', compact('quotes', 'statuses'));
        } catch (\Exception $e) {
            \Log::error('Quote index error: ' . $e->getMessage());
            return view('quotes.index', [
                'quotes' => collect([])->paginate(20),
                'statuses' => ['draft', 'sent', 'accepted', 'expired', 'converted'],
            ])->with('error', 'Error loading quotes: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $inquiryId = $request->get('inquiry_id');
        $inquiry = $inquiryId ? Inquiry::with('customer')->findOrFail($inquiryId) : null;
        
        $trailers = Trailer::where('status', 'available')->get();
        $customers = Customer::all();
        $inquiries = Inquiry::where('status', '!=', 'converted')->latest()->get();

        return view('quotes.create', compact('inquiry', 'trailers', 'customers', 'inquiries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inquiry_id' => 'nullable|exists:inquiries,id',
            'customer_id' => 'required_without:inquiry_id|exists:customers,id',
            'trailer_id' => 'required|exists:trailers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'rate_per_day' => 'required|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'straps_fee' => 'nullable|numeric|min:0',
            'damage_waiver_fee' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'validity_days' => 'nullable|integer|min:1|max:90',
            'notes' => 'nullable|string|max:2000',
            'terms_conditions' => 'nullable|string|max:5000',
            'items' => 'nullable|array',
            'items.*.description' => 'required_with:items|string|max:500',
            'items.*.quantity' => 'required_with:items|numeric|min:0.01',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {
            $trailer = Trailer::findOrFail($validated['trailer_id']);
            $startDate = \Carbon\Carbon::parse($validated['start_date']);
            $endDate = \Carbon\Carbon::parse($validated['end_date']);
            $totalDays = $startDate->diffInDays($endDate) + 1;

            // Get customer from inquiry if not provided
            $customerId = $validated['customer_id'] ?? null;
            if (!$customerId && isset($validated['inquiry_id'])) {
                $inquiry = Inquiry::findOrFail($validated['inquiry_id']);
                $customerId = $inquiry->customer_id;
            }

            $quote = Quote::create([
                'inquiry_id' => $validated['inquiry_id'] ?? null,
                'customer_id' => $customerId,
                'trailer_id' => $validated['trailer_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_days' => $totalDays,
                'rate_per_day' => $validated['rate_per_day'],
                'delivery_fee' => $validated['delivery_fee'] ?? 0,
                'straps_fee' => $validated['straps_fee'] ?? 0,
                'damage_waiver_fee' => $validated['damage_waiver_fee'] ?? 0,
                'tax' => $validated['tax'] ?? 0,
                'validity_days' => $validated['validity_days'] ?? 14,
                'notes' => $validated['notes'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Add quote items if provided
            if (isset($validated['items']) && is_array($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    QuoteItem::create([
                        'quote_id' => $quote->id,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                    ]);
                }
            }

            // Update inquiry status if linked
            if ($quote->inquiry_id) {
                $inquiry = Inquiry::find($quote->inquiry_id);
                if ($inquiry && $inquiry->status !== 'quoted') {
                    $inquiry->update(['status' => 'quoted']);
                }
            }

            return redirect()->route('quotes.show', $quote)
                ->with('success', 'Quote created successfully.');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote $quote)
    {
        $quote->load(['inquiry', 'customer', 'trailer', 'items', 'createdBy', 'convertedToBooking']);
        
        return view('quotes.show', compact('quote'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote)
    {
        if ($quote->status !== 'draft') {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Only draft quotes can be edited.');
        }

        $quote->load('items');
        $trailers = Trailer::where('status', 'available')->get();
        $customers = Customer::all();
        $inquiries = Inquiry::where('status', '!=', 'converted')->latest()->get();

        return view('quotes.edit', compact('quote', 'trailers', 'customers', 'inquiries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quote $quote)
    {
        if ($quote->status !== 'draft') {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Only draft quotes can be edited.');
        }

        $validated = $request->validate([
            'trailer_id' => 'required|exists:trailers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'rate_per_day' => 'required|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'straps_fee' => 'nullable|numeric|min:0',
            'damage_waiver_fee' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'validity_days' => 'nullable|integer|min:1|max:90',
            'notes' => 'nullable|string|max:2000',
            'terms_conditions' => 'nullable|string|max:5000',
            'items' => 'nullable|array',
            'items.*.description' => 'required_with:items|string|max:500',
            'items.*.quantity' => 'required_with:items|numeric|min:0.01',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
        ]);

        return DB::transaction(function () use ($quote, $validated) {
            $startDate = \Carbon\Carbon::parse($validated['start_date']);
            $endDate = \Carbon\Carbon::parse($validated['end_date']);
            $totalDays = $startDate->diffInDays($endDate) + 1;

            $quote->update([
                'trailer_id' => $validated['trailer_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_days' => $totalDays,
                'rate_per_day' => $validated['rate_per_day'],
                'delivery_fee' => $validated['delivery_fee'] ?? 0,
                'straps_fee' => $validated['straps_fee'] ?? 0,
                'damage_waiver_fee' => $validated['damage_waiver_fee'] ?? 0,
                'tax' => $validated['tax'] ?? 0,
                'validity_days' => $validated['validity_days'] ?? 14,
                'notes' => $validated['notes'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
            ]);

            // Update items
            $quote->items()->delete();
            if (isset($validated['items']) && is_array($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    QuoteItem::create([
                        'quote_id' => $quote->id,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                    ]);
                }
            }

            return redirect()->route('quotes.show', $quote)
                ->with('success', 'Quote updated successfully.');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quote)
    {
        if ($quote->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft quotes can be deleted.');
        }

        $quote->delete();

        return redirect()->route('quotes.index')
            ->with('success', 'Quote deleted successfully.');
    }

    /**
     * Mark quote as sent
     */
    public function markAsSent(Quote $quote)
    {
        $quote->markAsSent();

        if ($quote->inquiry_id) {
            $inquiry = Inquiry::find($quote->inquiry_id);
            if ($inquiry) {
                InquiryActivity::create([
                    'inquiry_id' => $inquiry->id,
                    'type' => 'email',
                    'subject' => 'Quote Sent',
                    'description' => "Quote {$quote->quote_number} sent to customer",
                    'created_by' => auth()->id(),
                    'completed_at' => now(),
                ]);
            }
        }

        return redirect()->back()
            ->with('success', 'Quote marked as sent.');
    }

    /**
     * Convert quote to booking
     */
    public function convertToBooking(Request $request, Quote $quote)
    {
        if (!$quote->customer_id) {
            return redirect()->back()
                ->with('error', 'Quote must have a customer to convert to booking.');
        }

        if (!$quote->trailer_id) {
            return redirect()->back()
                ->with('error', 'Quote must have a trailer to convert to booking.');
        }

        try {
            return DB::transaction(function () use ($quote, $request) {
                // Create booking from quote
                $bookingData = [
                    'trailer_id' => $quote->trailer_id,
                    'customer_id' => $quote->customer_id,
                    'start_date' => $quote->start_date->format('Y-m-d'),
                    'end_date' => $quote->end_date->format('Y-m-d'),
                    'rate_per_day' => $quote->rate_per_day,
                    'rental_cost' => $quote->rental_cost,
                    'delivery_fee' => $quote->delivery_fee,
                    'straps_fee' => $quote->straps_fee,
                    'damage_waiver_fee' => $quote->damage_waiver_fee,
                    'whatsapp_number' => $quote->inquiry?->whatsapp_number,
                ];

                $booking = $this->bookingService->createBooking($bookingData, auth()->id());

                // Update quote
                $quote->status = 'converted';
                $quote->converted_to_booking_id = $booking->id;
                $quote->save();

                // Update inquiry if linked
                if ($quote->inquiry_id) {
                    $inquiry = Inquiry::find($quote->inquiry_id);
                    if ($inquiry) {
                        $inquiry->convertToBooking($booking);
                        
                        InquiryActivity::create([
                            'inquiry_id' => $inquiry->id,
                            'type' => 'note',
                            'subject' => 'Converted to Booking',
                            'description' => "Quote {$quote->quote_number} converted to booking {$booking->booking_number}",
                            'created_by' => auth()->id(),
                            'completed_at' => now(),
                        ]);
                    }
                }

                return redirect()->route('bookings.show', $booking)
                    ->with('success', 'Quote converted to booking successfully.');
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Download quote as PDF
     */
    public function download(Quote $quote)
    {
        $quote->load(['customer', 'trailer', 'items']);
        $companyName = \App\Models\Setting::get('company_name', 'IronAxel Rentals');
        $companyAddress = \App\Models\Setting::get('company_address', '');
        $companyPhone = \App\Models\Setting::get('company_phone', '');
        $companyEmail = \App\Models\Setting::get('company_email', '');

        $pdf = Pdf::loadView('quotes.pdf', compact('quote', 'companyName', 'companyAddress', 'companyPhone', 'companyEmail'));
        
        return $pdf->download("quote-{$quote->quote_number}.pdf");
    }
}
