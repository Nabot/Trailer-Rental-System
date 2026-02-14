<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Trailers') }}
            </h2>
            @can('trailers.create')
            <a href="{{ route('trailers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                Add Trailer
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" class="flex gap-4 flex-wrap">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="rounded-md border-gray-300">
                    <select name="status" class="rounded-md border-gray-300">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="unavailable" {{ request('status') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">Filter</button>
                    <a href="{{ route('trailers.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">Clear</a>
                </form>
            </div>

            <!-- Trailers Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($trailers as $trailer)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                <a href="{{ route('trailers.show', $trailer) }}" class="hover:underline">
                                    {{ $trailer->name }}
                                </a>
                            </h3>
                            <x-status-badge :status="$trailer->status" type="trailer" />
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-2">{{ $trailer->type }} • {{ $trailer->axle }} Axle</p>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Size: {{ $trailer->size_m }}m</p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">N${{ number_format($trailer->rate_per_day, 2) }}/day</span>
                            <a href="{{ route('trailers.show', $trailer) }}" class="text-blue-600 dark:text-blue-400 hover:underline">View Details →</a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full">
                    <x-empty-state 
                        title="No trailers found"
                        description="Get started by adding your first trailer to the system."
                        :action="route('trailers.create')"
                        actionLabel="Add Trailer"
                    >
                        <x-slot name="icon">
                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </x-slot>
                    </x-empty-state>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $trailers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
