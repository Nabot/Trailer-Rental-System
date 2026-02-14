<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Payment Receipt #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('payments.download', $payment) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                    Download PDF
                </a>
                @php
                    $customer = $payment->booking?->customer ?? $payment->invoice?->customer;
                    $whatsappNumber = $payment->booking?->whatsapp_number ?? $customer?->phone;
                @endphp
                @if($whatsappNumber)
                <form method="POST" action="{{ route('payments.send-whatsapp', $payment) }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                        📱 Send via WhatsApp
                    </button>
                </form>
                @endif
                <a href="{{ route('payments.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
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
                    <!-- Payment Details -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-2xl font-bold">Payment Receipt</h3>
                                <p class="text-gray-600">Receipt #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-green-600">
                                    N${{ number_format($payment->amount, 2) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <h4 class="font-semibold mb-2">Payment Details:</h4>
                                <p class="text-gray-700"><strong>Date:</strong> {{ $payment->payment_date->format('M d, Y') }}</p>
                                <p class="text-gray-700"><strong>Method:</strong> {{ ucfirst($payment->method) }}</p>
                                @if($payment->reference_number)
                                <p class="text-gray-700"><strong>Reference:</strong> {{ $payment->reference_number }}</p>
                                @endif
                                @if($payment->recordedBy)
                                <p class="text-gray-700"><strong>Recorded By:</strong> {{ $payment->recordedBy->name }}</p>
                                @endif
                            </div>
                            <div>
                                @php
                                    $customer = $payment->booking?->customer ?? $payment->invoice?->customer;
                                @endphp
                                @if($customer)
                                <h4 class="font-semibold mb-2">Customer:</h4>
                                <p class="text-gray-700">{{ $customer->name }}</p>
                                @if($customer->email)
                                <p class="text-gray-600">{{ $customer->email }}</p>
                                @endif
                                @if($customer->phone)
                                <p class="text-gray-600">{{ $customer->phone }}</p>
                                @endif
                                @endif
                            </div>
                        </div>

                        @if($payment->booking)
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded">
                            <h4 class="font-semibold mb-2">Related Booking:</h4>
                            <p class="text-gray-700">
                                <a href="{{ route('bookings.show', $payment->booking) }}" class="text-blue-600 hover:underline">
                                    {{ $payment->booking->booking_number }}
                                </a>
                                - {{ $payment->booking->trailer->name }}
                            </p>
                        </div>
                        @endif

                        @if($payment->invoice)
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded">
                            <h4 class="font-semibold mb-2">Related Invoice:</h4>
                            <p class="text-gray-700">
                                <a href="{{ route('invoices.show', $payment->invoice) }}" class="text-blue-600 hover:underline">
                                    {{ $payment->invoice->invoice_number }}
                                </a>
                            </p>
                        </div>
                        @endif

                        @if($payment->notes)
                        <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded">
                            <p class="text-sm text-gray-700"><strong>Notes:</strong> {{ $payment->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                        <div class="space-y-2">
                            @can('payments.edit')
                            <a href="{{ route('payments.edit', $payment) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded-md">
                                Edit Payment
                            </a>
                            @endcan
                            @can('payments.delete')
                            <form method="POST" action="{{ route('payments.destroy', $payment) }}" onsubmit="return confirm('Are you sure you want to delete this payment?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="block w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                                    Delete Payment
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
