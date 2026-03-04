<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Leads & Commission') }}
            </h2>
            <a href="{{ route('inquiries.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                Back to Leads
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                You earn {{ number_format($commissionRate * 100) }}% of the booking value when a lead assigned to you is converted to a booking. Cancelled bookings are not included.
            </p>

            <!-- Date filter -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                    </div>
                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">Apply</button>
                    <a href="{{ route('reports.commission') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">Reset</a>
                </form>
            </div>

            <!-- Summary cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">My leads (period)</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalLeads }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $startDate }} – {{ $endDate }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Converted to booking</div>
                    <div class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">{{ $convertedCount }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total booking value</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">N$ {{ number_format($totalBookingValue, 2) }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Commission due ({{ number_format($commissionRate * 100) }}%)</div>
                    <div class="mt-2 text-3xl font-bold text-emerald-600 dark:text-emerald-400">N$ {{ number_format($totalCommission, 2) }}</div>
                </div>
            </div>

            <!-- Converted leads (earn commission) -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Converted leads (commission earned)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lead</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Booking</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Booking value</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Commission ({{ number_format($commissionRate * 100) }}%)</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($convertedLeads as $lead)
                                    @php
                                        $booking = $lead->convertedToBooking;
                                        $commission = round((float) $booking->total_amount * $commissionRate, 2);
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('inquiries.show', $lead) }}" class="font-medium text-orange-600 dark:text-orange-400 hover:underline">{{ $lead->inquiry_number }}</a>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $lead->name ?? $lead->customer?->name ?? '—' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('bookings.show', $booking) }}" class="text-sm text-orange-600 dark:text-orange-400 hover:underline">{{ $booking->booking_number }}</a>
                                            @if($booking->trailer)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->trailer->name ?? '—' }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            N$ {{ number_format((float) $booking->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                            N$ {{ number_format($commission, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('inquiries.show', $lead) }}" class="text-orange-600 dark:text-orange-400 hover:underline mr-2">Lead</a>
                                            <a href="{{ route('bookings.show', $booking) }}" class="text-orange-600 dark:text-orange-400 hover:underline">Booking</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No converted leads in this period. When a lead assigned to you is converted to a booking, it will appear here with your commission.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- All my leads in period -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">All my leads ({{ $startDate }} – {{ $endDate }})</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lead #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name / Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Source</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($leads as $lead)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $lead->inquiry_number }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $lead->name ?? $lead->customer?->name ?? '—' }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $lead->email ?? $lead->phone ?? '—' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            {{ ucfirst(str_replace('_', ' ', $lead->source ?? '—')) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-status-badge :status="$lead->status" type="inquiry" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            {{ $lead->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('inquiries.show', $lead) }}" class="text-orange-600 dark:text-orange-400 hover:underline">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No leads assigned to you in this period.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
