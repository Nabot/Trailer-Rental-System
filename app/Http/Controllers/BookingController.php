<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Trailer;
use App\Models\Customer;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService
    ) {
        // Authorization handled in each method
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Booking::class);
        
        $query = Booking::with(['trailer', 'customer']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by trailer
        if ($request->has('trailer_id') && $request->trailer_id !== '') {
            $query->where('trailer_id', $request->trailer_id);
        }

        // Filter by customer (for customer role)
        if (auth()->user()->isCustomer() && auth()->user()->customer_id) {
            $query->where('customer_id', auth()->user()->customer_id);
        } elseif ($request->has('customer_id') && $request->customer_id !== '') {
            $query->where('customer_id', $request->customer_id);
        }

        // Date range filter
        if ($request->has('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        // Search by booking number
        if ($request->has('search') && $request->search !== '') {
            $query->where('booking_number', 'like', "%{$request->search}%");
        }

        $bookings = $query->latest()->paginate(20);

        $trailers = Trailer::where('status', 'available')->get();
        $customers = Customer::all();

        return view('bookings.index', compact('bookings', 'trailers', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Booking::class);
        
        $trailers = Trailer::where('status', 'available')->get();
        $customers = Customer::all();

        return view('bookings.create', compact('trailers', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $this->authorize('create', Booking::class);
        
        try {
            $booking = $this->bookingService->createBooking(
                $request->validated(),
                auth()->id()
            );

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking created successfully. Invoice has been automatically generated.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        
        $booking->load([
            'trailer.photos',
            'customer',
            'addons',
            'payments',
            'inspections.photos',
            'inspections.damageItems',
            'invoices.items',
            'invoices',
            'contract',
            'pickupInspection.inspectedBy',
            'returnInspection.inspectedBy',
        ]);

        return view('bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        $this->authorize('update', $booking);
        
        if (!in_array($booking->status, ['draft', 'pending'])) {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Only draft or pending bookings can be edited.');
        }

        $trailers = Trailer::where('status', 'available')
            ->orWhere('id', $booking->trailer_id)
            ->get();
        $customers = Customer::all();

        return view('bookings.edit', compact('booking', 'trailers', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);
        
        if (!in_array($booking->status, ['draft', 'pending'])) {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Only draft or pending bookings can be edited.');
        }

        $validated = $request->validate([
            'trailer_id' => 'required|exists:trailers,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'pickup_time' => 'nullable|date_format:H:i',
            'whatsapp_number' => 'nullable|string|max:20',
            'delivery_fee' => 'nullable|numeric|min:0',
            'straps_fee' => 'nullable|numeric|min:0',
            'damage_waiver_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Check availability (excluding current booking)
        $trailer = Trailer::findOrFail($validated['trailer_id']);
        if (!$trailer->isAvailableForDates($validated['start_date'], $validated['end_date'], $booking->id)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Trailer is not available for the selected dates.');
        }

        // Recalculate costs
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $rentalCost = $totalDays * $trailer->rate_per_day;
        $subtotal = $rentalCost + ($validated['delivery_fee'] ?? 0) + ($validated['straps_fee'] ?? 0) + ($validated['damage_waiver_fee'] ?? 0);

        $booking->update([
            'trailer_id' => $validated['trailer_id'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'pickup_time' => $validated['pickup_time'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? $booking->whatsapp_number,
            'total_days' => $totalDays,
            'rate_per_day' => $trailer->rate_per_day,
            'rental_cost' => $rentalCost,
            'delivery_fee' => $validated['delivery_fee'] ?? 0,
            'straps_fee' => $validated['straps_fee'] ?? 0,
            'damage_waiver_fee' => $validated['damage_waiver_fee'] ?? 0,
            'subtotal' => $subtotal,
            'total_amount' => $subtotal,
            'notes' => $validated['notes'] ?? null,
        ]);

        $booking->updateBalance();

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);

        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    /**
     * Confirm a booking.
     */
    public function confirm(Request $request, Booking $booking)
    {
        $this->authorize('confirm', $booking);

        try {
            $this->bookingService->confirmBooking($booking);

            // If inspection data is provided, create pickup inspection
            if ($request->has('inspection_data') && !empty($request->input('inspection_data'))) {
                $inspectionData = $request->input('inspection_data');
                // Handle JSON string
                if (is_string($inspectionData)) {
                    $inspectionData = json_decode($inspectionData, true);
                }
                if (is_array($inspectionData) && !empty($inspectionData)) {
                    $this->createPickupInspection($booking, $inspectionData, $request);
                }
            }

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking confirmed successfully. ' . ($request->has('inspection_data') ? 'Pickup inspection completed.' : ''));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Create pickup inspection from booking confirmation.
     */
    private function createPickupInspection(Booking $booking, $inspectionData, Request $request)
    {
        // Handle JSON string if passed as string
        if (is_string($inspectionData)) {
            $inspectionData = json_decode($inspectionData, true);
        }

        $inspection = \App\Models\Inspection::create([
            'booking_id' => $booking->id,
            'type' => 'pickup',
            'checklist' => $inspectionData['checklist'] ?? [],
            'notes' => $inspectionData['notes'] ?? null,
            'condition_notes' => $inspectionData['condition_notes'] ?? null,
            'is_damaged' => $inspectionData['is_damaged'] ?? false,
            'inspected_by' => auth()->id(),
            'inspected_at' => now(),
        ]);

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('inspections', 'public');
                $inspection->photos()->create([
                    'path' => $path,
                    'disk' => 'public',
                    'description' => null,
                ]);
            }
        }

        return $inspection;
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, Booking $booking)
    {
        $this->authorize('cancel', $booking);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $this->bookingService->cancelBooking($booking, $validated['reason'] ?? null);

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Start rental (mark as active).
     */
    public function startRental(Booking $booking)
    {
        $this->authorize('update', $booking);

        if ($booking->status !== 'confirmed') {
            return redirect()->back()
                ->with('error', 'Only confirmed bookings can be started.');
        }

        // Check if pickup inspection exists
        $pickupInspection = $booking->pickupInspection;
        if (!$pickupInspection) {
            return redirect()->route('inspections.create', [
                'booking_id' => $booking->id,
                'type' => 'pickup'
            ])->with('info', 'Please complete the pickup inspection before starting the rental.');
        }

        $booking->transitionTo('active');
        $booking->update(['picked_up_at' => now()]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Rental started successfully.');
    }

    /**
     * Return trailer (mark as returned).
     */
    public function returnTrailer(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        if ($booking->status !== 'active') {
            return redirect()->back()
                ->with('error', 'Only active rentals can be returned.');
        }

        // Check if return inspection exists or create from request
        $returnInspection = $booking->returnInspection;
        
        if (!$returnInspection) {
            // If inspection data is provided, create return inspection
            if ($request->has('inspection_data') && !empty($request->input('inspection_data'))) {
                $inspectionData = $request->input('inspection_data');
                // Handle JSON string
                if (is_string($inspectionData)) {
                    $inspectionData = json_decode($inspectionData, true);
                }
                if (is_array($inspectionData) && !empty($inspectionData)) {
                    $returnInspection = $this->createReturnInspection($booking, $inspectionData, $request);
                } else {
                    return redirect()->route('inspections.create', [
                        'booking_id' => $booking->id,
                        'type' => 'return'
                    ])->with('info', 'Please complete the return inspection before returning the trailer.');
                }
            } else {
                return redirect()->route('inspections.create', [
                    'booking_id' => $booking->id,
                    'type' => 'return'
                ])->with('info', 'Please complete the return inspection before returning the trailer.');
            }
        }

        $booking->transitionTo('returned');
        $booking->update(['returned_at' => now()]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Trailer returned successfully.');
    }

    /**
     * Create return inspection from booking return.
     */
    private function createReturnInspection(Booking $booking, $inspectionData, Request $request)
    {
        // Handle JSON string if passed as string
        if (is_string($inspectionData)) {
            $inspectionData = json_decode($inspectionData, true);
        }

        $inspection = \App\Models\Inspection::create([
            'booking_id' => $booking->id,
            'type' => 'return',
            'checklist' => $inspectionData['checklist'] ?? [],
            'notes' => $inspectionData['notes'] ?? null,
            'condition_notes' => $inspectionData['condition_notes'] ?? null,
            'is_damaged' => $inspectionData['is_damaged'] ?? false,
            'inspected_by' => auth()->id(),
            'inspected_at' => now(),
        ]);

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('inspections', 'public');
                $inspection->photos()->create([
                    'path' => $path,
                    'disk' => 'public',
                    'description' => null,
                ]);
            }
        }

        // Handle damage items
        if (!empty($inspectionData['damage_items'])) {
            foreach ($inspectionData['damage_items'] as $damageData) {
                $damageItem = $inspection->damageItems()->create([
                    'description' => $damageData['description'],
                    'location' => $damageData['location'] ?? null,
                    'estimated_cost' => $damageData['estimated_cost'],
                    'severity' => $damageData['severity'] ?? 'minor',
                ]);

                // Handle photos for specific damage items
                if (isset($damageData['photos'])) {
                    foreach ($damageData['photos'] as $photo) {
                        $path = $photo->store('inspections/damages', 'public');
                        $damageItem->photos()->create([
                            'path' => $path,
                            'disk' => 'public',
                            'description' => $damageData['description'],
                        ]);
                    }
                }
            }

            $inspection->updateTotalDamageCost();

            // If damage cost exceeds deposit, create additional invoice
            if ($inspection->total_damage_cost > $booking->required_deposit) {
                $this->createDamageInvoice($booking, $inspection);
            }
        }

        return $inspection;
    }

    /**
     * Create an invoice for damages exceeding deposit.
     */
    private function createDamageInvoice(Booking $booking, \App\Models\Inspection $inspection)
    {
        $excessAmount = $inspection->total_damage_cost - $booking->required_deposit;

        if ($excessAmount <= 0) {
            return;
        }

        $invoice = \App\Models\Invoice::create([
            'booking_id' => $booking->id,
            'customer_id' => $booking->customer_id,
            'type' => 'damage',
            'invoice_date' => now(),
            'due_date' => now()->addDays(14),
            'subtotal' => $excessAmount,
            'tax' => 0,
            'total_amount' => $excessAmount,
            'paid_amount' => 0,
            'balance' => $excessAmount,
            'status' => 'pending',
            'notes' => "Damage charges exceeding deposit for booking {$booking->booking_number}",
        ]);

        // Add invoice items for each damage
        foreach ($inspection->damageItems as $damageItem) {
            $invoice->items()->create([
                'description' => "Damage: {$damageItem->description} ({$damageItem->location})",
                'quantity' => 1,
                'unit_price' => $damageItem->estimated_cost,
                'total' => $damageItem->estimated_cost,
            ]);
        }

        return $invoice;
    }

    /**
     * Display calendar view of bookings.
     */
    public function calendar(Request $request)
    {
        $this->authorize('viewAny', Booking::class);

        $trailerId = $request->get('trailer_id');
        
        // Handle month parameter (can be "YYYY-MM" format or separate month/year)
        $monthParam = $request->get('month');
        if ($monthParam && strpos($monthParam, '-') !== false) {
            // Format: "YYYY-MM"
            [$year, $month] = explode('-', $monthParam);
            $month = (int) $month;
            $year = (int) $year;
        } else {
            $month = $request->get('month', now()->month);
            $year = $request->get('year', now()->year);
        }

        $query = Booking::with(['trailer', 'customer'])
            ->where('status', '!=', 'cancelled');

        if ($trailerId) {
            $query->where('trailer_id', $trailerId);
        }

        // Get bookings for the selected month
        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $bookings = $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($q2) use ($startDate, $endDate) {
                    $q2->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        })->get();

        $trailers = \App\Models\Trailer::all();

        // Group bookings by date
        $calendarBookings = [];
        foreach ($bookings as $booking) {
            $currentDate = $booking->start_date->copy();
            while ($currentDate->lte($booking->end_date) && $currentDate->lte($endDate)) {
                if ($currentDate->gte($startDate)) {
                    $dateKey = $currentDate->format('Y-m-d');
                    if (!isset($calendarBookings[$dateKey])) {
                        $calendarBookings[$dateKey] = [];
                    }
                    $calendarBookings[$dateKey][] = $booking;
                }
                $currentDate->addDay();
            }
        }

        return view('bookings.calendar', compact('bookings', 'trailers', 'trailerId', 'month', 'year', 'startDate', 'calendarBookings'));
    }
}
