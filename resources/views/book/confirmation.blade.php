@extends('layouts.public')

@section('title', 'Booking confirmed – ' . $companyName)

@section('content')
    <nav class="mb-6 text-sm" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-1 text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('home') }}" class="hover:text-orange-600 dark:hover:text-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 rounded">Home</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-gray-900 dark:text-gray-100 font-medium">Booking confirmed</li>
        </ol>
    </nav>

    <div class="max-w-2xl mx-auto text-center min-w-0">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 lg:p-8">
            <div class="w-14 h-14 sm:w-16 sm:h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">Booking request received</h1>
            <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-400">Thank you. We have received your booking request and will be in touch shortly.</p>

            <div class="mt-6 sm:mt-8 p-4 sm:p-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-left">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Save your booking reference</p>
                <p class="text-lg sm:text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1 break-all" id="booking-reference">{{ $booking->booking_number }}</p>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Quote this number when you call or in email.</p>
                <dl class="mt-6 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">Trailer</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $booking->trailer->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">Dates</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $booking->start_date->format('d M Y') }} – {{ $booking->end_date->format('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600 dark:text-gray-400">Total</dt>
                        <dd class="text-gray-900 dark:text-gray-100">N${{ number_format($booking->total_amount, 2) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="mt-6 sm:mt-8 p-4 sm:p-6 bg-orange-50 dark:bg-orange-900/20 rounded-lg text-left">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">What happens next?</h2>
                <ul class="mt-3 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                    <li class="flex gap-2"><span class="text-orange-600 dark:text-orange-400 font-bold">1.</span> We’ll contact you at <strong>{{ $booking->customer->email }}</strong> or <strong>{{ $booking->customer->phone }}</strong> within 24 hours.</li>
                    <li class="flex gap-2"><span class="text-orange-600 dark:text-orange-400 font-bold">2.</span> We’ll confirm your booking and arrange payment and collection.</li>
                    <li class="flex gap-2"><span class="text-orange-600 dark:text-orange-400 font-bold">3.</span> You collect the trailer on the agreed date and return it by the end date.</li>
                </ul>
            </div>

            <a href="{{ route('home') }}" class="inline-block mt-8 min-h-[44px] flex items-center justify-center bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                Back to trailers
            </a>
        </div>
    </div>
@endsection
