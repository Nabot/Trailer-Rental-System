<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create ' . ucfirst($type)) . ' Inspection' }}
            </h2>
            <a href="{{ route('bookings.show', $booking) }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                Back to Booking
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Start Inspection Banner -->
            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Start Inspection</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Complete the inspection checklist below for {{ $booking->booking_number }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('bookings.show', $booking) }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                            View Booking
                        </a>
                    </div>
                </div>
            </div>

            <!-- Booking Info -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Booking Information</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Booking:</span>
                        <span class="font-semibold">{{ $booking->booking_number }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Trailer:</span>
                        <span class="font-semibold">{{ $booking->trailer->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Customer:</span>
                        <span class="font-semibold">{{ $booking->customer->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Dates:</span>
                        <span class="font-semibold">{{ $booking->start_date->format('M d') }} - {{ $booking->end_date->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('inspections.store') }}" enctype="multipart/form-data" id="inspectionForm">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                <input type="hidden" name="type" value="{{ $type }}">

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Inspection Checklist</h3>
                    
                    <!-- Exterior -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Exterior</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Body Condition</label>
                                <select name="checklist[exterior][body_condition]" class="w-full rounded-md border-gray-300">
                                    <option value="excellent">Excellent</option>
                                    <option value="good">Good</option>
                                    <option value="fair">Fair</option>
                                    <option value="poor">Poor</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Lights Working</label>
                                <select name="checklist[exterior][lights_working]" class="w-full rounded-md border-gray-300">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                    <option value="partial">Partial</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Tires Condition</label>
                                <select name="checklist[exterior][tires_condition]" class="w-full rounded-md border-gray-300">
                                    <option value="excellent">Excellent</option>
                                    <option value="good">Good</option>
                                    <option value="fair">Fair</option>
                                    <option value="poor">Poor</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Hitch Condition</label>
                                <select name="checklist[exterior][hitch_condition]" class="w-full rounded-md border-gray-300">
                                    <option value="excellent">Excellent</option>
                                    <option value="good">Good</option>
                                    <option value="fair">Fair</option>
                                    <option value="poor">Poor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Interior -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Interior</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Floor Condition</label>
                                <select name="checklist[interior][floor_condition]" class="w-full rounded-md border-gray-300">
                                    <option value="excellent">Excellent</option>
                                    <option value="good">Good</option>
                                    <option value="fair">Fair</option>
                                    <option value="poor">Poor</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Walls Condition</label>
                                <select name="checklist[interior][walls_condition]" class="w-full rounded-md border-gray-300">
                                    <option value="excellent">Excellent</option>
                                    <option value="good">Good</option>
                                    <option value="fair">Fair</option>
                                    <option value="poor">Poor</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Door Working</label>
                                <select name="checklist[interior][door_working]" class="w-full rounded-md border-gray-300">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Safety -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Safety</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Brakes Working</label>
                                <select name="checklist[safety][brakes_working]" class="w-full rounded-md border-gray-300">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Safety Chains</label>
                                <select name="checklist[safety][safety_chains]" class="w-full rounded-md border-gray-300">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Registration Valid</label>
                                <select name="checklist[safety][registration_valid]" class="w-full rounded-md border-gray-300">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Condition Notes -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Condition Notes</h3>
                    <textarea name="condition_notes" rows="4" class="w-full rounded-md border-gray-300" placeholder="General condition notes..."></textarea>
                </div>

                <!-- Photos -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Photos</h3>
                    <input type="file" name="photos[]" multiple accept="image/*" class="w-full rounded-md border-gray-300">
                    <p class="text-sm text-gray-500 mt-2">Upload multiple photos of the trailer condition</p>
                </div>

                <!-- Damage Assessment -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Damage Assessment</h3>
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_damaged" value="1" id="isDamaged" class="rounded border-gray-300">
                            <span class="ml-2">Trailer has damage</span>
                        </label>
                    </div>

                    <div id="damageItems" style="display: none;">
                        <div id="damageItemsContainer">
                            <!-- Damage items will be added here dynamically -->
                        </div>
                        <button type="button" id="addDamageItem" class="mt-4 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                            Add Damage Item
                        </button>
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Additional Notes</h3>
                    <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300" placeholder="Any additional notes..."></textarea>
                </div>

                <div class="flex justify-end">
                    <x-primary-button>
                        {{ __('Complete Inspection') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('isDamaged').addEventListener('change', function() {
            document.getElementById('damageItems').style.display = this.checked ? 'block' : 'none';
        });

        let damageItemCount = 0;
        document.getElementById('addDamageItem').addEventListener('click', function() {
            const container = document.getElementById('damageItemsContainer');
            const itemHtml = `
                <div class="border border-gray-300 rounded-lg p-4 mb-4 damage-item">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Description *</label>
                            <input type="text" name="damage_items[${damageItemCount}][description]" required class="w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Location</label>
                            <input type="text" name="damage_items[${damageItemCount}][location]" class="w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Estimated Cost (N$) *</label>
                            <input type="number" step="0.01" min="0" name="damage_items[${damageItemCount}][estimated_cost]" required class="w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Severity</label>
                            <select name="damage_items[${damageItemCount}][severity]" class="w-full rounded-md border-gray-300">
                                <option value="minor">Minor</option>
                                <option value="moderate">Moderate</option>
                                <option value="major">Major</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="remove-damage-item bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">Remove</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
            damageItemCount++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-damage-item')) {
                e.target.closest('.damage-item').remove();
            }
        });
    </script>
    @endpush
</x-app-layout>
