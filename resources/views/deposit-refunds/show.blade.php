<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Deposit Refund #{{ str_pad($depositRefund->id, 6, '0', STR_PAD_LEFT) }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('deposit-refunds.download', $depositRefund) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                    Download PDF
                </a>
                <a href="{{ route('bookings.show', $depositRefund->booking) }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                    Back to Booking
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-2xl font-bold">Deposit Refund Receipt</h3>
                        <p class="text-gray-600">Receipt #{{ str_pad($depositRefund->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <p class="text-2xl font-bold text-amber-600">-N${{ number_format($depositRefund->amount, 2) }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold mb-2">Refund Details</h4>
                        <p><strong>Date:</strong> {{ $depositRefund->refund_date->format('M d, Y') }}</p>
                        <p><strong>Method:</strong> {{ strtoupper($depositRefund->method) }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($depositRefund->status) }}</p>
                        <p><strong>Reference:</strong> {{ $depositRefund->reference_number ?: '-' }}</p>
                        <p><strong>Processed By:</strong> {{ $depositRefund->processedBy?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-2">Customer / Booking</h4>
                        <p><strong>Customer:</strong> {{ $depositRefund->customer->name }}</p>
                        <p><strong>Booking:</strong>
                            <a href="{{ route('bookings.show', $depositRefund->booking) }}" class="text-blue-600 hover:underline">
                                {{ $depositRefund->booking->booking_number }}
                            </a>
                        </p>
                        @if($depositRefund->invoice)
                        <p><strong>Invoice:</strong>
                            <a href="{{ route('invoices.show', $depositRefund->invoice) }}" class="text-blue-600 hover:underline">
                                {{ $depositRefund->invoice->invoice_number }}
                            </a>
                        </p>
                        @endif
                    </div>
                </div>

                @if($depositRefund->notes)
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded">
                    <p class="text-sm"><strong>Notes:</strong> {{ $depositRefund->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
