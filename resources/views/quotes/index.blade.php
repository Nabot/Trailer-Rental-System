<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Quotes') }}
            </h2>
            <a href="{{ route('quotes.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">
                Create Quote
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Action Button - PROMINENT -->
            <div class="mb-6 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Quick Actions</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Create a new quote</p>
                    </div>
                    <a href="{{ route('quotes.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-md font-semibold shadow-lg hover:shadow-xl transition-all">
                        ➕ Create Quote
                    </a>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md h-fit">Filter</button>
                        <a href="{{ route('quotes.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md h-fit">Clear</a>
                    </div>
                </form>
            </div>

            <!-- Quotes Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Quote #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Trailer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($quotes as $quote)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('quotes.show', $quote) }}" class="text-orange-600 dark:text-orange-400 hover:underline font-medium">
                                        {{ $quote->quote_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $quote->customer->name ?? ($quote->inquiry->name ?? '-') }}
                                    </div>
                                    @if($quote->inquiry)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        From: {{ $quote->inquiry->inquiry_number }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $quote->trailer->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($quote->start_date && $quote->end_date)
                                    {{ $quote->start_date->format('M d') }} - {{ $quote->end_date->format('M d, Y') }}
                                    @else
                                    -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$quote->status" type="quote" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">
                                        N${{ number_format($quote->total_amount, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('quotes.show', $quote) }}" class="text-orange-600 dark:text-orange-400 hover:text-orange-900 dark:hover:text-orange-300">
                                            View
                                        </a>
                                        @if($quote->status === 'draft')
                                        <a href="{{ route('quotes.edit', $quote) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                            Edit
                                        </a>
                                        @endif
                                        @can('quotes.delete')
                                        <form method="POST" action="{{ route('quotes.destroy', $quote) }}" class="inline" onsubmit="return confirm('Delete this quote?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12">
                                    <x-empty-state 
                                        title="No quotes found"
                                        description="Get started by creating your first quote."
                                        :action="route('quotes.create')"
                                        actionLabel="Create Quote"
                                    >
                                        <x-slot name="icon">
                                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </x-slot>
                                    </x-empty-state>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4">
                    {{ $quotes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
