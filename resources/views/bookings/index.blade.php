<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Bookings') }}
            </h2>
            @can('bookings.create')
            <a href="{{ route('bookings.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                New Booking
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" class="flex gap-4 flex-wrap">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search booking number..." class="rounded-md border-gray-300">
                    <select name="status" class="rounded-md border-gray-300">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @can('bookings.view')
                    <select name="trailer_id" class="rounded-md border-gray-300">
                        <option value="">All Trailers</option>
                        @foreach($trailers as $trailer)
                        <option value="{{ $trailer->id }}" {{ request('trailer_id') == $trailer->id ? 'selected' : '' }}>
                            {{ $trailer->name }}
                        </option>
                        @endforeach
                    </select>
                    <select name="customer_id" class="rounded-md border-gray-300">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                        @endforeach
                    </select>
                    @endcan
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">Filter</button>
                    <a href="{{ route('bookings.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">Clear</a>
                </form>
            </div>

            <!-- Bookings Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Booking #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Trailer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Days</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Balance</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($bookings as $booking)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                        {{ $booking->booking_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $booking->trailer->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $booking->customer->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>{{ $booking->start_date->format('M d, Y') }}</div>
                                    <div class="text-sm text-gray-500">to {{ $booking->end_date->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $booking->total_days }} days</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$booking->status" type="booking" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">N${{ number_format($booking->total_amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="{{ $booking->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        N${{ number_format($booking->balance, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 dark:text-blue-400 hover:underline">View</a>
                                        @can('delete', $booking)
                                        <form method="POST" action="{{ route('bookings.destroy', $booking) }}" class="inline" onsubmit="return confirm('Delete this booking?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12">
                                    <x-empty-state 
                                        title="No bookings found"
                                        description="Get started by creating your first booking."
                                        :action="route('bookings.create')"
                                        actionLabel="Create Booking"
                                    >
                                        <x-slot name="icon">
                                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </x-slot>
                                    </x-empty-state>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
