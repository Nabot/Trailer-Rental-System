<!-- Inspection Checklist -->
<div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Inspection Checklist</h3>
    
    <!-- Exterior -->
    <div class="mb-6">
        <h4 class="font-medium mb-3">Exterior</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Body Condition</label>
                <select name="checklist[exterior][body_condition]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="excellent">Excellent</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                    <option value="poor">Poor</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Lights Working</label>
                <select name="checklist[exterior][lights_working]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                    <option value="partial">Partial</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Tires Condition</label>
                <select name="checklist[exterior][tires_condition]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="excellent">Excellent</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                    <option value="poor">Poor</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Hitch Condition</label>
                <select name="checklist[exterior][hitch_condition]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
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
                <select name="checklist[interior][floor_condition]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="excellent">Excellent</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                    <option value="poor">Poor</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Walls Condition</label>
                <select name="checklist[interior][walls_condition]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="excellent">Excellent</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                    <option value="poor">Poor</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Door Working</label>
                <select name="checklist[interior][door_working]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
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
                <select name="checklist[safety][brakes_working]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Safety Chains</label>
                <select name="checklist[safety][safety_chains]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Registration Valid</label>
                <select name="checklist[safety][registration_valid]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
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
    <textarea name="condition_notes" rows="4" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" placeholder="General condition notes..."></textarea>
</div>

<!-- Photos -->
<div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Photos</h3>
    <input type="file" name="photos[]" multiple accept="image/*" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Upload multiple photos of the trailer condition</p>
</div>

@if($type === 'return')
<!-- Damage Assessment (only for return inspection) -->
<div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Damage Assessment</h3>
    
    <div class="mb-4">
        <label class="flex items-center">
            <input type="checkbox" name="is_damaged" value="1" id="isDamaged{{ $type }}" class="rounded border-gray-300 dark:border-gray-700">
            <span class="ml-2 dark:text-gray-300">Trailer has damage</span>
        </label>
    </div>

    <div id="damageItems{{ $type }}" style="display: none;">
        <div id="damageItemsContainer{{ $type }}">
            <!-- Damage items will be added here dynamically -->
        </div>
        <button type="button" id="addDamageItem{{ $type }}" class="mt-4 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
            Add Damage Item
        </button>
    </div>
</div>

<script>
    (function() {
        const type = '{{ $type }}';
        const isDamagedId = 'isDamaged' + type;
        const damageItemsId = 'damageItems' + type;
        const containerId = 'damageItemsContainer' + type;
        const addButtonId = 'addDamageItem' + type;
        
        let damageItemCount = 0;

        document.getElementById(isDamagedId)?.addEventListener('change', function() {
            document.getElementById(damageItemsId).style.display = this.checked ? 'block' : 'none';
        });

        document.getElementById(addButtonId)?.addEventListener('click', function() {
            const container = document.getElementById(containerId);
            const itemHtml = `
                <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-4 mb-4 damage-item">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Description *</label>
                            <input type="text" name="damage_items[${damageItemCount}][description]" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Location</label>
                            <input type="text" name="damage_items[${damageItemCount}][location]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Estimated Cost (N$) *</label>
                            <input type="number" step="0.01" min="0" name="damage_items[${damageItemCount}][estimated_cost]" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2 dark:text-gray-300">Severity</label>
                            <select name="damage_items[${damageItemCount}][severity]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
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
    })();
</script>
@endif

<!-- Notes -->
<div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Additional Notes</h3>
    <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" placeholder="Any additional notes..."></textarea>
</div>
