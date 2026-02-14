@props(['type' => 'text', 'class' => ''])

@if($type === 'text')
<div class="animate-pulse bg-gray-200 dark:bg-gray-700 rounded {{ $class }}"></div>
@elseif($type === 'card')
<div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg shadow p-6 {{ $class }}">
    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-4"></div>
    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
</div>
@elseif($type === 'table')
<div class="animate-pulse bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden {{ $class }}">
    <div class="p-6 space-y-4">
        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
    </div>
</div>
@elseif($type === 'avatar')
<div class="animate-pulse bg-gray-200 dark:bg-gray-700 rounded-full {{ $class }}"></div>
@elseif($type === 'button')
<div class="animate-pulse bg-gray-200 dark:bg-gray-700 rounded h-10 {{ $class }}"></div>
@endif
