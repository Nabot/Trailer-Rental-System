<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Lead Report') }}
            </h2>
            <a href="{{ route('reports.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                Back to Reports
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                            <option value="">All statuses</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s }}" {{ $statusFilter === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Source</label>
                        <select name="source" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                            <option value="">All sources</option>
                            @foreach($sources as $src)
                                <option value="{{ $src }}" {{ $sourceFilter === $src ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $src)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">Apply</button>
                        <a href="{{ route('reports.leads') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Leads</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalLeads }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $startDate }} to {{ $endDate }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Converted</div>
                    <div class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">{{ $convertedCount }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Conversion Rate</div>
                    <div class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $conversionRate }}%</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Filtered (this view)</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $leads->count() }}</div>
                </div>
            </div>

            <!-- By Status / By Source -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">By Status</h3>
                    <ul class="space-y-2">
                        @foreach($statuses as $s)
                            @php $count = $byStatus->get($s, 0); @endphp
                            @if($count > 0)
                                <li class="flex justify-between text-sm">
                                    <span class="text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $s)) }}</span>
                                    <span class="font-medium">{{ $count }}</span>
                                </li>
                            @endif
                        @endforeach
                        @if($byStatus->isEmpty())
                            <li class="text-sm text-gray-500 dark:text-gray-400">No leads in this period</li>
                        @endif
                    </ul>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">By Source</h3>
                    <ul class="space-y-2">
                        @foreach($bySource as $source => $count)
                            <li class="flex justify-between text-sm">
                                <span class="text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $source)) }}</span>
                                <span class="font-medium">{{ $count }}</span>
                            </li>
                        @endforeach
                        @if($bySource->isEmpty())
                            <li class="text-sm text-gray-500 dark:text-gray-400">No leads in this period</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Leads Table -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Leads ({{ $startDate }} – {{ $endDate }})</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lead #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name / Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Source</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assigned To</th>
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            {{ $lead->assignedTo?->name ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('inquiries.show', $lead) }}" class="text-orange-600 dark:text-orange-400 hover:underline">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No leads found for the selected period and filters.
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
