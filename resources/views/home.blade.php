@extends('layouts.public')

@section('title', $companyName . ' – Trailer Rentals')

@section('meta_description', 'Rent trailers in Namibia. Check availability and book online. Reliable trailer hire with clear pricing.')

@section('content')
    <!-- Hero -->
    <div class="mb-4 sm:mb-6 text-center">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-gray-100">Trailer Rentals</h1>
        <p class="mt-2 sm:mt-3 text-lg sm:text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto px-1">Trailer hire in Namibia – check availability and book online in a few clicks.</p>
    </div>

    <!-- How it works -->
    <div class="flex flex-wrap sm:flex-nowrap items-stretch justify-center gap-2 sm:gap-0 mb-6 sm:mb-8">
        <a href="#availability-form" class="flex flex-1 min-w-0 sm:min-w-[160px] sm:max-w-[200px] items-center gap-3 p-4 sm:p-5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 no-underline text-inherit focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2" aria-label="Pick your dates – go to date picker">
            <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400 font-bold text-base">1</div>
            <div class="min-w-0">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm sm:text-base">Pick your dates</h3>
                <p class="hidden sm:block text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">Choose start and end date.</p>
            </div>
        </a>
        <div class="hidden sm:flex flex-shrink-0 items-center text-gray-400 dark:text-gray-500 px-1" aria-hidden="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="flex flex-1 min-w-0 sm:min-w-[160px] sm:max-w-[200px] items-center gap-3 p-4 sm:p-5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600">
            <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400 font-bold text-base">2</div>
            <div class="min-w-0">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm sm:text-base">Choose a trailer</h3>
                <p class="hidden sm:block text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">See availability and rates.</p>
            </div>
        </div>
        <div class="hidden sm:flex flex-shrink-0 items-center text-gray-400 dark:text-gray-500 px-1" aria-hidden="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="flex flex-1 min-w-0 sm:min-w-[160px] sm:max-w-[200px] items-center gap-3 p-4 sm:p-5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600">
            <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400 font-bold text-base">3</div>
            <div class="min-w-0">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm sm:text-base">Book online</h3>
                <p class="hidden sm:block text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">Submit details, we confirm.</p>
            </div>
        </div>
    </div>

    <!-- Check availability (above the fold) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6 sm:mb-8">
        <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Check availability</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Select your rental period to see which trailers are available and book.</p>
        <form method="GET" action="{{ route('home') }}" id="availability-form" class="flex flex-col sm:flex-row sm:flex-wrap gap-4 sm:items-end" aria-label="Check trailer availability by date">
            <div class="w-full sm:w-auto flex-1 sm:min-w-[140px]">
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start date</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $startDate) }}"
                    min="{{ date('Y-m-d') }}"
                    class="w-full min-h-[44px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 focus:ring-offset-2 text-base">
            </div>
            <div class="w-full sm:w-auto flex-1 sm:min-w-[140px]">
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End date</label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $endDate) }}"
                    min="{{ date('Y-m-d') }}"
                    class="w-full min-h-[44px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500 focus:ring-offset-2 text-base">
            </div>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:items-center">
                <button type="submit" id="check-availability-btn" class="w-full sm:w-auto min-h-[44px] bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white px-5 py-3 rounded-lg font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-transform duration-100 active:scale-[0.98]">Check availability</button>
                @if($startDate || $endDate)
                    <a href="{{ route('home') }}" class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 font-medium focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 rounded px-3 transition-transform duration-100 active:scale-[0.98]">Clear dates</a>
                @endif
            </div>
        </form>
    </div>

    @if($startDate && $endDate)
    @php
        $availableCount = $trailers->filter(fn($t) => isset($t->available_for_dates) && $t->available_for_dates)->count();
    @endphp
    <div class="sticky top-0 z-10 -mx-4 px-4 py-3 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8 mb-4 sm:mb-6 bg-orange-50 dark:bg-orange-900/20 border-y border-orange-200 dark:border-orange-800 flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center justify-between gap-2 shadow-sm" role="status" aria-live="polite" id="date-summary">
        <span class="font-medium text-gray-900 dark:text-gray-100 text-sm sm:text-base">Rental: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
        <span class="flex flex-wrap items-center gap-2 sm:gap-3">
            @if($trailers->isNotEmpty())
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $availableCount }} {{ $availableCount === 1 ? 'trailer' : 'trailers' }} available</span>
            @endif
            <a href="{{ route('home') }}#availability-form" class="min-h-[44px] inline-flex items-center text-sm font-medium text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 rounded -my-1 py-2 transition-opacity duration-100 active:opacity-70">Change dates</a>
        </span>
    </div>
    @endif

    <!-- Trailers grid -->
    @php
        $trailersHeading = ($startDate && $endDate) ? 'Available trailers' : 'Our trailers';
    @endphp
    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3 sm:mb-4">{{ $trailersHeading }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6" id="trailers-grid">
        @forelse($trailers as $trailer)
            @php
                $primaryPhoto = $trailer->photos->firstWhere('is_primary', true) ?? $trailer->photos->first();
                $days = ($startDate && $endDate) ? max(1, (int) \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate))) : null;
                $estimatedTotal = $days ? $days * $trailer->rate_per_day : null;
                $bookable = isset($trailer->available_for_dates) && $trailer->available_for_dates && $startDate && $endDate;
                $bookUrl = $bookable ? route('book.form', ['trailer_id' => $trailer->id, 'start_date' => $startDate, 'end_date' => $endDate]) : null;
            @endphp
            @if($bookable)
            <a href="{{ $bookUrl }}" class="group block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col no-underline text-inherit transition-shadow duration-150 hover:shadow-md active:scale-[0.995] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900" aria-label="Book {{ $trailer->name }} for selected dates">
            @else
            <article class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
            @endif
                @if($primaryPhoto && $primaryPhoto->image_url)
                    <div class="aspect-video bg-gray-200 dark:bg-gray-700 shrink-0 pointer-events-none" style="aspect-ratio: 16/9;">
                        <img src="{{ $primaryPhoto->image_url }}" alt="{{ $bookable ? '' : $trailer->name }}" width="400" height="225" class="w-full h-full object-cover" loading="lazy" @if($bookable) role="presentation" @endif>
                    </div>
                @else
                    <div class="aspect-video bg-gray-200 dark:bg-gray-700 flex items-center justify-center shrink-0 pointer-events-none" style="aspect-ratio: 16/9;" aria-hidden="true">
                        <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                @endif
                <div class="p-4 sm:p-5 flex-1 flex flex-col">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $trailer->name }}</h2>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $trailer->type }}@if($trailer->axle) · {{ $trailer->axle }} axle @endif</p>
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
                    <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-100 dark:border-gray-700 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-xl sm:text-2xl font-bold text-orange-600 dark:text-orange-400">N${{ number_format($trailer->rate_per_day, 2) }}<span class="text-sm font-normal text-gray-500 dark:text-gray-400">/day</span></span>
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
                        @if($bookable)
                            <span class="min-h-[44px] flex items-center justify-center bg-orange-600 group-hover:bg-orange-700 text-white font-medium py-3 px-4 rounded-lg pointer-events-none">Book now</span>
                        @endif
                    </div>
                </div>
            @if($bookable)
            </a>
            @else
            </article>
            @endif
        @empty
            <div class="col-span-full text-center py-10 sm:py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-4 sm:px-6">
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
