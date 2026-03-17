@extends('layouts.public')

@section('title', $companyName . ' – Trailer Rentals')

@section('meta_description', 'Rent trailers in Namibia. Check availability and book online. Reliable trailer hire with clear pricing.')

@section('content')
    <!-- Hero -->
    <div class="mb-6 text-center">
        <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 dark:text-gray-100">Trailer Rentals</h1>
        <p class="mt-3 text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Trailer hire in Namibia – check availability and book online in a few clicks.</p>
    </div>

    <!-- Check availability (above the fold) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Check availability</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Select your rental period to see which trailers are available and book.</p>
        <form method="GET" action="{{ route('home') }}" id="availability-form" class="flex flex-wrap gap-4 items-end" aria-label="Check trailer availability by date">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start date</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $startDate) }}"
                    min="{{ date('Y-m-d') }}"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 focus:ring-offset-2">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End date</label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $endDate) }}"
                    min="{{ date('Y-m-d') }}"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 focus:ring-offset-2">
            </div>
            <button type="submit" id="check-availability-btn" class="min-h-[44px] bg-orange-600 hover:bg-orange-700 text-white px-5 py-3 rounded-lg font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">Check availability</button>
            @if($startDate || $endDate)
                <a href="{{ route('home') }}" class="min-h-[44px] inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 font-medium focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 rounded px-3">Clear dates</a>
            @endif
        </form>
    </div>

    @if($startDate && $endDate)
    @php
        $availableCount = $trailers->filter(fn($t) => isset($t->available_for_dates) && $t->available_for_dates)->count();
    @endphp
    <div class="sticky top-0 z-10 -mx-4 px-4 py-3 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8 mb-6 bg-orange-50 dark:bg-orange-900/20 border-y border-orange-200 dark:border-orange-800 flex flex-wrap items-center justify-between gap-2 shadow-sm" role="status" aria-live="polite" id="date-summary">
        <span class="font-medium text-gray-900 dark:text-gray-100">Rental: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
        <span class="flex items-center gap-3">
            @if($trailers->isNotEmpty())
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $availableCount }} {{ $availableCount === 1 ? 'trailer' : 'trailers' }} available</span>
            @endif
            <a href="{{ route('home') }}#availability-form" class="text-sm font-medium text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 rounded">Change dates</a>
        </span>
    </div>
    @endif

    <!-- How it works (horizontal steps) -->
    <div class="flex flex-wrap sm:flex-nowrap items-stretch justify-center gap-2 sm:gap-4 mb-8">
        <div class="flex flex-1 min-w-0 sm:min-w-[140px] items-center gap-3 sm:flex-col sm:gap-2 sm:text-center p-3 sm:p-4 rounded-lg bg-orange-50/50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-800/50">
            <div class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 font-bold text-base sm:text-lg">1</div>
            <div class="min-w-0">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm sm:text-base">Pick your dates</h3>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-600 dark:text-gray-400">Choose start and end date.</p>
            </div>
        </div>
        <div class="hidden sm:flex flex-shrink-0 items-center text-orange-300 dark:text-orange-600" aria-hidden="true">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="flex flex-1 min-w-0 sm:min-w-[140px] items-center gap-3 sm:flex-col sm:gap-2 sm:text-center p-3 sm:p-4 rounded-lg bg-orange-50/50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-800/50">
            <div class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 font-bold text-base sm:text-lg">2</div>
            <div class="min-w-0">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm sm:text-base">Choose a trailer</h3>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-600 dark:text-gray-400">See availability and rates.</p>
            </div>
        </div>
        <div class="hidden sm:flex flex-shrink-0 items-center text-orange-300 dark:text-orange-600" aria-hidden="true">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="flex flex-1 min-w-0 sm:min-w-[140px] items-center gap-3 sm:flex-col sm:gap-2 sm:text-center p-3 sm:p-4 rounded-lg bg-orange-50/50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-800/50">
            <div class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 font-bold text-base sm:text-lg">3</div>
            <div class="min-w-0">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm sm:text-base">Book online</h3>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-600 dark:text-gray-400">Submit details, we confirm.</p>
            </div>
        </div>
    </div>

    <!-- Why us -->
    <div class="flex flex-wrap justify-center gap-6 mb-10 text-sm text-gray-600 dark:text-gray-400">
        <span class="inline-flex items-center gap-2"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Easy online booking</span>
        <span class="inline-flex items-center gap-2"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Clear pricing</span>
        <span class="inline-flex items-center gap-2"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Well-maintained trailers</span>
    </div>

    <!-- Trailers grid -->
    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
        @if($startDate && $endDate)
            Available trailers
        @else
            Our trailers
            @if($minRatePerDay !== null)
                <span class="font-normal text-gray-500 dark:text-gray-400"> — Rates from N${{ number_format($minRatePerDay, 2) }}/day</span>
            @endif
        @endif
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="trailers-grid">
        @forelse($trailers as $trailer)
            @php
                $primaryPhoto = $trailer->photos->firstWhere('is_primary', true) ?? $trailer->photos->first();
                $days = ($startDate && $endDate) ? \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 : null;
                $estimatedTotal = $days ? $days * $trailer->rate_per_day : null;
            @endphp
            <article class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
                @if($primaryPhoto && $primaryPhoto->path)
                    <div class="aspect-video bg-gray-200 dark:bg-gray-700 shrink-0" style="aspect-ratio: 16/9;">
                        <img src="{{ storage_asset($primaryPhoto->path) }}" alt="{{ $trailer->name }}" width="400" height="225" class="w-full h-full object-cover" loading="lazy">
                    </div>
                @else
                    <div class="aspect-video bg-gray-200 dark:bg-gray-700 flex items-center justify-center shrink-0" style="aspect-ratio: 16/9;">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                @endif
                <div class="p-5 flex-1 flex flex-col">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $trailer->name }}</h2>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $trailer->type }}@if($trailer->axle) · {{ $trailer->axle }} axle@endif</p>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @if($trailer->size_m)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ $trailer->size_m }}m</span>
                        @endif
                        @if($trailer->axle)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ $trailer->axle }} axle</span>
                        @endif
                        @if($trailer->load_capacity_kg)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ number_format($trailer->load_capacity_kg) }} kg</span>
                        @endif
                    </div>
                    @if($trailer->description)
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $trailer->description }}</p>
                    @endif
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-2xl font-bold text-orange-600 dark:text-orange-400">N${{ number_format($trailer->rate_per_day, 2) }}<span class="text-sm font-normal text-gray-500 dark:text-gray-400">/day</span></span>
                            @if(isset($trailer->available_for_dates))
                                @if($trailer->available_for_dates)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Available</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">Booked</span>
                                @endif
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">From N${{ number_format($trailer->rate_per_day, 2) }}/day</span>
                            @endif
                        </div>
                        @if($estimatedTotal !== null)
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $days }} day{{ $days !== 1 ? 's' : '' }} · <span class="font-medium text-gray-900 dark:text-gray-100">N${{ number_format($estimatedTotal, 2) }} total</span></p>
                        @endif
                        @if(isset($trailer->available_for_dates) && $trailer->available_for_dates && $startDate && $endDate)
                            <a href="{{ route('book.form', ['trailer_id' => $trailer->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="min-h-[44px] flex items-center justify-center bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                                Book now
                            </a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-6">
                <p class="text-gray-600 dark:text-gray-400">No trailers available at the moment. Check back later or get in touch to arrange a rental.</p>
                @if($companyPhone || $companyEmail)
                    <div class="mt-6 flex flex-wrap justify-center gap-6 text-sm">
                        @if($companyPhone)
                            <a href="tel:{{ preg_replace('/\s+/', '', $companyPhone) }}" class="inline-flex items-center gap-2 text-orange-600 dark:text-orange-400 font-medium hover:underline">{{ $companyPhone }}</a>
                        @endif
                        @if($companyEmail)
                            <a href="mailto:{{ $companyEmail }}" class="inline-flex items-center gap-2 text-orange-600 dark:text-orange-400 font-medium hover:underline">{{ $companyEmail }}</a>
                        @endif
                    </div>
                @endif
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
    document.getElementById('availability-form')?.addEventListener('submit', function() {
        var btn = document.getElementById('check-availability-btn');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Checking availability…';
        }
    });
</script>
@endpush
