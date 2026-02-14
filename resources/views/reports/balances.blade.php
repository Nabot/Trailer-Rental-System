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
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Total Outstanding</h3>
                    <p class="text-3xl font-bold text-red-600">N${{ number_format($totalOutstanding, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Overdue Amount</h3>
                    <p class="text-3xl font-bold text-red-800">N${{ number_format($overdueAmount, 2) }}</p>
                </div>
            </div>

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
                            <tr class="{{ $invoice->due_date->isPast() ? 'bg-red-50' : '' }}">
                                <td class="px-4 py-2">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:underline">
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
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:underline">View</a>
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
