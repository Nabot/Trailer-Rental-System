<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'IronAxle Rentals') }}</title>
        
        <!-- Favicon -->
        @if(file_exists(public_path('images/ironaxle-logo.png')))
        <link rel="icon" type="image/png" href="{{ asset('images/ironaxle-logo.png') }}">
        @elseif(file_exists(public_path('images/ironaxle-logo.svg')))
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/ironaxle-logo.svg') }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
            <!-- Logo Section -->
            <div class="mb-8 text-center">
                <a href="/" class="inline-block">
                    @if(file_exists(public_path('images/ironaxle-logo.png')))
                        <img src="{{ asset('images/ironaxle-logo.png') }}" alt="IronAxle Rentals" class="h-16 sm:h-24 w-auto mx-auto mb-2" />
                    @elseif(file_exists(public_path('images/ironaxle-logo.svg')))
                        <img src="{{ asset('images/ironaxle-logo.svg') }}" alt="IronAxle Rentals" class="h-16 sm:h-24 w-auto mx-auto mb-2" />
                    @else
                        <div class="flex flex-col items-center space-y-2 mb-4">
                            <div class="text-4xl font-bold">
                                <span class="text-gray-800 dark:text-gray-200">IRON</span>
                                <span class="text-orange-600 dark:text-orange-400">AXLE</span>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 font-medium uppercase tracking-wider">RENTALS</div>
                        </div>
                    @endif
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ config('app.name', 'IronAxle Rentals') }}</h1>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-gray-800 shadow-2xl sm:rounded-lg border border-gray-200 dark:border-gray-700 ring-1 ring-gray-200/50 dark:ring-gray-700/50">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
