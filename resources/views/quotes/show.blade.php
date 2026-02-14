<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Quote: {{ $quote->quote_number }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('quotes.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                    Back
                </a>
                @if($quote->status === 'draft')
                <a href="{{ route('quotes.edit', $quote) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                    Edit
                </a>
                @endif
                <a href="{{ route('quotes.download', $quote) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                    Download PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded">
                {{ session('error') }}
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Quote Details -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Quote Details</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Quote Number</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100 font-semibold">{{ $quote->quote_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <x-status-badge :status="$quote->status" type="quote" />
                                </dd>
                            </div>
                            @if($quote->inquiry)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Inquiry</dt>
                                <dd class="mt-1">
                                    <a href="{{ route('inquiries.show', $quote->inquiry) }}" class="text-orange-600 dark:text-orange-400 hover:underline">
                                        {{ $quote->inquiry->inquiry_number }}
                                    </a>
                                </dd>
                            </div>
                            @endif
                            @if($quote->customer)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer</dt>
                                <dd class="mt-1">
                                    <a href="{{ route('customers.show', $quote->customer) }}" class="text-orange-600 dark:text-orange-400 hover:underline">
                                        {{ $quote->customer->name }}
                                    </a>
                                </dd>
                            </div>
                            @endif
                            @if($quote->trailer)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Trailer</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $quote->trailer->name }}</dd>
                            </div>
                            @endif
                            @if($quote->start_date && $quote->end_date)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rental Period</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $quote->start_date->format('M d, Y') }} - {{ $quote->end_date->format('M d, Y') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Days</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $quote->total_days }} days</dd>
                            </div>
                            @endif
                            @if($quote->expires_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Expires</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $quote->expires_at->format('M d, Y') }}
                                    @if($quote->isExpired())
                                    <span class="text-red-600 dark:text-red-400 text-sm">(Expired)</span>
                                    @endif
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Pricing -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Pricing Breakdown</h3>
                        <dl class="space-y-2">
                            @if($quote->rental_cost > 0)
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">Rental Cost ({{ $quote->total_days }} days × N${{ number_format($quote->rate_per_day, 2) }})</dt>
                                <dd class="font-semibold">N${{ number_format($quote->rental_cost, 2) }}</dd>
                            </div>
                            @endif
                            @if($quote->delivery_fee > 0)
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">Delivery Fee</dt>
                                <dd>N${{ number_format($quote->delivery_fee, 2) }}</dd>
                            </div>
                            @endif
                            @if($quote->straps_fee > 0)
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">Straps Fee</dt>
                                <dd>N${{ number_format($quote->straps_fee, 2) }}</dd>
                            </div>
                            @endif
                            @if($quote->damage_waiver_fee > 0)
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">Damage Waiver Fee</dt>
                                <dd>N${{ number_format($quote->damage_waiver_fee, 2) }}</dd>
                            </div>
                            @endif
                            <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-2">
                                <dt class="text-gray-600 dark:text-gray-400">Subtotal</dt>
                                <dd class="font-semibold">N${{ number_format($quote->subtotal, 2) }}</dd>
                            </div>
                            @if($quote->tax > 0)
                            <div class="flex justify-between">
                                <dt class="text-gray-600 dark:text-gray-400">Tax</dt>
                                <dd>N${{ number_format($quote->tax, 2) }}</dd>
                            </div>
                            @endif
                            <div class="flex justify-between border-t-2 border-gray-300 dark:border-gray-600 pt-2">
                                <dt class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Amount</dt>
                                <dd class="text-lg font-bold text-orange-600 dark:text-orange-400">N${{ number_format($quote->total_amount, 2) }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if($quote->items->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Additional Items</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Quantity</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Unit Price</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($quote->items as $item)
                                    <tr>
                                        <td class="px-4 py-2">{{ $item->description }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($item->quantity, 2) }}</td>
                                        <td class="px-4 py-2 text-right">N${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="px-4 py-2 text-right font-semibold">N${{ number_format($item->total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($quote->notes)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-2">Notes</h3>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $quote->notes }}</p>
                    </div>
                    @endif

                    @if($quote->terms_conditions)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-2">Terms & Conditions</h3>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap text-sm">{{ $quote->terms_conditions }}</p>
                    </div>
                    @endif
                </div>

                <!-- Sidebar Actions -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Actions</h3>
                        <div class="space-y-2">
                            @if($quote->status === 'draft')
                            <form method="POST" action="{{ route('quotes.mark-as-sent', $quote) }}" class="inline-block w-full">
                                @csrf
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                                    Mark as Sent
                                </button>
                            </form>
                            @endif
                            @if($quote->status === 'sent' || $quote->status === 'accepted')
                            @if($quote->customer_id && $quote->trailer_id && !$quote->converted_to_booking_id)
                            <form method="POST" action="{{ route('quotes.convert-to-booking', $quote) }}" class="inline-block w-full">
                                @csrf
                                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md text-sm">
                                    Convert to Booking
                                </button>
                            </form>
                            @endif
                            @endif
                            @if($quote->convertedToBooking)
                            <div class="p-3 bg-green-50 dark:bg-green-900 rounded">
                                <div class="text-sm font-semibold text-green-800 dark:text-green-200">Converted</div>
                                <a href="{{ route('bookings.show', $quote->convertedToBooking) }}" class="text-sm text-green-600 dark:text-green-400 hover:underline">
                                    View Booking: {{ $quote->convertedToBooking->booking_number }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Information</h3>
                        <dl class="space-y-2 text-sm">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Created</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $quote->created_at->format('M d, Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Created By</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $quote->createdBy->name }}</dd>
                            </div>
                            @if($quote->sent_at)
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Sent</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $quote->sent_at->format('M d, Y H:i') }}</dd>
                            </div>
                            @endif
                            @if($quote->accepted_at)
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Accepted</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $quote->accepted_at->format('M d, Y H:i') }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
