<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Manage your trailer rentals and bookings</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Spent</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">N${{ number_format($totalSpent, 2) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Upcoming Bookings</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $upcomingBookings->count() }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Bookings</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $bookings->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Active Booking -->
    @if($activeBooking)
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">Active Rental</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-blue-700 dark:text-blue-300">Trailer</p>
                    <p class="font-semibold text-blue-900 dark:text-blue-100">{{ $activeBooking->trailer->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-blue-700 dark:text-blue-300">Return Date</p>
                    <p class="font-semibold text-blue-900 dark:text-blue-100">{{ $activeBooking->end_date->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('bookings.show', $activeBooking) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    View Details
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- My Bookings -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Bookings</h3>
                <a href="{{ route('bookings.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline">New Booking</a>
            </div>
            <div class="space-y-4">
                @forelse($bookings->take(5) as $booking)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <a href="{{ route('bookings.show', $booking) }}" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $booking->booking_number }}
                            </a>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $booking->trailer->name }} • {{ $booking->start_date->format('M d') }} - {{ $booking->end_date->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($booking->status === 'active') bg-blue-100 text-blue-800
                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                            <p class="mt-2 font-semibold text-gray-900 dark:text-gray-100">N${{ number_format($booking->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-8">No bookings yet. <a href="{{ route('bookings.create') }}" class="text-blue-600 hover:underline">Create your first booking</a></p>
                @endforelse
            </div>
            @if($bookings->count() > 5)
            <div class="mt-4 text-center">
                <a href="{{ route('bookings.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">View All Bookings</a>
            </div>
            @endif
        </div>
    </div>
</div>
