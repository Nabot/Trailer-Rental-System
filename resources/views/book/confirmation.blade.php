@extends('layouts.public')

@section('title', 'Booking confirmed – ' . $companyName)

@section('content')
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Booking request received</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Thank you. We have received your booking request and will be in touch shortly.</p>

            <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-left">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Booking reference</p>
                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1">{{ $booking->booking_number }}</p>
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

            <p class="mt-6 text-sm text-gray-600 dark:text-gray-400">We will contact you at <strong>{{ $booking->customer->email }}</strong> or <strong>{{ $booking->customer->phone }}</strong> to confirm your booking and arrange payment and collection.</p>

            <a href="{{ route('home') }}" class="inline-block mt-8 bg-orange-600 hover:bg-orange-700 text-white font-medium py-2.5 px-6 rounded-lg">
                Back to trailers
            </a>
        </div>
    </div>
@endsection
