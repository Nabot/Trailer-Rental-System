<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Invoice') }}: {{ $invoice->invoice_number }}
            </h2>
            <a href="{{ route('invoices.show', $invoice) }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if($invoice->status !== 'pending')
            <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                <p><strong>Note:</strong> Only pending invoices can be edited. This invoice status is: <strong>{{ ucfirst($invoice->status) }}</strong></p>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('invoices.update', $invoice) }}" id="invoiceForm">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Booking Selection (Read-only) -->
                        <div>
                            <x-input-label for="booking_id" :value="__('Booking')" />
                            <input type="text" 
                                   value="{{ $invoice->booking ? $invoice->booking->booking_number . ' - ' . $invoice->booking->customer->name . ' (' . $invoice->booking->trailer->name . ')' : 'N/A' }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 bg-gray-100" 
                                   disabled />
                            <input type="hidden" name="booking_id" value="{{ $invoice->booking_id }}" />
                            <x-input-error :messages="$errors->get('booking_id')" class="mt-2" />
                        </div>

                        <!-- Customer Selection (Read-only) -->
                        <div>
                            <x-input-label for="customer_id" :value="__('Customer')" />
                            <input type="text" 
                                   value="{{ $invoice->customer->name }}@if($invoice->customer->email) ({{ $invoice->customer->email }})@endif" 
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 bg-gray-100" 
                                   disabled />
                            <input type="hidden" name="customer_id" value="{{ $invoice->customer_id }}" />
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                        </div>

                        <!-- Invoice Type (Read-only) -->
                        <div>
                            <x-input-label for="type" :value="__('Invoice Type')" />
                            <input type="text" 
                                   value="{{ ucfirst($invoice->type) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 bg-gray-100" 
                                   disabled />
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Invoice Date -->
                        <div>
                            <x-input-label for="invoice_date" :value="__('Invoice Date')" />
                            <x-text-input id="invoice_date" name="invoice_date" type="date" class="mt-1 block w-full" :value="old('invoice_date', $invoice->invoice_date->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('invoice_date')" class="mt-2" />
                        </div>

                        <!-- Due Date -->
                        <div>
                            <x-input-label for="due_date" :value="__('Due Date')" />
                            <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" :value="old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '')" required />
                            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                        </div>

                        <!-- Tax Rate -->
                        <div>
                            <x-input-label for="tax_rate" :value="__('Tax Rate (%)')" />
                            @php
                                $taxRate = old('tax_rate');
                                if ($taxRate === null) {
                                    $taxRate = $invoice->subtotal > 0 ? ($invoice->tax / $invoice->subtotal) * 100 : \App\Models\Setting::get('tax_rate', 0);
                                }
                            @endphp
                            <x-text-input id="tax_rate" name="tax_rate" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full" :value="$taxRate" />
                            <x-input-error :messages="$errors->get('tax_rate')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Invoice Items</h3>
                            <button type="button" id="addItem" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                                Add Item
                            </button>
                        </div>

                        <div id="itemsContainer" class="space-y-4">
                            @foreach($invoice->items as $index => $item)
                            <div class="item-row grid grid-cols-12 gap-4 items-end p-4 bg-gray-50 dark:bg-gray-900 rounded-md">
                                <div class="col-span-12 md:col-span-5">
                                    <x-input-label :for="'items.' . $index . '.description'" :value="__('Description')" />
                                    <x-text-input :id="'items.' . $index . '.description'" :name="'items[' . $index . '][description]'" type="text" class="mt-1 block w-full" :value="old('items.' . $index . '.description', $item->description)" required />
                                    <x-input-error :messages="$errors->get('items.' . $index . '.description')" class="mt-2" />
                                </div>
                                <div class="col-span-6 md:col-span-2">
                                    <x-input-label :for="'items.' . $index . '.quantity'" :value="__('Quantity')" />
                                    <x-text-input :id="'items.' . $index . '.quantity'" :name="'items[' . $index . '][quantity]'" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('items.' . $index . '.quantity', $item->quantity)" required />
                                    <x-input-error :messages="$errors->get('items.' . $index . '.quantity')" class="mt-2" />
                                </div>
                                <div class="col-span-6 md:col-span-3">
                                    <x-input-label :for="'items.' . $index . '.unit_price'" :value="__('Unit Price (N$)')" />
                                    <x-text-input :id="'items.' . $index . '.unit_price'" :name="'items[' . $index . '][unit_price]'" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('items.' . $index . '.unit_price', $item->unit_price)" required />
                                    <x-input-error :messages="$errors->get('items.' . $index . '.unit_price')" class="mt-2" />
                                </div>
                                <div class="col-span-12 md:col-span-2 flex items-end">
                                    <button type="button" class="removeItem bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm w-full {{ $invoice->items->count() <= 1 ? 'hidden' : '' }}">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <x-input-label for="notes" :value="__('Notes (Optional)')" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">{{ old('notes', $invoice->notes) }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('invoices.show', $invoice) }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                            Cancel
                        </a>
                        <x-primary-button>
                            {{ __('Update Invoice') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        let itemIndex = {{ $invoice->items->count() }};

        document.getElementById('addItem').addEventListener('click', function() {
            const container = document.getElementById('itemsContainer');
            const newRow = document.createElement('div');
            newRow.className = 'item-row grid grid-cols-12 gap-4 items-end p-4 bg-gray-50 dark:bg-gray-900 rounded-md';
            newRow.innerHTML = `
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <input type="text" name="items[${itemIndex}][description]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" required />
                </div>
                <div class="col-span-6 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                    <input type="number" step="0.01" min="0.01" name="items[${itemIndex}][quantity]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" value="1" required />
                </div>
                <div class="col-span-6 md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price (N$)</label>
                    <input type="number" step="0.01" min="0" name="items[${itemIndex}][unit_price]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" required />
                </div>
                <div class="col-span-12 md:col-span-2 flex items-end">
                    <button type="button" class="removeItem bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm w-full">
                        Remove
                    </button>
                </div>
            `;
            container.appendChild(newRow);
            itemIndex++;
            updateRemoveButtons();
        });

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.item-row');
            rows.forEach((row, index) => {
                const removeBtn = row.querySelector('.removeItem');
                if (rows.length > 1) {
                    removeBtn.classList.remove('hidden');
                } else {
                    removeBtn.classList.add('hidden');
                }
            });
        }

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('removeItem')) {
                e.target.closest('.item-row').remove();
                updateRemoveButtons();
            }
        });

        updateRemoveButtons();
        }); // End DOMContentLoaded
    </script>
    @endpush
</x-app-layout>
