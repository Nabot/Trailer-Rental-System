<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Payments') }}
            </h2>
            @can('payments.create')
            <a href="{{ route('payments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                Record Payment
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" class="flex gap-4 flex-wrap">
                    <input type="text" name="booking_id" value="{{ request('booking_id') }}" placeholder="Booking ID" class="rounded-md border-gray-300">
                    <select name="method" class="rounded-md border-gray-300">
                        <option value="">All Methods</option>
                        <option value="eft" {{ request('method') === 'eft' ? 'selected' : '' }}>EFT</option>
                        <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="card" {{ request('method') === 'card' ? 'selected' : '' }}>Card</option>
                    </select>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" placeholder="Start Date" class="rounded-md border-gray-300">
                    <input type="date" name="end_date" value="{{ request('end_date') }}" placeholder="End Date" class="rounded-md border-gray-300">
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">Filter</button>
                    <a href="{{ route('payments.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">Clear</a>
                </form>
            </div>

            <!-- Payments Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Booking</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Reference</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Amount</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($payments as $payment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($payment->booking)
                                    <a href="{{ route('bookings.show', $payment->booking_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline" onclick="event.stopPropagation()">
                                        {{ $payment->booking->booking_number }}
                                    </a>
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $customer = $payment->booking?->customer ?? $payment->invoice?->customer;
                                    @endphp
                                    {{ $customer?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($payment->method) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $payment->reference_number ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right font-semibold">N${{ number_format($payment->amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('payments.show', $payment) }}" class="text-orange-600 dark:text-orange-400 hover:underline">View</a>
                                        @can('payments.edit')
                                        <form method="POST" action="{{ route('payments.destroy', $payment) }}" class="inline" onsubmit="return confirm('Delete this payment?');">
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
                                        title="No payments found"
                                        description="Payments will appear here once they are recorded."
                                        :action="route('payments.create')"
                                        actionLabel="Record Payment"
                                    >
                                        <x-slot name="icon">
                                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
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
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
