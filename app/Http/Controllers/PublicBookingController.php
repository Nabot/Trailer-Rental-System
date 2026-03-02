<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Trailer;
use App\Models\Customer;
use App\Services\BookingService;
use Illuminate\Http\Request;

class PublicBookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService
    ) {}

    /**
     * Show the public booking form (no auth required).
     */
    public function form(Request $request)
    {
        $validated = $request->validate([
            'trailer_id' => ['required', 'exists:trailers,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $trailer = Trailer::findOrFail($validated['trailer_id']);

        if ($trailer->status !== 'available') {
            return redirect()->route('home')->with('error', 'This trailer is not available for booking.');
        }

        if (!$trailer->isAvailableForDates($validated['start_date'], $validated['end_date'])) {
            return redirect()->route('home', [
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ])->with('error', 'This trailer is no longer available for the selected dates. Please check availability again.');
        }

        $companyName = \App\Models\Setting::get('company_name', config('app.name', 'IronAxle Trailers'));

        return view('book.form', [
            'trailer' => $trailer,
            'startDate' => $validated['start_date'],
            'endDate' => $validated['end_date'],
            'companyName' => $companyName,
        ]);
    }

    /**
     * Store a booking from the public form (find or create customer).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trailer_id' => ['required', 'exists:trailers,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'id_number' => ['nullable', 'string', 'max:50'],
            'driver_licence' => ['nullable', 'string', 'max:50'],
            'pickup_time' => ['nullable', 'date_format:H:i'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'straps_fee' => ['nullable', 'numeric', 'min:0'],
            'damage_waiver_fee' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $trailer = Trailer::findOrFail($validated['trailer_id']);

        if ($trailer->status !== 'available') {
            return redirect()->route('home')->with('error', 'This trailer is not available for booking.');
        }

        if (!$trailer->isAvailableForDates($validated['start_date'], $validated['end_date'])) {
            return redirect()->route('book.form', [
                'trailer_id' => $validated['trailer_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ])->withInput()->with('error', 'This trailer is no longer available for the selected dates.');
        }

        $customer = Customer::where('email', $validated['email'])->first();

        if (!$customer) {
            $customer = Customer::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
                'id_number' => $validated['id_number'] ?? null,
                'driver_licence' => $validated['driver_licence'] ?? null,
            ]);
        } else {
            $customer->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? $customer->address,
                'id_number' => $validated['id_number'] ?? $customer->id_number,
                'driver_licence' => $validated['driver_licence'] ?? $customer->driver_licence,
            ]);
        }

        try {
            $booking = $this->bookingService->createBooking([
                'trailer_id' => $trailer->id,
                'customer_id' => $customer->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'pickup_time' => $validated['pickup_time'] ?? null,
                'whatsapp_number' => $validated['whatsapp_number'] ?? $validated['phone'],
                'delivery_fee' => (float) ($validated['delivery_fee'] ?? 0),
                'straps_fee' => (float) ($validated['straps_fee'] ?? 0),
                'damage_waiver_fee' => (float) ($validated['damage_waiver_fee'] ?? 0),
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ], null);
        } catch (\Exception $e) {
            return redirect()->route('book.form', [
                'trailer_id' => $validated['trailer_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ])->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('book.confirmation', ['booking' => $booking->id])
            ->with('success', 'Your booking request has been submitted.');
    }

    /**
     * Show booking confirmation (thank you page).
     */
    public function confirmation(Booking $booking)
    {
        $booking->load(['trailer', 'customer']);
        $companyName = \App\Models\Setting::get('company_name', config('app.name', 'IronAxle Trailers'));

        return view('book.confirmation', compact('booking', 'companyName'));
    }
}
