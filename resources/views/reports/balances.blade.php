<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Outstanding Balances') }}
            </h2>
            <a href="{{ route('reports.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Total Outstanding</h3>
                    <p class="text-3xl font-bold text-red-600">N${{ number_format($totalOutstanding, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Overdue Amount</h3>
                    <p class="text-3xl font-bold text-red-800">N${{ number_format($overdueAmount, 2) }}</p>
                </div>
            </div>

            <!-- By customer summary -->
            @if(isset($byCustomer) && $byCustomer->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Outstanding by customer</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-400">Customer</th>
                                <th class="px-4 py-2 text-right text-sm font-medium text-gray-600 dark:text-gray-400">Invoices</th>
                                <th class="px-4 py-2 text-right text-sm font-medium text-gray-600 dark:text-gray-400">Balance</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-400">Oldest due</th>
                                <th class="px-4 py-2 text-right text-sm font-medium text-gray-600 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byCustomer as $row)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-gray-100">{{ $row['customer']->name }}</td>
                                <td class="px-4 py-2 text-right">{{ $row['invoice_count'] }}</td>
                                <td class="px-4 py-2 text-right font-semibold text-red-600">N${{ number_format($row['balance'], 2) }}</td>
                                <td class="px-4 py-2">{{ $row['oldest_due'] ? $row['oldest_due']->format('M d, Y') : '—' }}</td>
                                <td class="px-4 py-2 text-right">
                                    <a href="{{ route('customers.show', $row['customer']) }}" class="text-blue-600 dark:text-blue-400 hover:underline">View customer</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Invoices Table -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Invoice #</th>
                                <th class="px-4 py-2 text-left">Customer</th>
                                <th class="px-4 py-2 text-left">Due Date</th>
                                <th class="px-4 py-2 text-right">Amount</th>
                                <th class="px-4 py-2 text-right">Balance</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                            <tr class="{{ $invoice->due_date->isPast() ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                                <td class="px-4 py-2">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-2">{{ $invoice->customer->name }}</td>
                                <td class="px-4 py-2 {{ $invoice->due_date->isPast() ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $invoice->due_date->format('M d, Y') }}
                                    @if($invoice->due_date->isPast())
                                    <span class="text-xs">(Overdue)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right">N${{ number_format($invoice->total_amount, 2) }}</td>
                                <td class="px-4 py-2 text-right font-semibold text-red-600">N${{ number_format($invoice->balance, 2) }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 dark:text-blue-400 hover:underline">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-4 text-center text-gray-500">No outstanding invoices</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
