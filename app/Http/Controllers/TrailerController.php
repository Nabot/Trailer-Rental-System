<?php

namespace App\Http\Controllers;

use App\Models\Trailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrailerController extends Controller
{
    public function __construct()
    {
        // Authorization handled in each method
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Trailer::class);
        
        $query = Trailer::query();

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $trailers = $query->with('primaryPhoto')->latest()->paginate(15);

        return view('trailers.index', compact('trailers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Trailer::class);
        return view('trailers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Trailer::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'axle' => 'required|in:Single,Double',
            'size_m' => 'required|numeric|min:0',
            'rate_per_day' => 'required|numeric|min:0',
            'required_deposit' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,maintenance,unavailable',
            'description' => 'nullable|string',
            'registration_number' => 'nullable|string|max:255',
            'colour' => 'nullable|string|max:100',
            'load_capacity_kg' => 'nullable|integer|min:0',
            'trailer_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $trailer = Trailer::create($validated);

        return redirect()->route('trailers.show', $trailer)
            ->with('success', 'Trailer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Trailer $trailer)
    {
        $this->authorize('view', $trailer);
        
        $trailer->load(['photos', 'documents', 'bookings' => function ($query) {
            $query->where('status', '!=', 'cancelled')
                ->orderBy('start_date', 'desc')
                ->limit(10);
        }]);

        return view('trailers.show', compact('trailer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trailer $trailer)
    {
        $this->authorize('update', $trailer);
        return view('trailers.edit', compact('trailer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trailer $trailer)
    {
        $this->authorize('update', $trailer);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'axle' => 'required|in:Single,Double',
            'size_m' => 'required|numeric|min:0',
            'rate_per_day' => 'required|numeric|min:0',
            'required_deposit' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,maintenance,unavailable',
            'description' => 'nullable|string',
            'registration_number' => 'nullable|string|max:255',
            'colour' => 'nullable|string|max:100',
            'load_capacity_kg' => 'nullable|integer|min:0',
            'trailer_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $trailer->update($validated);

        return redirect()->route('trailers.show', $trailer)
            ->with('success', 'Trailer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trailer $trailer)
    {
        $this->authorize('delete', $trailer);
        
        // Check if trailer has active bookings
        $hasActiveBookings = $trailer->bookings()
            ->whereIn('status', ['confirmed', 'active'])
            ->exists();

        if ($hasActiveBookings) {
            return redirect()->back()
                ->with('error', 'Cannot delete trailer with active bookings.');
        }

        $trailer->delete();

        return redirect()->route('trailers.index')
            ->with('success', 'Trailer deleted successfully.');
    }
}
