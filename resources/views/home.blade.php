@extends('layouts.public')

@section('title', $companyName . ' – Trailer Rentals')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Trailer Rentals</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">View our trailers and check availability for your dates.</p>
    </div>

    <!-- Check availability -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Check availability</h2>
        <form method="GET" action="{{ route('home') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start date</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $startDate) }}"
                    min="{{ date('Y-m-d') }}"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End date</label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $endDate) }}"
                    min="{{ date('Y-m-d') }}"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm">Check availability</button>
            @if($startDate || $endDate)
                <a href="{{ route('home') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 font-medium">Clear</a>
            @endif
        </form>
    </div>

    <!-- Trailers grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($trailers as $trailer)
            <article class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
                @php
                    $primaryPhoto = $trailer->photos->firstWhere('is_primary', true) ?? $trailer->photos->first();
                @endphp
                @if($primaryPhoto && $primaryPhoto->path)
                    <div class="aspect-video bg-gray-200 dark:bg-gray-700">
                        <img src="{{ asset('storage/' . $primaryPhoto->path) }}" alt="{{ $trailer->name }}" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="aspect-video bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                @endif
                <div class="p-5 flex-1 flex flex-col">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $trailer->name }}</h2>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $trailer->type }} @if($trailer->axle) • {{ $trailer->axle }} axle @endif</p>
                    @if($trailer->size_m)
                        <p class="text-sm text-gray-500 dark:text-gray-500">Size: {{ $trailer->size_m }}m</p>
                    @endif
                    @if($trailer->load_capacity_kg)
                        <p class="text-sm text-gray-500 dark:text-gray-500">Load capacity: {{ number_format($trailer->load_capacity_kg) }} kg</p>
                    @endif
                    @if($trailer->description)
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $trailer->description }}</p>
                    @endif
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-3">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-2xl font-bold text-orange-600 dark:text-orange-400">N${{ number_format($trailer->rate_per_day, 2) }}<span class="text-sm font-normal text-gray-500 dark:text-gray-400">/day</span></span>
                            @if(isset($trailer->available_for_dates))
                                @if($trailer->available_for_dates)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Available</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">Booked</span>
                                @endif
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">Select dates to check</span>
                            @endif
                        </div>
                        @if(isset($trailer->available_for_dates) && $trailer->available_for_dates && $startDate && $endDate)
                            <a href="{{ route('book.form', ['trailer_id' => $trailer->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="block w-full text-center bg-orange-600 hover:bg-orange-700 text-white font-medium py-2.5 px-4 rounded-lg">
                                Book now
                            </a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <p class="text-gray-600 dark:text-gray-400">No trailers available at the moment. Check back later.</p>
            </div>
        @endforelse
    </div>

@endsection

@push('scripts')
<script>
    document.getElementById('start_date')?.addEventListener('change', function() {
        var end = document.getElementById('end_date');
        if (end && this.value) end.min = this.value;
    });
</script>
@endpush
