@props(['title', 'value', 'icon', 'change' => null, 'changeType' => 'positive', 'href' => null, 'color' => 'blue'])

@php
    $colorClasses = [
        'blue' => ['bg' => 'bg-blue-500', 'light' => 'bg-blue-50 dark:bg-blue-900', 'text' => 'text-blue-600 dark:text-blue-400'],
        'green' => ['bg' => 'bg-green-500', 'light' => 'bg-green-50 dark:bg-green-900', 'text' => 'text-green-600 dark:text-green-400'],
        'yellow' => ['bg' => 'bg-yellow-500', 'light' => 'bg-yellow-50 dark:bg-yellow-900', 'text' => 'text-yellow-600 dark:text-yellow-400'],
        'red' => ['bg' => 'bg-red-500', 'light' => 'bg-red-50 dark:bg-red-900', 'text' => 'text-red-600 dark:text-red-400'],
        'purple' => ['bg' => 'bg-purple-500', 'light' => 'bg-purple-50 dark:bg-purple-900', 'text' => 'text-purple-600 dark:text-purple-400'],
        'indigo' => ['bg' => 'bg-indigo-500', 'light' => 'bg-indigo-50 dark:bg-indigo-900', 'text' => 'text-indigo-600 dark:text-indigo-400'],
    ];
    $colors = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

@if($href)
<a href="{{ $href }}" class="block">
@endif
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 {{ $href ? 'cursor-pointer' : '' }}">
    <div class="p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md {{ $colors['bg'] }} text-white">
                    {!! $icon !!}
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                        {{ $title }}
                    </dt>
                    <dd class="flex items-baseline">
                        <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $value }}
                        </div>
                        @if($change !== null)
                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $changeType === 'positive' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            @if($changeType === 'positive')
                            <svg class="self-center flex-shrink-0 h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            @else
                            <svg class="self-center flex-shrink-0 h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            @endif
                            <span class="sr-only">{{ $changeType === 'positive' ? 'Increased' : 'Decreased' }} by</span>
                            {{ $change }}
                        </div>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@if($href)
</a>
@endif
