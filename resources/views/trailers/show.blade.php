<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $trailer->name }}
            </h2>
            <div class="flex gap-2">
                @can('trailers.edit')
                <a href="{{ route('trailers.edit', $trailer) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                    Edit
                </a>
                @endcan
                <a href="{{ route('trailers.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Details</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Type</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $trailer->type }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Axle</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $trailer->axle }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Size</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $trailer->size_m }}m</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Rate per Day</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">N${{ number_format($trailer->rate_per_day, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($trailer->status === 'available') bg-green-100 text-green-800
                                        @elseif($trailer->status === 'maintenance') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($trailer->status) }}
                                    </span>
                                </dd>
                            </div>
                            @if($trailer->registration_number)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Registration</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $trailer->registration_number }}</dd>
                            </div>
                            @endif
                        </dl>
                        @if($trailer->description)
                        <div class="mt-4">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $trailer->description }}</dd>
                        </div>
                        @endif
                    </div>

                    <!-- Recent Bookings -->
                    @if($trailer->bookings->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Recent Bookings</h3>
                        <div class="space-y-3">
                            @foreach($trailer->bookings as $booking)
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                                <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $booking->booking_number }}
                                </a>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $booking->customer->name }} • {{ $booking->start_date->format('M d') }} - {{ $booking->end_date->format('M d, Y') }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                        <div class="space-y-2">
                            @can('bookings.create')
                            <a href="{{ route('bookings.create', ['trailer_id' => $trailer->id]) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded-md">
                                Create Booking
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
