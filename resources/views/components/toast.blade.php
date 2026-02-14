@props(['type' => 'info', 'message' => '', 'duration' => 5000])

<div 
    x-data="{ show: true }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-x-full"
    x-transition:enter-end="opacity-100 transform translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-x-0"
    x-transition:leave-end="opacity-0 transform translate-x-full"
    @if($duration > 0)
    x-init="setTimeout(() => show = false, {{ $duration }})"
    @endif
    class="fixed top-4 right-4 z-50 max-w-sm w-full"
>
    <div class="rounded-lg shadow-lg overflow-hidden
        @if($type === 'success') bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700
        @elseif($type === 'error') bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700
        @elseif($type === 'warning') bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700
        @else bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700
        @endif">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    @if($type === 'success')
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    @elseif($type === 'error')
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    @elseif($type === 'warning')
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    @else
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    @endif
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm font-medium
                        @if($type === 'success') text-green-800 dark:text-green-200
                        @elseif($type === 'error') text-red-800 dark:text-red-200
                        @elseif($type === 'warning') text-yellow-800 dark:text-yellow-200
                        @else text-blue-800 dark:text-blue-200
                        @endif">
                        {{ $message }}
                    </p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button 
                        @click="show = false"
                        class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2
                            @if($type === 'success') text-green-400 hover:text-green-500 focus:ring-green-500
                            @elseif($type === 'error') text-red-400 hover:text-red-500 focus:ring-red-500
                            @elseif($type === 'warning') text-yellow-400 hover:text-yellow-500 focus:ring-yellow-500
                            @else text-blue-400 hover:text-blue-500 focus:ring-blue-500
                            @endif">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
