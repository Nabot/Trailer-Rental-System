<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Inspections') }}
            </h2>
            @can('inspections.create')
            <a href="{{ route('bookings.index') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">
                Start Inspection
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Action - Start Inspection -->
            @can('inspections.create')
            <div class="mb-6 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Start New Inspection</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Create a pickup or return inspection for a booking</p>
                    </div>
                    <a href="{{ route('bookings.index') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-md font-semibold shadow-lg hover:shadow-xl transition-all">
                        Start Inspection
                    </a>
                </div>
            </div>
            @endcan

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" class="flex gap-4 flex-wrap">
                    <input type="text" name="booking_id" value="{{ request('booking_id') }}" placeholder="Booking ID" class="rounded-md border-gray-300">
                    <select name="type" class="rounded-md border-gray-300">
                        <option value="">All Types</option>
                        <option value="pickup" {{ request('type') === 'pickup' ? 'selected' : '' }}>Pickup</option>
                        <option value="return" {{ request('type') === 'return' ? 'selected' : '' }}>Return</option>
                    </select>
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">Filter</button>
                    <a href="{{ route('inspections.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">Clear</a>
                </form>
            </div>

            <!-- Inspections Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Booking</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Inspected By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Damaged</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Damage Cost</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($inspections as $inspection)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('bookings.show', $inspection->booking_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $inspection->booking->booking_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $inspection->type === 'pickup' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($inspection->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $inspection->inspectedBy->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $inspection->inspected_at->format('M d, Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($inspection->is_damaged)
                                    <span class="text-red-600 font-semibold">Yes</span>
                                    @else
                                    <span class="text-green-600">No</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($inspection->total_damage_cost > 0)
                                    <span class="text-red-600 font-semibold">N${{ number_format($inspection->total_damage_cost, 2) }}</span>
                                    @else
                                    <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('inspections.show', $inspection) }}" class="text-blue-600 dark:text-blue-400 hover:underline">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No inspections found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4">
                    {{ $inspections->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
