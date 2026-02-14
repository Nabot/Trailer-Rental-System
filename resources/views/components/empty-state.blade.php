@props(['title', 'description' => null, 'icon' => null, 'action' => null, 'actionLabel' => null])

<div class="text-center py-12">
    @if($icon)
    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
        {!! $icon !!}
    </div>
    @endif
    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $title }}</h3>
    @if($description)
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
    @endif
    @if($action && $actionLabel)
    <div class="mt-6">
        <a href="{{ $action }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
            {{ $actionLabel }}
        </a>
    </div>
    @endif
    {{ $slot }}
</div>
