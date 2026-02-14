<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Portal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(auth()->user()->isCustomer() && auth()->user()->customer_id)
                @php
                    $customer = auth()->user()->customer;
                    $customer->load(['bookings.trailer', 'invoices']);
                @endphp

                <!-- Welcome Section -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                    <h2 class="text-2xl font-bold mb-2">Welcome, {{ $customer->name }}!</h2>
                    <p class="text-gray-600">Manage your bookings, invoices, and documents</p>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-sm font-medium text-gray-600 mb-2">Total Bookings</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $customer->bookings->count() }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-sm font-medium text-gray-600 mb-2">Outstanding Balance</h3>
                        <p class="text-3xl font-bold text-red-600">N${{ number_format($customer->invoices->sum('balance'), 2) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-sm font-medium text-gray-600 mb-2">Total Spent</h3>
                        <p class="text-3xl font-bold text-green-600">N${{ number_format($customer->totalSpent(), 2) }}</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('bookings.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md text-center">
                            Request New Booking
                        </a>
                        <a href="{{ route('customers.show', $customer) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-md text-center">
                            View My Profile
                        </a>
                    </div>
                </div>

                <!-- My Bookings -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">My Bookings</h3>
                    @if($customer->bookings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left">Booking #</th>
                                    <th class="px-4 py-2 text-left">Trailer</th>
                                    <th class="px-4 py-2 text-left">Dates</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-right">Amount</th>
                                    <th class="px-4 py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->bookings->take(5) as $booking)
                                <tr>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 hover:underline">
                                            {{ $booking->booking_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2">{{ $booking->trailer->name }}</td>
                                    <td class="px-4 py-2">{{ $booking->start_date->format('M d') }} - {{ $booking->end_date->format('M d, Y') }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                            @elseif($booking->status === 'active') bg-blue-100 text-blue-800
                                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-right">N${{ number_format($booking->total_amount, 2) }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 hover:underline">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('bookings.index') }}" class="text-blue-600 hover:underline">View All Bookings</a>
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-4">No bookings yet. <a href="{{ route('bookings.create') }}" class="text-blue-600 hover:underline">Request a booking</a></p>
                    @endif
                </div>

                <!-- My Invoices -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">My Invoices</h3>
                    @if($customer->invoices->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left">Invoice #</th>
                                    <th class="px-4 py-2 text-left">Date</th>
                                    <th class="px-4 py-2 text-right">Amount</th>
                                    <th class="px-4 py-2 text-right">Balance</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->invoices->take(5) as $invoice)
                                <tr>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:underline">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2">{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                    <td class="px-4 py-2 text-right">N${{ number_format($invoice->total_amount, 2) }}</td>
                                    <td class="px-4 py-2 text-right {{ $invoice->balance > 0 ? 'text-red-600 font-semibold' : 'text-green-600' }}">
                                        N${{ number_format($invoice->balance, 2) }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($invoice->status === 'paid') bg-green-100 text-green-800
                                            @elseif($invoice->status === 'overdue') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:underline mr-2">View</a>
                                        <a href="{{ route('invoices.download', $invoice) }}" class="text-green-600 hover:underline">PDF</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-4">No invoices yet.</p>
                    @endif
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <p class="text-center text-gray-500">Customer portal is only available for customer accounts.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
