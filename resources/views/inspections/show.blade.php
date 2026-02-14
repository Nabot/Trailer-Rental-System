<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ ucfirst($inspection->type) }} Inspection - {{ $inspection->booking->booking_number }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('bookings.show', $inspection->booking) }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                    Back to Booking
                </a>
                <a href="{{ route('inspections.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                    All Inspections
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Inspection Details -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Inspection Details</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Booking</dt>
                                <dd class="mt-1">
                                    <a href="{{ route('bookings.show', $inspection->booking) }}" class="text-blue-600 hover:underline">
                                        {{ $inspection->booking->booking_number }}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Type</dt>
                                <dd class="mt-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $inspection->type === 'pickup' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($inspection->type) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Inspected By</dt>
                                <dd class="mt-1">{{ $inspection->inspectedBy->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date</dt>
                                <dd class="mt-1">{{ $inspection->inspected_at->format('M d, Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Has Damage</dt>
                                <dd class="mt-1">
                                    @if($inspection->is_damaged)
                                    <span class="text-red-600 font-semibold">Yes</span>
                                    @else
                                    <span class="text-green-600">No</span>
                                    @endif
                                </dd>
                            </div>
                            @if($inspection->total_damage_cost > 0)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Damage Cost</dt>
                                <dd class="mt-1 text-red-600 font-semibold text-lg">N${{ number_format($inspection->total_damage_cost, 2) }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Checklist -->
                    @if($inspection->checklist)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Checklist</h3>
                        @foreach($inspection->checklist as $category => $items)
                        <div class="mb-4">
                            <h4 class="font-medium mb-2 capitalize">{{ $category }}</h4>
                            <dl class="grid grid-cols-2 gap-2 text-sm">
                                @foreach($items as $item => $value)
                                <div>
                                    <dt class="text-gray-600 capitalize">{{ str_replace('_', ' ', $item) }}:</dt>
                                    <dd class="font-semibold capitalize">{{ $value ?? 'N/A' }}</dd>
                                </div>
                                @endforeach
                            </dl>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Condition Notes -->
                    @if($inspection->condition_notes)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Condition Notes</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $inspection->condition_notes }}</p>
                    </div>
                    @endif

                    <!-- Damage Items -->
                    @if($inspection->damageItems->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Damage Items</h3>
                        <div class="space-y-4">
                            @foreach($inspection->damageItems as $damage)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-semibold">{{ $damage->description }}</h4>
                                        @if($damage->location)
                                        <p class="text-sm text-gray-600">Location: {{ $damage->location }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $damage->severity === 'major' ? 'bg-red-100 text-red-800' : ($damage->severity === 'moderate' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ ucfirst($damage->severity) }}
                                        </span>
                                        <p class="text-lg font-bold text-red-600 mt-1">N${{ number_format($damage->estimated_cost, 2) }}</p>
                                    </div>
                                </div>
                                @if($damage->photos->count() > 0)
                                <div class="grid grid-cols-4 gap-2 mt-2">
                                    @foreach($damage->photos as $photo)
                                    <img src="{{ Storage::url($photo->path) }}" alt="Damage photo" class="w-full h-20 object-cover rounded">
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Photos -->
                    @if($inspection->photos->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Inspection Photos</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($inspection->photos as $photo)
                            <img src="{{ Storage::url($photo->path) }}" alt="Inspection photo" class="w-full h-32 object-cover rounded cursor-pointer" onclick="window.open(this.src)">
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if($inspection->notes)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Additional Notes</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $inspection->notes }}</p>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                        <div class="space-y-2">
                            @can('inspections.create')
                            @if($inspection->type === 'pickup' && !$inspection->booking->returnInspection && $inspection->booking->status === 'active')
                            <a href="{{ route('inspections.create', ['booking_id' => $inspection->booking->id, 'type' => 'return']) }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center px-4 py-2 rounded-md">
                                Start Return Inspection
                            </a>
                            @endif
                            @if($inspection->type === 'return' && !$inspection->booking->pickupInspection && in_array($inspection->booking->status, ['pending', 'confirmed', 'active']))
                            <a href="{{ route('inspections.create', ['booking_id' => $inspection->booking->id, 'type' => 'pickup']) }}" class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center px-4 py-2 rounded-md">
                                Start Pickup Inspection
                            </a>
                            @endif
                            @endcan
                            @if($inspection->type === 'pickup' && !$inspection->booking->pickupInspection)
                            <a href="{{ route('bookings.start-rental', $inspection->booking) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded-md">
                                Start Rental
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
