<?php

if (!function_exists('storage_asset')) {
    /**
     * URL for a file stored on the public disk (e.g. trailer photos).
     * When document root is the app root (PUBLIC_PATH_IS_APP_ROOT) or STORAGE_URL_PREFIX
     * is set, uses the path so the server can find files under storage/app/public.
     */
    function storage_asset(string $path): string
    {
        $prefix = config('app.storage_url_prefix', '');
        if (config('app.public_path_is_app_root')) {
            $base = 'storage/app/public';
        } elseif ($prefix !== '') {
            $base = rtrim($prefix, '/');
        } else {
            $base = 'storage';
        }

        return asset($base . '/' . ltrim($path, '/'));
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol(): string
    {
        return config('app.currency_symbol', 'N$');
    }
}

if (!function_exists('format_money')) {
    function format_money(float $amount, int $decimals = 2): string
    {
        return currency_symbol() . ' ' . number_format($amount, $decimals);
    }
}

if (!function_exists('activity_log')) {
    function activity_log(string $action, ?string $subjectType = null, ?int $subjectId = null, array $properties = []): ?\App\Models\ActivityLog
    {
        return \App\Models\ActivityLog::log($action, $subjectType, $subjectId, $properties);
    }
}
