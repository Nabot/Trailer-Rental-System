@extends('layouts.public')

@section('title', 'Book ' . $trailer->name . ' – ' . $companyName)

@section('content')
    @php
        $trailersUrl = route('home', ['start_date' => $startDate, 'end_date' => $endDate]);
    @endphp
    <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
        <a href="{{ $trailersUrl }}" class="inline-flex items-center gap-2 min-h-[44px] px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 transition-colors">
            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to trailers
        </a>
        <a href="{{ route('home') }}" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2">
            Home
        </a>
    </div>
    <nav class="mb-4 sm:mb-6 text-sm" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-1 text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('home') }}" class="min-h-[44px] inline-flex items-center -my-2 py-2 hover:text-orange-600 dark:hover:text-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 rounded">Home</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-gray-900 dark:text-gray-100 font-medium truncate max-w-[180px] sm:max-w-none">Book {{ $trailer->name }}</li>
        </ol>
    </nav>
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100">Book online</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400 text-sm sm:text-base">Complete your details to request this trailer for the selected dates.</p>
        <p class="mt-3 py-3 px-4 bg-gray-100 dark:bg-gray-700/50 rounded-lg text-sm font-medium text-gray-800 dark:text-gray-200">You're booking <strong>{{ $trailer->name }}</strong> for {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <div class="lg:col-span-2 min-w-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">

                <form method="POST" action="{{ route('book.store') }}">
                    @csrf
                    <input type="hidden" name="trailer_id" value="{{ $trailer->id }}">
                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" value="{{ $endDate }}">

                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Your details</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full name <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full min-h-[44px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500 focus:ring-offset-0" autocomplete="name">
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" inputmode="email" class="w-full min-h-[44px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500 focus:ring-offset-0" autocomplete="email">
                            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone <span class="text-red-500">*</span></label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required inputmode="tel" class="w-full min-h-[44px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500 focus:ring-offset-0" placeholder="e.g. +264 81 234 5678" autocomplete="tel">
                            @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="text" id="address" name="address" value="{{ old('address') }}" class="w-full min-h-[44px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500 focus:ring-offset-0" placeholder="e.g. Windhoek, Namibia">
                            @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="id_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID / Passport number <span class="text-red-500">*</span></label>
                            <input type="text" id="id_number" name="id_number" value="{{ old('id_number') }}" required class="w-full min-h-[44px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500 focus:ring-offset-0" placeholder="e.g. ID or passport number">
                            @error('id_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="driver_licence" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Driver licence number <span class="text-red-500">*</span></label>
                            <input type="text" id="driver_licence" name="driver_licence" value="{{ old('driver_licence') }}" required class="w-full min-h-[44px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500 focus:ring-offset-0" placeholder="e.g. licence number">
                            @error('driver_licence')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Rental details</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="pickup_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preferred pickup time <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="time" id="pickup_time" name="pickup_time" value="{{ old('pickup_time') }}" class="w-full min-h-[44px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500 focus:ring-offset-0">
                            @error('pickup_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">WhatsApp number <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="tel" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', old('phone')) }}" inputmode="tel" placeholder="e.g. +264 81 123 4567" class="w-full min-h-[44px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500 focus:ring-offset-0" autocomplete="tel">
                            @error('whatsapp_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                            <textarea id="notes" name="notes" rows="2" class="w-full min-h-[44px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500 focus:ring-offset-0" placeholder="Special requests, delivery address, etc.">{{ old('notes') }}</textarea>
                            @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <button type="submit" class="w-full sm:w-auto min-h-[44px] bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white font-medium py-3 px-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-transform duration-100 active:scale-[0.99]">
                        Submit booking request
                    </button>
                </form>
            </div>
        </div>

        <div class="min-w-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 lg:sticky lg:top-4">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Booking summary</h3>
                @php
                    $primaryPhoto = $trailer->photos->firstWhere('is_primary', true) ?? $trailer->photos->first();
                @endphp
                @if($primaryPhoto && $primaryPhoto->image_url)
                    <div class="aspect-video rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700 mb-4" style="aspect-ratio: 16/9;">
                        <img src="{{ $primaryPhoto->image_url }}" alt="{{ $trailer->name }}" width="320" height="180" class="w-full h-full object-cover" loading="lazy">
                    </div>
                @endif
                <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $trailer->name }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $trailer->type }} @if($trailer->axle) • {{ $trailer->axle }} axle @endif</p>
                <p class="mt-3 py-2 px-3 bg-gray-100 dark:bg-gray-700/50 rounded-lg text-sm font-medium text-gray-800 dark:text-gray-200">
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </p>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">Start date</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">End date</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</dd>
                    </div>
                    @php
                        // 24-hour-period model: same-day rental = 1 day (minimum),
                        // each additional calendar day = 1 more day.
                        $days = max(1, (int) \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)));
                        $total = $days * $trailer->rate_per_day;
                    @endphp
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">Duration</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $days }} day{{ $days !== 1 ? 's' : '' }}</dd>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                        <dt class="text-gray-600 dark:text-gray-400">Rate</dt>
                        <dd class="text-gray-900 dark:text-gray-100">N${{ number_format($trailer->rate_per_day, 2) }}/day</dd>
                    </div>
                    <div class="flex justify-between font-medium">
                        <dt class="text-gray-900 dark:text-gray-100">Estimated total</dt>
                        <dd class="text-orange-600 dark:text-orange-400">N${{ number_format($total, 2) }}</dd>
                    </div>
                </dl>
                <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">Deposit and any delivery/optional fees will be confirmed after your request is received.</p>
            </div>
        </div>
    </div>
@endsection
