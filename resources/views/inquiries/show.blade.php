<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Lead: {{ $inquiry->inquiry_number }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('inquiries.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                    Back
                </a>
                <a href="{{ route('inquiries.edit', $inquiry) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded">
                {{ session('error') }}
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Inquiry Details -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Lead Details</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Inquiry Number</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100 font-semibold">{{ $inquiry->inquiry_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <x-status-badge :status="$inquiry->status" type="inquiry" />
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Source</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $inquiry->source)) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Priority</dt>
                                <dd class="mt-1">
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        {{ $inquiry->priority === 'high' ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' : '' }}
                                        {{ $inquiry->priority === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' : '' }}
                                        {{ $inquiry->priority === 'low' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : '' }}
                                    ">
                                        {{ ucfirst($inquiry->priority) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $inquiry->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $inquiry->phone ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $inquiry->email ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">WhatsApp</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $inquiry->whatsapp_number ?? '-' }}</dd>
                            </div>
                            @if($inquiry->preferred_start_date)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Preferred Start Date</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $inquiry->preferred_start_date->format('M d, Y') }}</dd>
                            </div>
                            @endif
                            @if($inquiry->preferred_end_date)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Preferred End Date</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $inquiry->preferred_end_date->format('M d, Y') }}</dd>
                            </div>
                            @endif
                            @if($inquiry->customer)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Linked Customer</dt>
                                <dd class="mt-1">
                                    <a href="{{ route('customers.show', $inquiry->customer) }}" class="text-orange-600 dark:text-orange-400 hover:underline">
                                        {{ $inquiry->customer->name }}
                                    </a>
                                </dd>
                            </div>
                            @endif
                            @if($inquiry->assignedTo)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned To</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $inquiry->assignedTo->name }}</dd>
                            </div>
                            @endif
                        </dl>
                        @if($inquiry->trailer_interests && count($inquiry->trailer_interests) > 0)
                        <div class="mt-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Interested Trailers</dt>
                            <dd class="mt-1">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($inquiry->trailer_interests as $trailerId)
                                        @php $trailer = $trailers->find($trailerId); @endphp
                                        @if($trailer)
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm">
                                            {{ $trailer->name }}
                                        </span>
                                        @endif
                                    @endforeach
                                </div>
                            </dd>
                        </div>
                        @endif
                        @if($inquiry->rental_purpose)
                        <div class="mt-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rental Purpose</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $inquiry->rental_purpose }}</dd>
                        </div>
                        @endif
                        @if($inquiry->budget_range)
                        <div class="mt-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Budget Range</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $inquiry->budget_range }}</dd>
                        </div>
                        @endif
                        @if($inquiry->notes)
                        <div class="mt-4">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Notes</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $inquiry->notes }}</dd>
                        </div>
                        @endif
                    </div>

                    <!-- Quotes -->
                    @if($inquiry->quotes->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Quotes</h3>
                        <div class="space-y-4">
                            @foreach($inquiry->quotes as $quote)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <a href="{{ route('quotes.show', $quote) }}" class="text-orange-600 dark:text-orange-400 hover:underline font-semibold">
                                            {{ $quote->quote_number }}
                                        </a>
                                        <div class="mt-1">
                                            <x-status-badge :status="$quote->status" type="quote" />
                                        </div>
                                        @if($quote->trailer)
                                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $quote->trailer->name }} - {{ $quote->start_date->format('M d') }} to {{ $quote->end_date->format('M d, Y') }}
                                        </div>
                                        <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            Total: N${{ number_format($quote->total_amount, 2) }}
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        @if($quote->status === 'draft')
                                        <a href="{{ route('quotes.edit', $quote) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Edit</a>
                                        @endif
                                        <a href="{{ route('quotes.show', $quote) }}" class="text-orange-600 dark:text-orange-400 hover:underline text-sm">View</a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Activity Timeline -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Activity Timeline</h3>
                        <div class="space-y-4">
                            @forelse($inquiry->activities as $activity)
                            <div class="border-l-4 border-orange-500 pl-4 pb-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">
                                            {{ ucfirst($activity->type) }}
                                            @if($activity->subject)
                                            : {{ $activity->subject }}
                                            @endif
                                        </div>
                                        @if($activity->description)
                                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $activity->description }}
                                        </div>
                                        @endif
                                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                            {{ $activity->createdBy->name }} - {{ $activity->created_at->format('M d, Y H:i') }}
                                        </div>
                                    </div>
                                    @if($activity->scheduled_at && !$activity->isCompleted())
                                    <span class="text-xs px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded">
                                        Scheduled: {{ $activity->scheduled_at->format('M d, Y') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 dark:text-gray-400">No activities yet.</p>
                            @endforelse
                        </div>

                        <!-- Add Activity Form -->
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold mb-3">Add Activity</h4>
                            <form method="POST" action="{{ route('inquiries.add-activity', $inquiry) }}">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                                        <select name="type" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600" required>
                                            <option value="call">Call</option>
                                            <option value="email">Email</option>
                                            <option value="whatsapp">WhatsApp</option>
                                            <option value="meeting">Meeting</option>
                                            <option value="note">Note</option>
                                            <option value="follow_up">Follow-up</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Scheduled At (for follow-ups)</label>
                                        <input type="datetime-local" name="scheduled_at" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                                        <input type="text" name="subject" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                        <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600" required></textarea>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">
                                        Add Activity
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Actions -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                        <div class="space-y-2">
                            @if(!$inquiry->customer_id)
                            <form method="POST" action="{{ route('inquiries.convert-to-customer', $inquiry) }}" class="inline-block w-full">
                                @csrf
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                                    Convert to Customer
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('quotes.create', ['inquiry_id' => $inquiry->id]) }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md text-center text-sm">
                                Create Quote
                            </a>
                            @if($inquiry->convertedToBooking)
                            <div class="p-3 bg-green-50 dark:bg-green-900 rounded">
                                <div class="text-sm font-semibold text-green-800 dark:text-green-200">Converted</div>
                                <a href="{{ route('bookings.show', $inquiry->convertedToBooking) }}" class="text-sm text-green-600 dark:text-green-400 hover:underline">
                                    View Booking: {{ $inquiry->convertedToBooking->booking_number }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Information</h3>
                        <dl class="space-y-2 text-sm">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Created</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $inquiry->created_at->format('M d, Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Created By</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $inquiry->createdBy->name }}</dd>
                            </div>
                            @if($inquiry->converted_at)
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Converted</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $inquiry->converted_at->format('M d, Y H:i') }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
