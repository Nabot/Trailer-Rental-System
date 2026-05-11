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
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Rent trailers in Namibia. Check availability and book online. Reliable trailer hire with clear pricing.')">
    <title>@yield('title', $appName)</title>
    @if($faviconPath)
    <link rel="icon" type="{{ str_ends_with($faviconPath, '.svg') ? 'image/svg+xml' : 'image/png' }}" href="{{ asset($faviconPath) }}">
    @endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="public-site font-sans text-gray-900 dark:text-gray-100 antialiased bg-gray-50 dark:bg-gray-900 min-h-screen overflow-x-hidden">
    <div id="frontend-passcode-protected" class="hidden">
    <a href="#main-content" class="sr-only focus:fixed focus:left-[max(1rem,env(safe-area-inset-left))] focus:top-[max(1rem,env(safe-area-inset-top))] focus:z-50 focus:w-auto focus:h-auto focus:px-4 focus:py-2 focus:m-0 focus:overflow-visible focus:[clip:auto] focus:bg-orange-600 focus:text-white focus:rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">Skip to main content</a>
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm pt-[env(safe-area-inset-top)]">
        <div class="max-w-7xl mx-auto px-[calc(1rem+env(safe-area-inset-left))] pr-[calc(1rem+env(safe-area-inset-right))] sm:pl-[calc(1.5rem+env(safe-area-inset-left))] sm:pr-[calc(1.5rem+env(safe-area-inset-right))] lg:pl-[calc(2rem+env(safe-area-inset-left))] lg:pr-[calc(2rem+env(safe-area-inset-right))]">
            <div class="flex justify-between items-center h-20 sm:h-28 md:h-32 py-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-3 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 min-h-[44px] transition-transform duration-100 active:scale-[0.98]">
                    @if($logoPath && file_exists(public_path($logoPath)))
                        <img src="{{ asset($logoPath) }}" alt="{{ $appName }}" class="h-14 sm:h-24 md:h-32 w-auto max-h-[120px]" />
                    @elseif(file_exists(public_path('images/ironaxle-logo.png')))
                        <img src="{{ asset('images/ironaxle-logo.png') }}" alt="{{ $appName }}" class="h-14 sm:h-24 md:h-32 w-auto max-h-[120px]" />
                    @elseif(file_exists(public_path('images/ironaxle-logo.svg')))
                        <img src="{{ asset('images/ironaxle-logo.svg') }}" alt="{{ $appName }}" class="h-14 sm:h-24 md:h-32 w-auto max-h-[120px]" />
                    @else
                        <span class="text-xl font-bold"><span class="text-gray-800 dark:text-gray-200">IRON</span><span class="text-orange-600 dark:text-orange-400">AXLE</span> <span class="text-gray-600 dark:text-gray-400 text-sm font-normal uppercase tracking-wider">Trailers</span></span>
                    @endif
                </a>
                <nav class="flex items-center gap-2 sm:gap-4">
                    <a href="tel:+264813544045" class="min-h-[44px] inline-flex items-center justify-center px-4 py-2 bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white font-medium rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 transition-transform duration-100 active:scale-[0.98]" aria-label="Call us on 081 354 4045">Call us</a>
                    @auth
                    <a href="{{ route('dashboard') }}" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center px-4 text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 font-medium rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 transition-transform duration-100 active:scale-[0.98]">Dashboard</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <main id="main-content" class="max-w-7xl mx-auto py-6 sm:py-12 pl-[calc(1rem+env(safe-area-inset-left))] pr-[calc(1rem+env(safe-area-inset-right))] pb-[calc(1.5rem+env(safe-area-inset-bottom))] sm:pl-[calc(1.5rem+env(safe-area-inset-left))] sm:pr-[calc(1.5rem+env(safe-area-inset-right))] sm:pb-[calc(3rem+env(safe-area-inset-bottom))] lg:pl-[calc(2rem+env(safe-area-inset-left))] lg:pr-[calc(2rem+env(safe-area-inset-right))]" role="main">
        @yield('content')
    </main>

    <x-toast-container />
    </div>

    <div id="frontend-passcode-lock" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/90 px-4">
        <div class="w-full max-w-sm rounded-xl bg-white p-6 shadow-2xl">
            <h2 class="text-xl font-semibold text-gray-900">Enter Passcode</h2>
            <p class="mt-2 text-sm text-gray-600">This page is protected. Enter the passcode to continue.</p>

            <form id="frontend-passcode-form" class="mt-5 space-y-3">
                <label for="frontend-passcode-input" class="block text-sm font-medium text-gray-700">Passcode</label>
                <input id="frontend-passcode-input" type="password" autocomplete="off" class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500" />
                <p id="frontend-passcode-error" class="hidden text-sm text-red-600">Incorrect passcode. Please try again.</p>
                <button type="submit" class="w-full rounded-lg bg-orange-600 px-4 py-2 text-white font-medium hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">Unlock</button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const PASSCODE = 'Iron';
            const STORAGE_KEY = 'frontend_passcode_unlocked';
            const protectedContent = document.getElementById('frontend-passcode-protected');
            const lockScreen = document.getElementById('frontend-passcode-lock');
            const form = document.getElementById('frontend-passcode-form');
            const input = document.getElementById('frontend-passcode-input');
            const error = document.getElementById('frontend-passcode-error');

            const unlock = () => {
                protectedContent.classList.remove('hidden');
                lockScreen.classList.add('hidden');
            };

            if (localStorage.getItem(STORAGE_KEY) === '1') {
                unlock();
                return;
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                if (input.value.trim() === PASSCODE) {
                    localStorage.setItem(STORAGE_KEY, '1');
                    unlock();
                    return;
                }

                error.classList.remove('hidden');
                input.value = '';
                input.focus();
            });

            input.focus();
        })();
    </script>
    @stack('scripts')
</body>
</html>
