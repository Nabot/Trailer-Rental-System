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
                            @if($trailer->colour)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Colour</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $trailer->colour }}</dd>
                            </div>
                            @endif
                            @if($trailer->load_capacity_kg)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Load Capacity</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ number_format($trailer->load_capacity_kg) }} kg</dd>
                            </div>
                            @endif
                            @if($trailer->trailer_value)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Trailer Value</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">N${{ number_format($trailer->trailer_value, 2) }}</dd>
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

                    <!-- Photos (for public view) -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Photos (shown on public trailer listing)</h3>
                        @can('update', $trailer)
                        <form action="{{ route('trailers.upload-photo', $trailer) }}" method="POST" enctype="multipart/form-data" class="mb-6">
                            @csrf
                            <div class="flex flex-wrap items-end gap-4">
                                <div class="min-w-0">
                                    <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Add photo</label>
                                    <input type="file" name="photo" id="photo" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 dark:file:bg-orange-900/30 dark:file:text-orange-400 hover:file:bg-orange-100 dark:hover:file:bg-orange-900/50">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">JPEG, PNG, GIF or WebP. Max 5 MB.</p>
                                </div>
                                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md text-sm font-medium">Upload</button>
                            </div>
                        </form>
                        @endcan
                        @if($trailer->photos->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($trailer->photos as $p)
                            <div class="relative group rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600">
                                <img src="{{ Storage::disk($p->disk ?? 'public')->url($p->path) }}" alt="Trailer photo" class="w-full aspect-square object-cover">
                                @if($p->is_primary)
                                    <span class="absolute top-2 left-2 px-2 py-0.5 bg-green-600 text-white text-xs font-medium rounded">Primary</span>
                                @endif
                                @can('update', $trailer)
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                    @if(!$p->is_primary)
                                    <form action="{{ route('trailers.set-primary-photo', [$trailer, $p]) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-white text-gray-800 px-3 py-1.5 rounded text-sm font-medium hover:bg-gray-100">Set primary</button>
                                    </form>
                                    @endif
                                    <form action="{{ route('trailers.destroy-photo', [$trailer, $p]) }}" method="POST" class="inline" onsubmit="return confirm('Remove this photo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded text-sm font-medium hover:bg-red-700">Remove</button>
                                    </form>
                                </div>
                                @endcan
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500 dark:text-gray-400 text-sm">No photos yet. Add a photo to display it on the public trailer listing.</p>
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
