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
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $appName)</title>
    @if($faviconPath)
    <link rel="icon" type="{{ str_ends_with($faviconPath, '.svg') ? 'image/svg+xml' : 'image/png' }}" href="{{ asset($faviconPath) }}">
    @endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans text-gray-900 dark:text-gray-100 antialiased bg-gray-50 dark:bg-gray-900">
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    @if($logoPath && file_exists(public_path($logoPath)))
                        <img src="{{ asset($logoPath) }}" alt="{{ $appName }}" class="h-10 w-auto" />
                    @else
                        <span class="text-xl font-bold"><span class="text-gray-800 dark:text-gray-200">IRON</span><span class="text-orange-600 dark:text-orange-400">AXLE</span> <span class="text-gray-600 dark:text-gray-400 text-sm font-normal uppercase tracking-wider">Trailers</span></span>
                    @endif
                </a>
                @auth
                <nav class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 font-medium">Dashboard</a>
                </nav>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        @yield('content')
    </main>

    <footer class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
