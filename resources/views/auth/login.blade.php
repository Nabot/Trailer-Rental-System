<x-guest-layout>
    <!-- Page context -->
    <div class="mb-6 text-center">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Sign in to your account') }}</h2>
        <p class="mt-1 flex items-center justify-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
            <svg class="h-4 w-4 text-orange-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
            </svg>
            {{ __('Secure sign-in') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Error summary -->
    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-700 dark:text-red-300 ring-1 ring-red-200 dark:ring-red-800">
            <p class="font-medium">{{ __('Please fix the errors below.') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me + Forgot password row -->
        <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-orange-600 shadow-sm focus:ring-orange-500 dark:focus:ring-orange-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <!-- Submit -->
        <div class="mt-6">
            <x-primary-button
                class="w-full sm:w-auto justify-center disabled:opacity-75 disabled:cursor-not-allowed"
                aria-label="{{ __('Log in to your account') }}"
                x-bind:disabled="loading"
            >
                <span x-show="!loading">{{ __('Log in') }}</span>
                <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Signing in…') }}
                </span>
            </x-primary-button>
        </div>
    </form>

    @if (Route::has('register'))
        <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
            {{ __("Don't have an account?") }}
            <a class="font-medium text-orange-600 dark:text-orange-400 hover:text-orange-500 dark:hover:text-orange-300 focus:outline-none focus:underline focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 rounded" href="{{ route('register') }}">
                {{ __('Register') }}
            </a>
        </p>
    @endif
</x-guest-layout>
