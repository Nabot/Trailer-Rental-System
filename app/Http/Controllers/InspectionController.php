<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Inspection;
use App\Models\DamageItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Inspection::with(['booking.trailer', 'booking.customer', 'inspectedBy']);

        if ($request->has('booking_id')) {
            $query->where('booking_id', $request->booking_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $inspections = $query->latest('inspected_at')->paginate(20);

        return view('inspections.index', compact('inspections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $bookingId = $request->get('booking_id');
        $type = $request->get('type', 'pickup');

        if (!$bookingId) {
            return redirect()->back()->with('error', 'Booking ID is required.');
        }

        $booking = Booking::with(['trailer', 'customer'])->findOrFail($bookingId);

        // Check if inspection already exists
        $existingInspection = Inspection::where('booking_id', $bookingId)
            ->where('type', $type)
            ->first();

        if ($existingInspection) {
            return redirect()->route('inspections.show', $existingInspection)
                ->with('info', 'Inspection already exists. Viewing existing inspection.');
        }

        // Default checklist items
        $defaultChecklist = [
            'exterior' => [
                'body_condition' => null,
                'lights_working' => null,
                'tires_condition' => null,
                'hitch_condition' => null,
            ],
            'interior' => [
                'floor_condition' => null,
                'walls_condition' => null,
                'door_working' => null,
            ],
            'safety' => [
                'brakes_working' => null,
                'safety_chains' => null,
                'registration_valid' => null,
            ],
        ];

        return view('inspections.create', compact('booking', 'type', 'defaultChecklist'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'type' => 'required|in:pickup,return',
            'checklist' => 'required|array',
            'notes' => 'nullable|string|max:1000',
            'condition_notes' => 'nullable|string|max:1000',
            'is_damaged' => 'boolean',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120',
            'damage_items' => 'nullable|array',
            'damage_items.*.description' => 'required_with:damage_items|string|max:500',
            'damage_items.*.location' => 'nullable|string|max:255',
            'damage_items.*.estimated_cost' => 'required_with:damage_items|numeric|min:0',
            'damage_items.*.severity' => 'nullable|in:minor,moderate,major',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $inspection = Inspection::create([
                'booking_id' => $validated['booking_id'],
                'type' => $validated['type'],
                'checklist' => $validated['checklist'],
                'notes' => $validated['notes'] ?? null,
                'condition_notes' => $validated['condition_notes'] ?? null,
                'is_damaged' => $validated['is_damaged'] ?? false,
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
            if (!empty($validated['damage_items'])) {
                foreach ($validated['damage_items'] as $damageData) {
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
                $booking = $inspection->booking;
                if ($inspection->total_damage_cost > $booking->required_deposit) {
                    $this->createDamageInvoice($booking, $inspection);
                }
            }

            // Update booking status based on inspection type
            $booking = $inspection->booking;
            if ($validated['type'] === 'pickup' && $booking->status === 'confirmed') {
                // Inspection done, ready to start rental
            } elseif ($validated['type'] === 'return' && $booking->status === 'active') {
                // Return inspection done, mark as returned
                $booking->transitionTo('returned');
            }

            return redirect()->route('inspections.show', $inspection)
                ->with('success', ucfirst($validated['type']) . ' inspection completed successfully.');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Inspection $inspection)
    {
        $inspection->load([
            'booking.trailer',
            'booking.customer',
            'inspectedBy',
            'photos',
            'damageItems.photos'
        ]);

        return view('inspections.show', compact('inspection'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inspection $inspection)
    {
        $inspection->load(['booking.trailer', 'booking.customer', 'photos', 'damageItems.photos']);

        return view('inspections.edit', compact('inspection'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inspection $inspection)
    {
        $validated = $request->validate([
            'checklist' => 'required|array',
            'notes' => 'nullable|string|max:1000',
            'condition_notes' => 'nullable|string|max:1000',
            'is_damaged' => 'boolean',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:5120',
        ]);

        $inspection->update($validated);

        // Handle new photo uploads
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

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Inspection updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inspection $inspection)
    {
        // Delete photos
        foreach ($inspection->photos as $photo) {
            Storage::disk($photo->disk)->delete($photo->path);
        }

        foreach ($inspection->damageItems as $damageItem) {
            foreach ($damageItem->photos as $photo) {
                Storage::disk($photo->disk)->delete($photo->path);
            }
        }

        $inspection->delete();

        return redirect()->route('inspections.index')
            ->with('success', 'Inspection deleted successfully.');
    }

    /**
     * Create an invoice for damages exceeding deposit.
     */
    private function createDamageInvoice(Booking $booking, Inspection $inspection)
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
}
