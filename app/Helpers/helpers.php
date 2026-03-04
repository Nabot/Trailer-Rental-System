<?php

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
