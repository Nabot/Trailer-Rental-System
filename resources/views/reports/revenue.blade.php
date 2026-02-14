<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Revenue Report') }}
            </h2>
            <a href="{{ route('reports.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" class="flex gap-4 flex-wrap items-end">
                    <div>
                        <label class="block text-sm font-medium mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Group By</label>
                        <select name="group_by" class="rounded-md border-gray-300">
                            <option value="day" {{ $groupBy === 'day' ? 'selected' : '' }}>Day</option>
                            <option value="week" {{ $groupBy === 'week' ? 'selected' : '' }}>Week</option>
                            <option value="month" {{ $groupBy === 'month' ? 'selected' : '' }}>Month</option>
                            <option value="year" {{ $groupBy === 'year' ? 'selected' : '' }}>Year</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md h-fit">Generate</button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Total Revenue</h3>
                    <p class="text-3xl font-bold text-green-600">N${{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Total Bookings</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $totalBookings }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Average Booking Value</h3>
                    <p class="text-3xl font-bold text-purple-600">N${{ number_format($averageBookingValue ?? 0, 2) }}</p>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Revenue Trend</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Period</th>
                                <th class="px-4 py-2 text-right">Revenue</th>
                                <th class="px-4 py-2 w-64">Chart</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $maxRevenue = $revenueData->max('total') ?? 1;
                            @endphp
                            @foreach($revenueData as $data)
                            <tr>
                                <td class="px-4 py-2">{{ $data->period }}</td>
                                <td class="px-4 py-2 text-right font-semibold">N${{ number_format($data->total, 2) }}</td>
                                <td class="px-4 py-2">
                                    <div class="w-full bg-gray-200 rounded-full h-4">
                                        <div class="bg-green-600 h-4 rounded-full" style="width: {{ ($data->total / $maxRevenue) * 100 }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
