<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Trailer Utilization Report') }}
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
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md h-fit">Generate</button>
                </form>
            </div>

            <!-- Utilization Table -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Trailer Utilization ({{ $startDate }} to {{ $endDate }})</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Trailer</th>
                                <th class="px-4 py-2 text-right">Rented Days</th>
                                <th class="px-4 py-2 text-right">Total Days</th>
                                <th class="px-4 py-2 text-right">Utilization Rate</th>
                                <th class="px-4 py-2 text-right">Revenue</th>
                                <th class="px-4 py-2 w-64">Chart</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($utilizationData as $data)
                            <tr>
                                <td class="px-4 py-2 font-semibold">{{ $data['trailer']->name }}</td>
                                <td class="px-4 py-2 text-right">{{ $data['rented_days'] }} days</td>
                                <td class="px-4 py-2 text-right">{{ $data['total_days'] }} days</td>
                                <td class="px-4 py-2 text-right">
                                    <span class="font-semibold {{ $data['utilization_rate'] > 70 ? 'text-green-600' : ($data['utilization_rate'] > 40 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ number_format($data['utilization_rate'], 1) }}%
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right font-semibold">N${{ number_format($data['revenue'], 2) }}</td>
                                <td class="px-4 py-2">
                                    <div class="w-full bg-gray-200 rounded-full h-4">
                                        <div class="h-4 rounded-full {{ $data['utilization_rate'] > 70 ? 'bg-green-600' : ($data['utilization_rate'] > 40 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                             style="width: {{ min(100, $data['utilization_rate']) }}%"></div>
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
