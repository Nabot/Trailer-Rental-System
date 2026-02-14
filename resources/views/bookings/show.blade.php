<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Booking: {{ $booking->booking_number }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('bookings.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                    Back
                </a>
                @can('bookings.edit')
                @if(in_array($booking->status, ['draft', 'pending']))
                <a href="{{ route('bookings.edit', $booking) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                    Edit
                </a>
                @endif
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Booking Details -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Booking Details</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Booking Number</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100 font-semibold">{{ $booking->booking_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <x-status-badge :status="$booking->status" type="booking" />
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Trailer</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $booking->trailer->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Customer</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $booking->customer->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $booking->start_date->format('M d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">End Date</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $booking->end_date->format('M d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Days</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $booking->total_days }} days</dd>
                            </div>
                            @if($booking->pickup_time)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pickup Time</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($booking->pickup_time)->format('H:i') }}</dd>
                            </div>
                            @endif
                        </dl>
                        @if($booking->notes)
                        <div class="mt-4">
                            <dt class="text-sm font-medium text-gray-500">Notes</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $booking->notes }}</dd>
                        </div>
                        @endif
                    </div>

                    <!-- Pricing -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Pricing</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">Rental Cost ({{ $booking->total_days }} days × N${{ number_format($booking->rate_per_day, 2) }})</dt>
                                <dd class="font-semibold">N${{ number_format($booking->rental_cost, 2) }}</dd>
                            </div>
                            @if($booking->delivery_fee > 0)
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">Delivery Fee</dt>
                                <dd>N${{ number_format($booking->delivery_fee, 2) }}</dd>
                            </div>
                            @endif
                            @if($booking->straps_fee > 0)
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">Straps Fee</dt>
                                <dd>N${{ number_format($booking->straps_fee, 2) }}</dd>
                            </div>
                            @endif
                            @if($booking->damage_waiver_fee > 0)
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">Damage Waiver</dt>
                                <dd>N${{ number_format($booking->damage_waiver_fee, 2) }}</dd>
                            </div>
                            @endif
                            <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                <dt class="text-lg font-semibold">Total Amount</dt>
                                <dd class="text-lg font-bold">N${{ number_format($booking->total_amount, 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">Paid Amount</dt>
                                <dd>N${{ number_format($booking->paid_amount, 2) }}</dd>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                <dt class="text-lg font-semibold {{ $booking->balance > 0 ? 'text-red-600' : 'text-green-600' }}">Balance</dt>
                                <dd class="text-lg font-bold {{ $booking->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    N${{ number_format($booking->balance, 2) }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Inspections -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Inspections</h3>
                            @can('inspections.create')
                            @if(!$booking->pickupInspection && in_array($booking->status, ['pending', 'confirmed', 'active']))
                            <button type="button" onclick="window.openPickupInspectionModal()" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                Complete Pickup Inspection
                            </button>
                            @endif
                            @if(!$booking->returnInspection && in_array($booking->status, ['active', 'returned']))
                            <button type="button" onclick="window.openReturnInspectionModal()" class="text-orange-600 dark:text-orange-400 hover:underline text-sm">
                                Complete Return Inspection
                            </button>
                            @endif
                            @endcan
                        </div>
                        <div class="space-y-4">
                            <!-- Pickup Inspection -->
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-purple-600 dark:text-purple-400 mb-2">Pickup Inspection</h4>
                                        @if($booking->pickupInspection)
                                        <div class="space-y-1 text-sm">
                                            <p class="text-gray-600 dark:text-gray-400">
                                                <span class="font-medium">Status:</span> 
                                                <span class="text-green-600 dark:text-green-400">Completed</span>
                                            </p>
                                            <p class="text-gray-600 dark:text-gray-400">
                                                <span class="font-medium">Date:</span> 
                                                {{ $booking->pickupInspection->inspected_at->format('M d, Y H:i') }}
                                            </p>
                                            @if($booking->pickupInspection->inspectedBy)
                                            <p class="text-gray-600 dark:text-gray-400">
                                                <span class="font-medium">Inspected By:</span> 
                                                {{ $booking->pickupInspection->inspectedBy->name }}
                                            </p>
                                            @endif
                                            @if($booking->pickupInspection->is_damaged)
                                            <p class="text-red-600 dark:text-red-400 font-medium">⚠️ Damage Reported</p>
                                            @endif
                                        </div>
                                        @else
                                        <div class="space-y-1 text-sm">
                                            <p class="text-yellow-600 dark:text-yellow-400">
                                                <span class="font-medium">Status:</span> 
                                                <span>Pending</span>
                                            </p>
                                            @if(in_array($booking->status, ['pending', 'confirmed', 'active']))
                                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-2">
                                                Click the button above or use the action buttons in the sidebar to complete the pickup inspection.
                                            </p>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    <div>
                                        @if($booking->pickupInspection)
                                        <a href="{{ route('inspections.show', $booking->pickupInspection) }}" class="bg-purple-100 hover:bg-purple-200 text-purple-800 px-3 py-1 rounded-md text-sm">
                                            View Details
                                        </a>
                                        @else
                                        <span class="px-3 py-1 rounded-md text-sm bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            Not Started
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Return Inspection -->
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-orange-600 dark:text-orange-400 mb-2">Return Inspection</h4>
                                        @if($booking->returnInspection)
                                        <div class="space-y-1 text-sm">
                                            <p class="text-gray-600 dark:text-gray-400">
                                                <span class="font-medium">Status:</span> 
                                                <span class="text-green-600 dark:text-green-400">Completed</span>
                                            </p>
                                            <p class="text-gray-600 dark:text-gray-400">
                                                <span class="font-medium">Date:</span> 
                                                {{ $booking->returnInspection->inspected_at->format('M d, Y H:i') }}
                                            </p>
                                            @if($booking->returnInspection->inspectedBy)
                                            <p class="text-gray-600 dark:text-gray-400">
                                                <span class="font-medium">Inspected By:</span> 
                                                {{ $booking->returnInspection->inspectedBy->name }}
                                            </p>
                                            @endif
                                            @if($booking->returnInspection->is_damaged)
                                            <p class="text-red-600 dark:text-red-400 font-medium">
                                                ⚠️ Damage Reported
                                                @if($booking->returnInspection->total_damage_cost > 0)
                                                - Total Cost: N${{ number_format($booking->returnInspection->total_damage_cost, 2) }}
                                                @endif
                                            </p>
                                            @endif
                                        </div>
                                        @else
                                        <div class="space-y-1 text-sm">
                                            <p class="text-yellow-600 dark:text-yellow-400">
                                                <span class="font-medium">Status:</span> 
                                                <span>Pending</span>
                                            </p>
                                            @if(in_array($booking->status, ['active', 'returned']))
                                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-2">
                                                Click the button above or use the action buttons in the sidebar to complete the return inspection.
                                            </p>
                                            @else
                                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-2">
                                                Return inspection will be available when the rental is active.
                                            </p>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    <div>
                                        @if($booking->returnInspection)
                                        <a href="{{ route('inspections.show', $booking->returnInspection) }}" class="bg-orange-100 hover:bg-orange-200 text-orange-800 px-3 py-1 rounded-md text-sm">
                                            View Details
                                        </a>
                                        @else
                                        <span class="px-3 py-1 rounded-md text-sm bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                            Not Started
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contract -->
                    @if($booking->contract)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Contract</h3>
                            <a href="{{ route('bookings.contract.download', $booking) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-md text-sm" target="_blank">
                                Download PDF
                            </a>
                        </div>
                        <dl class="grid grid-cols-2 gap-2 text-sm">
                            <dt class="text-gray-500 dark:text-gray-400">Contract No</dt>
                            <dd class="font-medium">{{ $booking->contract->contract_number }}</dd>
                            <dt class="text-gray-500 dark:text-gray-400">Date</dt>
                            <dd>{{ $booking->contract->contract_date->format('M d, Y') }}</dd>
                            <dt class="text-gray-500 dark:text-gray-400">Status</dt>
                            <dd>
                                <span class="px-2 py-0.5 rounded text-xs font-medium
                                    @if($booking->contract->status === 'signed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                    @endif">
                                    {{ ucfirst($booking->contract->status) }}
                                </span>
                            </dd>
                        </dl>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Print and have the customer sign. Keep a copy for your records.</p>
                    </div>
                    @endif

                    <!-- Invoices -->
                    @if($booking->invoices->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Invoices</h3>
                            @can('invoices.create')
                            <a href="{{ route('invoices.create', ['booking_id' => $booking->id]) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                Create Invoice
                            </a>
                            @endcan
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Invoice #</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Type</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Amount</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Balance</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($booking->invoices as $invoice)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-2">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                {{ $invoice->invoice_number }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-2">{{ ucfirst($invoice->type) }}</td>
                                        <td class="px-4 py-2">{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if($invoice->status === 'paid') bg-green-100 text-green-800
                                                @elseif($invoice->status === 'overdue') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-right">N${{ number_format($invoice->total_amount, 2) }}</td>
                                        <td class="px-4 py-2 text-right {{ $invoice->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            N${{ number_format($invoice->balance, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Payments -->
                    @if($booking->payments->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Payments</h3>
                            @can('payments.create')
                            <a href="{{ route('payments.create', ['booking_id' => $booking->id]) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                Add Payment
                            </a>
                            @endcan
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Method</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Reference</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($booking->payments as $payment)
                                    <tr>
                                        <td class="px-4 py-2">{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-2">{{ ucfirst($payment->method) }}</td>
                                        <td class="px-4 py-2">{{ $payment->reference_number ?? '-' }}</td>
                                        <td class="px-4 py-2 text-right">N${{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar Actions -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Actions</h3>
                        <div class="space-y-2">
                            @if($booking->status === 'pending')
                                @can('bookings.confirm')
                                <button type="button" onclick="window.openPickupInspectionModal()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                                    Confirm Booking & Complete Pickup Inspection
                                </button>
                                @endcan
                            @endif

                            @if($booking->status === 'confirmed')
                                @can('bookings.update')
                                @if(!$booking->pickupInspection)
                                <button type="button" onclick="window.openPickupInspectionModal()" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md mb-2">
                                    Complete Pickup Inspection
                                </button>
                                @else
                                <form method="POST" action="{{ route('bookings.start-rental', $booking) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">
                                        Start Rental
                                    </button>
                                </form>
                                @endif
                                @endcan
                                @can('inspections.create')
                                @if($booking->pickupInspection)
                                <a href="{{ route('inspections.show', $booking->pickupInspection) }}" class="block w-full bg-purple-100 hover:bg-purple-200 text-purple-800 text-center px-4 py-2 rounded-md mt-2">
                                    View Pickup Inspection
                                </a>
                                @endif
                                @endcan
                            @endif

                            @if($booking->status === 'active')
                                @can('bookings.update')
                                @if(!$booking->returnInspection)
                                <button type="button" onclick="window.openReturnInspectionModal()" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md mb-2">
                                    Return Trailer & Complete Return Inspection
                                </button>
                                @else
                                <form method="POST" action="{{ route('bookings.return', $booking) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">
                                        Return Trailer
                                    </button>
                                </form>
                                @endif
                                @endcan
                                @can('inspections.create')
                                @if($booking->returnInspection)
                                <a href="{{ route('inspections.show', $booking->returnInspection) }}" class="block w-full bg-orange-100 hover:bg-orange-200 text-orange-800 text-center px-4 py-2 rounded-md mt-2">
                                    View Return Inspection
                                </a>
                                @endif
                                @endcan
                            @endif

                            @if(in_array($booking->status, ['draft', 'pending', 'confirmed']))
                                @can('bookings.cancel')
                                <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="inline" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                    @csrf
                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                                        Cancel Booking
                                    </button>
                                </form>
                                @endcan
                            @endif

                            @can('payments.create')
                            <a href="{{ route('payments.create', ['booking_id' => $booking->id]) }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center px-4 py-2 rounded-md">
                                Record Payment
                            </a>
                            @endcan

                            @can('bookings.view')
                            <a href="{{ route('bookings.contract.download', $booking) }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center px-4 py-2 rounded-md" target="_blank">
                                {{ $booking->contract ? 'Download Contract (' . $booking->contract->contract_number . ')' : 'Generate & Download Contract' }}
                            </a>
                            @endcan

                            @can('inspections.create')
                            @if(!$booking->pickupInspection && in_array($booking->status, ['confirmed', 'active']))
                            <a href="{{ route('inspections.create', ['booking_id' => $booking->id, 'type' => 'pickup']) }}" class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center px-4 py-2 rounded-md">
                                Pre-Pickup Inspection
                            </a>
                            @endif
                            @if(!$booking->returnInspection && in_array($booking->status, ['active', 'returned']))
                            <a href="{{ route('inspections.create', ['booking_id' => $booking->id, 'type' => 'return']) }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center px-4 py-2 rounded-md">
                                Return Inspection
                            </a>
                            @endif
                            @if($booking->pickupInspection)
                            <a href="{{ route('inspections.show', $booking->pickupInspection) }}" class="block w-full bg-purple-100 hover:bg-purple-200 text-purple-800 text-center px-4 py-2 rounded-md">
                                View Pickup Inspection
                            </a>
                            @endif
                            @if($booking->returnInspection)
                            <a href="{{ route('inspections.show', $booking->returnInspection) }}" class="block w-full bg-orange-100 hover:bg-orange-200 text-orange-800 text-center px-4 py-2 rounded-md">
                                View Return Inspection
                            </a>
                            @endif
                            @endcan

                            @can('invoices.view')
                            @php
                                $rentalInvoice = $booking->invoices()->where('type', 'rental')->first();
                            @endphp
                            @if($rentalInvoice)
                            <a href="{{ route('invoices.show', $rentalInvoice) }}" class="block w-full bg-teal-600 hover:bg-teal-700 text-white text-center px-4 py-2 rounded-md">
                                View Invoice ({{ $rentalInvoice->invoice_number }})
                            </a>
                            @else
                            <form method="POST" action="{{ route('bookings.generate-invoice', $booking) }}" class="inline">
                                @csrf
                                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-md">
                                    Generate Invoice
                                </button>
                            </form>
                            @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pickup Inspection Modal -->
    @if(!$booking->pickupInspection && in_array($booking->status, ['pending', 'confirmed', 'active']))
    <div id="pickupInspectionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white dark:bg-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold dark:text-gray-200">Pickup Inspection - {{ $booking->booking_number }}</h3>
                    <button onclick="window.closePickupInspectionModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ $booking->status === 'pending' ? route('bookings.confirm', $booking) : route('inspections.store') }}" enctype="multipart/form-data" id="pickupInspectionForm">
                    @csrf
                    @if($booking->status !== 'pending')
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                    <input type="hidden" name="type" value="pickup">
                    @endif
                    
                    @include('inspections.partials.inspection-form', ['type' => 'pickup'])

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="window.closePickupInspectionModal()" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                            Cancel
                        </button>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                            @if($booking->status === 'pending')
                                Confirm Booking & Complete Inspection
                            @else
                                Complete Pickup Inspection
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Return Inspection Modal -->
    @if(!$booking->returnInspection && in_array($booking->status, ['active', 'returned']))
    <div id="returnInspectionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white dark:bg-gray-800 max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold dark:text-gray-200">Return Inspection - {{ $booking->booking_number }}</h3>
                    <button onclick="window.closeReturnInspectionModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('bookings.return', $booking) }}" enctype="multipart/form-data" id="returnInspectionForm">
                    @csrf
                    
                    @include('inspections.partials.inspection-form', ['type' => 'return'])

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="window.closeReturnInspectionModal()" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                            Cancel
                        </button>
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">
                            Return Trailer & Complete Inspection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        // Define functions immediately in global scope to ensure they're available when buttons are clicked
        (function() {
            window.openPickupInspectionModal = function() {
            const modal = document.getElementById('pickupInspectionModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            } else {
                console.warn('Pickup inspection modal not found. Redirecting to inspection create page.');
                window.location.href = '{{ route("inspections.create", ["booking_id" => $booking->id, "type" => "pickup"]) }}';
            }
        };

        window.closePickupInspectionModal = function() {
            const modal = document.getElementById('pickupInspectionModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = ''; // Restore scrolling
            }
        };

        window.openReturnInspectionModal = function() {
            const modal = document.getElementById('returnInspectionModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            } else {
                console.warn('Return inspection modal not found. Redirecting to inspection create page.');
                window.location.href = '{{ route("inspections.create", ["booking_id" => $booking->id, "type" => "return"]) }}';
            }
        };

        window.closeReturnInspectionModal = function() {
            const modal = document.getElementById('returnInspectionModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = ''; // Restore scrolling
            }
        };
        })(); // End IIFE - functions are now available globally

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const pickupModal = document.getElementById('pickupInspectionModal');
            const returnModal = document.getElementById('returnInspectionModal');
            if (event.target == pickupModal) {
                window.closePickupInspectionModal();
            }
            if (event.target == returnModal) {
                window.closeReturnInspectionModal();
            }
        });

        // Handle inspection form submission - convert to JSON format
        document.addEventListener('DOMContentLoaded', function() {
            const pickupForm = document.getElementById('pickupInspectionForm');
            const returnForm = document.getElementById('returnInspectionForm');

            if (pickupForm) {
                pickupForm.addEventListener('submit', function(e) {
                    // Check if form is submitting to bookings.confirm (pending status)
                    const formAction = this.getAttribute('action');
                    const isConfirmRoute = formAction.includes('bookings') && formAction.includes('confirm');
                    
                    if (isConfirmRoute) {
                        // For pending bookings, convert to JSON format for bookings.confirm route
                        const inspectionData = {
                            checklist: {
                                exterior: {
                                    body_condition: this.querySelector('select[name="checklist[exterior][body_condition]"]')?.value || null,
                                    lights_working: this.querySelector('select[name="checklist[exterior][lights_working]"]')?.value || null,
                                    tires_condition: this.querySelector('select[name="checklist[exterior][tires_condition]"]')?.value || null,
                                    hitch_condition: this.querySelector('select[name="checklist[exterior][hitch_condition]"]')?.value || null,
                                },
                                interior: {
                                    floor_condition: this.querySelector('select[name="checklist[interior][floor_condition]"]')?.value || null,
                                    walls_condition: this.querySelector('select[name="checklist[interior][walls_condition]"]')?.value || null,
                                    door_working: this.querySelector('select[name="checklist[interior][door_working]"]')?.value || null,
                                },
                                safety: {
                                    brakes_working: this.querySelector('select[name="checklist[safety][brakes_working]"]')?.value || null,
                                    safety_chains: this.querySelector('select[name="checklist[safety][safety_chains]"]')?.value || null,
                                    registration_valid: this.querySelector('select[name="checklist[safety][registration_valid]"]')?.value || null,
                                }
                            },
                            notes: this.querySelector('textarea[name="notes"]')?.value || null,
                            condition_notes: this.querySelector('textarea[name="condition_notes"]')?.value || null,
                            is_damaged: this.querySelector('input[name="is_damaged"]')?.checked || false,
                        };

                        // Add hidden input with inspection data (photos will be handled separately via FormData)
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'inspection_data';
                        input.value = JSON.stringify(inspectionData);
                        this.appendChild(input);
                    }
                    // For confirmed/active bookings, form submits directly to inspections.store with form fields
                    // The form fields are already properly named, so no conversion needed
                });
            }

            if (returnForm) {
                returnForm.addEventListener('submit', function(e) {
                    // Don't prevent default - let form submit normally with files
                    const inspectionData = {
                        checklist: {
                            exterior: {
                                body_condition: this.querySelector('select[name="checklist[exterior][body_condition]"]')?.value || null,
                                lights_working: this.querySelector('select[name="checklist[exterior][lights_working]"]')?.value || null,
                                tires_condition: this.querySelector('select[name="checklist[exterior][tires_condition]"]')?.value || null,
                                hitch_condition: this.querySelector('select[name="checklist[exterior][hitch_condition]"]')?.value || null,
                            },
                            interior: {
                                floor_condition: this.querySelector('select[name="checklist[interior][floor_condition]"]')?.value || null,
                                walls_condition: this.querySelector('select[name="checklist[interior][walls_condition]"]')?.value || null,
                                door_working: this.querySelector('select[name="checklist[interior][door_working]"]')?.value || null,
                            },
                            safety: {
                                brakes_working: this.querySelector('select[name="checklist[safety][brakes_working]"]')?.value || null,
                                safety_chains: this.querySelector('select[name="checklist[safety][safety_chains]"]')?.value || null,
                                registration_valid: this.querySelector('select[name="checklist[safety][registration_valid]"]')?.value || null,
                            }
                        },
                        notes: this.querySelector('textarea[name="notes"]')?.value || null,
                        condition_notes: this.querySelector('textarea[name="condition_notes"]')?.value || null,
                        is_damaged: this.querySelector('input[name="is_damaged"]')?.checked || false,
                    };

                    // Collect damage items
                    const damageItems = [];
                    this.querySelectorAll('.damage-item').forEach((item) => {
                        const desc = item.querySelector('input[name*="[description]"]');
                        const loc = item.querySelector('input[name*="[location]"]');
                        const cost = item.querySelector('input[name*="[estimated_cost]"]');
                        const sev = item.querySelector('select[name*="[severity]"]');
                        
                        if (desc && desc.value && cost && cost.value) {
                            damageItems.push({
                                description: desc.value,
                                location: loc ? loc.value : null,
                                estimated_cost: parseFloat(cost.value),
                                severity: sev ? sev.value : 'minor',
                            });
                        }
                    });
                    inspectionData.damage_items = damageItems;

                    // Add hidden input with inspection data (photos will be handled separately via FormData)
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'inspection_data';
                    input.value = JSON.stringify(inspectionData);
                    this.appendChild(input);
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
