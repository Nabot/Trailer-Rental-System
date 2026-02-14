<div class="space-y-6">
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-kpi-card 
            title="Revenue This Month" 
            :value="'N$' . number_format($revenueThisMonth, 2)"
            :change="$revenueChange >= 0 ? '+' . number_format($revenueChange, 1) . '%' : number_format($revenueChange, 1) . '%'"
            :changeType="$revenueChange >= 0 ? 'positive' : 'negative'"
            href="{{ route('payments.index') }}"
            color="green"
        >
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </x-slot>
        </x-kpi-card>

        <x-kpi-card 
            title="Active Bookings" 
            :value="$activeBookings"
            href="{{ route('bookings.index', ['status' => 'active']) }}"
            color="blue"
        >
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </x-slot>
        </x-kpi-card>

        <x-kpi-card 
            title="Pending Bookings" 
            :value="$pendingBookings"
            href="{{ route('bookings.index', ['status' => 'pending']) }}"
            color="yellow"
        >
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </x-slot>
        </x-kpi-card>

        <x-kpi-card 
            title="Available Trailers" 
            :value="$availableTrailers . '/' . $totalTrailers"
            href="{{ route('trailers.index') }}"
            color="purple"
        >
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </x-slot>
        </x-kpi-card>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-kpi-card 
            title="Total Bookings" 
            :value="$totalBookings"
            href="{{ route('bookings.index') }}"
            color="indigo"
        >
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </x-slot>
        </x-kpi-card>

        <x-kpi-card 
            title="Total Customers" 
            :value="$totalCustomers"
            href="{{ route('customers.index') }}"
            color="blue"
        >
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </x-slot>
        </x-kpi-card>

        <x-kpi-card 
            title="Pending Invoices" 
            :value="$pendingInvoices"
            href="{{ route('invoices.index', ['status' => 'pending']) }}"
            color="yellow"
        >
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </x-slot>
        </x-kpi-card>

        <x-kpi-card 
            title="Overdue Invoices" 
            :value="$overdueInvoices"
            href="{{ route('invoices.index', ['status' => 'overdue']) }}"
            color="red"
        >
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </x-slot>
        </x-kpi-card>
    </div>

    <!-- Quick Actions & Today's Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    @can('bookings.create')
                    <a href="{{ route('bookings.create') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded-md transition-colors">
                        New Booking
                    </a>
                    @endcan
                    @can('payments.create')
                    <a href="{{ route('payments.create') }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded-md transition-colors">
                        Record Payment
                    </a>
                    @endcan
                    @can('invoices.create')
                    <a href="{{ route('invoices.create') }}" class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center px-4 py-2 rounded-md transition-colors">
                        Create Invoice
                    </a>
                    @endcan
                    @can('customers.create')
                    <a href="{{ route('customers.create') }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center px-4 py-2 rounded-md transition-colors">
                        Add Customer
                    </a>
                    @endcan
                    <a href="{{ route('inquiries.create') }}" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center px-4 py-2 rounded-md transition-colors">
                        Add Lead
                    </a>
                    <a href="{{ route('quotes.create') }}" class="block w-full bg-orange-400 hover:bg-orange-500 text-white text-center px-4 py-2 rounded-md transition-colors">
                        Create Quote
                    </a>
                </div>
            </div>
        </div>

        <!-- Today's Activity -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Today's Activity</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Pickups Scheduled</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Today</p>
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $todayPickups }}</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-orange-100 dark:bg-orange-900 flex items-center justify-center">
                                <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Returns Scheduled</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Today</p>
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $todayReturns }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Chart Placeholder -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Revenue Trend</h3>
                <div class="h-48 flex items-center justify-center text-gray-400 dark:text-gray-500">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <p class="text-sm">Chart coming soon</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Bookings</h3>
                <a href="{{ route('bookings.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">View All →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Booking #</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Trailer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dates</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentBookings as $booking)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                    {{ $booking->booking_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $booking->trailer->name }}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $booking->customer->name }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                {{ $booking->start_date->format('M d') }} - {{ $booking->end_date->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <x-status-badge :status="$booking->status" type="booking" />
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-gray-100">N${{ number_format($booking->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12">
                                <x-empty-state 
                                    title="No bookings found"
                                    description="Get started by creating your first booking."
                                    :action="route('bookings.create')"
                                    actionLabel="Create Booking"
                                >
                                    <x-slot name="icon">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </x-slot>
                                </x-empty-state>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Top Customers</h3>
            <div class="space-y-3">
                @forelse($topCustomers as $customer)
                <div class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                            <span class="text-orange-600 dark:text-orange-400 font-semibold">{{ substr($customer->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $customer->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->bookings->count() }} bookings</p>
                        </div>
                    </div>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">N${{ number_format($customer->total_spent ?? 0, 2) }}</span>
                </div>
                @empty
                <x-empty-state 
                    title="No customer data available"
                    description="Customer statistics will appear here once bookings are created."
                >
                    <x-slot name="icon">
                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </x-slot>
                </x-empty-state>
                @endforelse
            </div>
        </div>
    </div>
</div>
