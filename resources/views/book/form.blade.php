@extends('layouts.public')

@section('title', 'Book ' . $trailer->name . ' – ' . $companyName)

@section('content')
    <div class="mb-8">
        <a href="{{ route('home') }}" class="text-orange-600 dark:text-orange-400 hover:underline font-medium">&larr; Back to trailers</a>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">Book online</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Complete your details to request this trailer for the selected dates.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 text-red-700 dark:text-red-300 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('book.store') }}">
                    @csrf
                    <input type="hidden" name="trailer_id" value="{{ $trailer->id }}">
                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" value="{{ $endDate }}">

                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Your details</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full name <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone <span class="text-red-500">*</span></label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                            <input type="text" id="address" name="address" value="{{ old('address') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="id_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID / Passport number</label>
                            <input type="text" id="id_number" name="id_number" value="{{ old('id_number') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('id_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="driver_licence" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Driver licence number</label>
                            <input type="text" id="driver_licence" name="driver_licence" value="{{ old('driver_licence') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('driver_licence')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Rental details</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="pickup_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preferred pickup time</label>
                            <input type="time" id="pickup_time" name="pickup_time" value="{{ old('pickup_time') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('pickup_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">WhatsApp number</label>
                            <input type="text" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', old('phone')) }}" placeholder="e.g. +264811234567" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('whatsapp_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <textarea id="notes" name="notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-orange-500 focus:ring-orange-500">{{ old('notes') }}</textarea>
                            @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <button type="submit" class="w-full sm:w-auto bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-8 rounded-lg">
                        Submit booking request
                    </button>
                </form>
            </div>
        </div>

        <div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-4">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Booking summary</h3>
                <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $trailer->name }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $trailer->type }} @if($trailer->axle) • {{ $trailer->axle }} axle @endif</p>
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
                        $days = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
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
