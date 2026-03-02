<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $appName = \App\Models\Setting::get('company_name', config('app.name', 'IronAxle Trailers'));
            $logoPath = \App\Models\Setting::get('company_logo', '');
            $faviconPath = null;
            if ($logoPath && file_exists(public_path($logoPath))) {
                $faviconPath = $logoPath;
            } elseif (file_exists(public_path('images/ironaxle-logo.png'))) {
                $faviconPath = 'images/ironaxle-logo.png';
            } elseif (file_exists(public_path('images/ironaxle-logo.svg'))) {
                $faviconPath = 'images/ironaxle-logo.svg';
            }
        @endphp
        <title>{{ $appName }}</title>
        
        <!-- Favicon -->
        @if($faviconPath)
        <link rel="icon" type="{{ str_ends_with($faviconPath, '.svg') ? 'image/svg+xml' : 'image/png' }}" href="{{ asset($faviconPath) }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        
        <!-- Toast Notifications Container -->
        <x-toast-container />
        
        @stack('scripts')
    </body>
</html>
