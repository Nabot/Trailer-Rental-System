<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Customer;
use App\Models\Trailer;
use App\Models\User;
use App\Models\InquiryActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('inquiries.view');
        try {
            $query = Inquiry::with(['customer', 'assignedTo', 'createdBy']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by source
        if ($request->has('source') && $request->source !== '') {
            $query->where('source', $request->source);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned to (including "My leads")
        if ($request->has('assigned_to') && $request->assigned_to !== '') {
            if ($request->assigned_to === 'my') {
                $query->where('assigned_to', auth()->id());
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('inquiry_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

            $inquiries = $query->latest()->paginate(20);
            $users = User::all();
            $statuses = ['new', 'contacted', 'quoted', 'follow_up', 'converted', 'lost', 'on_hold'];
            $sources = ['website', 'phone', 'referral', 'walk_in', 'social_media', 'google_ads', 'other'];
            $priorities = ['high', 'medium', 'low'];

            return view('inquiries.index', compact('inquiries', 'users', 'statuses', 'sources', 'priorities'));
        } catch (\Exception $e) {
            \Log::error('Inquiry index error: ' . $e->getMessage());
            return view('inquiries.index', [
                'inquiries' => collect([])->paginate(20),
                'users' => collect([]),
                'statuses' => ['new', 'contacted', 'quoted', 'follow_up', 'converted', 'lost', 'on_hold'],
                'sources' => ['website', 'phone', 'referral', 'walk_in', 'social_media', 'google_ads', 'other'],
                'priorities' => ['high', 'medium', 'low'],
            ])->with('error', 'Error loading inquiries: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('inquiries.create');
        $customers = Customer::all();
        $trailers = Trailer::where('status', 'available')->get();
        $users = User::all();
        
        $customerId = $request->get('customer_id');
        $customer = $customerId ? Customer::find($customerId) : null;

        return view('inquiries.create', compact('customers', 'trailers', 'users', 'customer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('inquiries.create');
        $validated = $request->validate([
            'source' => 'required|in:website,phone,referral,walk_in,social_media,google_ads,other',
            'status' => 'nullable|in:new,contacted,quoted,follow_up,converted,lost,on_hold',
            'priority' => 'nullable|in:high,medium,low',
            'customer_id' => 'nullable|exists:customers,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'preferred_start_date' => 'nullable|date',
            'preferred_end_date' => 'nullable|date|after_or_equal:preferred_start_date',
            'trailer_interests' => 'nullable|array',
            'trailer_interests.*' => 'exists:trailers,id',
            'rental_purpose' => 'nullable|string|max:1000',
            'budget_range' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'assigned_to' => 'nullable|exists:users,id',
            'create_anyway' => 'nullable|boolean',
        ]);

        // Duplicate check: same email or phone on another lead (unless "create anyway" was checked)
        if (!$request->boolean('create_anyway') && (!empty($validated['email']) || !empty($validated['phone']))) {
            $existing = Inquiry::where(function ($q) use ($validated) {
                if (!empty($validated['email'])) {
                    $q->orWhere('email', $validated['email']);
                }
                if (!empty($validated['phone'])) {
                    $q->orWhere('phone', $validated['phone']);
                }
            })->first();
            if ($existing) {
                return redirect()->route('inquiries.create')
                    ->withInput()
                    ->with('existing_inquiry_id', $existing->id)
                    ->with('existing_inquiry_number', $existing->inquiry_number)
                    ->with('existing_inquiry_name', $existing->name)
                    ->with('warning', 'A lead with this email or phone already exists.');
            }
        }

        $inquiry = Inquiry::create([
            ...$validated,
            'status' => $validated['status'] ?? 'new',
            'priority' => $validated['priority'] ?? 'medium',
            'created_by' => auth()->id(),
            'assigned_to' => !empty($validated['assigned_to']) ? $validated['assigned_to'] : auth()->id(),
        ]);

        // Create initial activity
        InquiryActivity::create([
            'inquiry_id' => $inquiry->id,
            'type' => 'note',
            'subject' => 'Inquiry Created',
            'description' => 'Inquiry was created',
            'created_by' => auth()->id(),
            'completed_at' => now(),
        ]);

        return redirect()->route('inquiries.show', $inquiry)
            ->with('success', 'Inquiry created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inquiry $inquiry)
    {
        $this->authorize('inquiries.view');
        $inquiry->load(['customer', 'assignedTo', 'createdBy', 'quotes.trailer', 'activities.createdBy', 'convertedToBooking']);
        $trailers = Trailer::where('status', 'available')->get();
        $customers = Customer::all();
        $users = User::all();

        return view('inquiries.show', compact('inquiry', 'trailers', 'customers', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inquiry $inquiry)
    {
        $this->authorize('inquiries.edit');
        $customers = Customer::all();
        $trailers = Trailer::where('status', 'available')->get();
        $users = User::all();

        return view('inquiries.edit', compact('inquiry', 'customers', 'trailers', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inquiry $inquiry)
    {
        $this->authorize('inquiries.edit');
        $validated = $request->validate([
            'source' => 'required|in:website,phone,referral,walk_in,social_media,google_ads,other',
            'status' => 'required|in:new,contacted,quoted,follow_up,converted,lost,on_hold',
            'priority' => 'required|in:high,medium,low',
            'customer_id' => 'nullable|exists:customers,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'preferred_start_date' => 'nullable|date',
            'preferred_end_date' => 'nullable|date|after_or_equal:preferred_start_date',
            'trailer_interests' => 'nullable|array',
            'trailer_interests.*' => 'exists:trailers,id',
            'rental_purpose' => 'nullable|string|max:1000',
            'budget_range' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'lost_reason' => 'nullable|string|max:500',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $inquiry->update($validated);

        return redirect()->route('inquiries.show', $inquiry)
            ->with('success', 'Inquiry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inquiry $inquiry)
    {
        $this->authorize('inquiries.delete');
        if ($inquiry->status === 'converted') {
            return redirect()->back()
                ->with('error', 'Cannot delete converted inquiries.');
        }

        $inquiry->delete();

        return redirect()->route('inquiries.index')
            ->with('success', 'Inquiry deleted successfully.');
    }

    /**
     * Quick-update status from list (and optional lost_reason when status = lost)
     */
    public function updateStatus(Request $request, Inquiry $inquiry)
    {
        $this->authorize('inquiries.edit');
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,quoted,follow_up,converted,lost,on_hold',
            'lost_reason' => 'nullable|string|max:500',
        ]);
        $inquiry->update($validated);
        return redirect()->back()
            ->with('success', 'Lead status updated.');
    }

    /**
     * Add activity to inquiry
     */
    public function addActivity(Request $request, Inquiry $inquiry)
    {
        $this->authorize('inquiries.edit');
        $validated = $request->validate([
            'type' => 'required|in:call,email,whatsapp,meeting,note,follow_up',
            'subject' => 'nullable|string|max:255',
            'description' => 'required|string|max:2000',
            'scheduled_at' => 'nullable|date',
        ]);

        $activity = InquiryActivity::create([
            'inquiry_id' => $inquiry->id,
            ...$validated,
            'created_by' => auth()->id(),
            'completed_at' => $validated['type'] !== 'follow_up' ? now() : null,
        ]);

        return redirect()->back()
            ->with('success', 'Activity added successfully.');
    }

    /**
     * Mark an activity (e.g. follow-up) as complete
     */
    public function completeActivity(Inquiry $inquiry, int $activity)
    {
        $this->authorize('inquiries.edit');
        $activityModel = InquiryActivity::where('inquiry_id', $inquiry->id)->findOrFail($activity);
        $activityModel->markAsCompleted();
        return redirect()->back()
            ->with('success', 'Activity marked as complete.');
    }

    /**
     * Convert inquiry to customer
     */
    public function convertToCustomer(Inquiry $inquiry)
    {
        $this->authorize('inquiries.edit');
        // Check if customer already exists
        $customer = Customer::where('email', $inquiry->email)
            ->orWhere('phone', $inquiry->phone)
            ->first();

        if (!$customer) {
            $customer = Customer::create([
                'name' => $inquiry->name,
                'email' => $inquiry->email,
                'phone' => $inquiry->phone,
                'notes' => "Converted from inquiry {$inquiry->inquiry_number}",
            ]);
        }

        $inquiry->update(['customer_id' => $customer->id]);

        InquiryActivity::create([
            'inquiry_id' => $inquiry->id,
            'type' => 'note',
            'subject' => 'Converted to Customer',
            'description' => "Inquiry converted to customer: {$customer->name}",
            'created_by' => auth()->id(),
            'completed_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Inquiry converted to customer successfully.');
    }
}
