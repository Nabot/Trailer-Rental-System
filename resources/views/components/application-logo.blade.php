@php
    $logoPath = \App\Models\Setting::get('company_logo', '');
    $companyName = \App\Models\Setting::get('company_name', config('app.name', 'IronAxle Trailers'));
@endphp
@if($logoPath && file_exists(public_path($logoPath)))
    <img src="{{ asset($logoPath) }}" alt="{{ $companyName }}" {{ $attributes->merge(['class' => 'h-9 w-auto']) }} />
@elseif(file_exists(public_path('images/ironaxle-logo.png')))
    <img src="{{ asset('images/ironaxle-logo.png') }}" alt="{{ $companyName }}" {{ $attributes->merge(['class' => 'h-9 w-auto']) }} />
@elseif(file_exists(public_path('images/ironaxle-logo.svg')))
    <img src="{{ asset('images/ironaxle-logo.svg') }}" alt="{{ $companyName }}" {{ $attributes->merge(['class' => 'h-9 w-auto']) }} />
@else
    <!-- Fallback: Text logo until image is uploaded -->
    <div {{ $attributes->merge(['class' => 'flex items-center space-x-2']) }}>
        <span class="text-xl font-bold">
            <span class="text-gray-700 dark:text-gray-300">IRON</span>
            <span class="text-orange-600 dark:text-orange-400">AXLE</span>
        </span>
        <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">RENTALS</span>
    </div>
@endif
