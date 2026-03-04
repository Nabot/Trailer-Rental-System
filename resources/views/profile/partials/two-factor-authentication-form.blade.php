<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Two-Factor Authentication') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Add an extra layer of security to your account.') }}
        </p>
    </header>
    <div class="mt-6">
        @if(auth()->user()->two_factor_confirmed_at)
            <p class="text-sm text-green-600 dark:text-green-400 font-medium">{{ __('Two-factor authentication is enabled.') }}</p>
        @else
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('Two-factor authentication is not enabled. To enable it, install and configure Laravel Fortify two-factor authentication, or ask your administrator.') }}
            </p>
        @endif
    </div>
</section>
