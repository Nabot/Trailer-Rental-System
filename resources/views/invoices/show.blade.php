<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Invoice: {{ $invoice->invoice_number }}
            </h2>
            <div class="flex gap-2">
                @can('invoices.update')
                @if($invoice->status === 'pending')
                <a href="{{ route('invoices.edit', $invoice) }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">
                    Edit Invoice
                </a>
                @endif
                @endcan
                <a href="{{ route('invoices.download', $invoice) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                    Download PDF
                </a>
                @if($invoice->booking?->whatsapp_number || $invoice->customer->phone)
                <form method="POST" action="{{ route('invoices.send-whatsapp', $invoice) }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">
                        📱 Send via WhatsApp
                    </button>
                </form>
                @endif
                <a href="{{ route('invoices.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Invoice Details -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-2xl font-bold">Invoice</h3>
                                <p class="text-gray-600">{{ $invoice->invoice_number }}</p>
                            </div>
                            <div class="text-right">
                                <x-status-badge :status="$invoice->status" type="invoice" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <h4 class="font-semibold mb-2">Bill To:</h4>
                                <p class="text-gray-700">{{ $invoice->customer->name }}</p>
                                @if($invoice->customer->email)
                                <p class="text-gray-600">{{ $invoice->customer->email }}</p>
                                @endif
                                @if($invoice->customer->phone)
                                <p class="text-gray-600">{{ $invoice->customer->phone }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-gray-600"><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('M d, Y') }}</p>
                                <p class="text-gray-600"><strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}</p>
                                @if($invoice->booking)
                                <p class="text-gray-600 mt-2">
                                    <strong>Booking:</strong> 
                                    <a href="{{ route('bookings.show', $invoice->booking) }}" class="text-blue-600 hover:underline">
                                        {{ $invoice->booking->booking_number }}
                                    </a>
                                </p>
                                @endif
                            </div>
                        </div>

                        <!-- Invoice Items -->
                        <table class="w-full mb-6">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Description</th>
                                    <th class="text-right py-2">Quantity</th>
                                    <th class="text-right py-2">Unit Price</th>
                                    <th class="text-right py-2">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
                                <tr class="border-b">
                                    <td class="py-2">{{ $item->description }}</td>
                                    <td class="text-right py-2">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="text-right py-2">N${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-right py-2">N${{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right py-2 font-semibold">Subtotal:</td>
                                    <td class="text-right py-2 font-semibold">N${{ number_format($invoice->subtotal, 2) }}</td>
                                </tr>
                                @if($invoice->tax > 0)
                                <tr>
                                    <td colspan="3" class="text-right py-2">Tax:</td>
                                    <td class="text-right py-2">N${{ number_format($invoice->tax, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="border-t-2">
                                    <td colspan="3" class="text-right py-2 font-bold text-lg">Total:</td>
                                    <td class="text-right py-2 font-bold text-lg">N${{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right py-2">Paid:</td>
                                    <td class="text-right py-2 text-green-600">N${{ number_format($invoice->paid_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right py-2 font-semibold">Balance:</td>
                                    <td class="text-right py-2 font-semibold {{ $invoice->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        N${{ number_format($invoice->balance, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        @if($invoice->notes)
                        <div class="mt-4 p-4 bg-gray-50 rounded">
                            <p class="text-sm text-gray-700"><strong>Notes:</strong> {{ $invoice->notes }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Payments -->
                    @if($invoice->payments->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Payment History</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2">Date</th>
                                        <th class="text-left py-2">Method</th>
                                        <th class="text-left py-2">Reference</th>
                                        <th class="text-right py-2">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->payments as $payment)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td class="py-2">{{ ucfirst($payment->method) }}</td>
                                        <td class="py-2">{{ $payment->reference_number ?? '-' }}</td>
                                        <td class="text-right py-2">N${{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                        <div class="space-y-2">
                            @can('payments.create')
                            <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded-md">
                                Record Payment
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
