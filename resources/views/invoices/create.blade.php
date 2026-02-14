<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create Invoice') }}
            </h2>
            <a href="{{ route('invoices.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Booking Selection -->
                        <div>
                            <x-input-label for="booking_id" :value="__('Booking (Optional)')" />
                            <select id="booking_id" name="booking_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                <option value="">Select Booking (Optional)</option>
                                @foreach($bookings as $b)
                                <option value="{{ $b->id }}" {{ old('booking_id', $booking?->id) == $b->id ? 'selected' : '' }}>
                                    {{ $b->booking_number }} - {{ $b->customer->name }} ({{ $b->trailer->name }})
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('booking_id')" class="mt-2" />
                        </div>

                        <!-- Customer Selection -->
                        <div>
                            <x-input-label for="customer_id" :value="__('Customer (Required if no booking)')" />
                            <select id="customer_id" name="customer_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} @if($customer->email)({{ $customer->email }})@endif
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                        </div>

                        <!-- Invoice Type -->
                        <div>
                            <x-input-label for="type" :value="__('Invoice Type')" />
                            <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" required>
                                <option value="rental" {{ old('type') === 'rental' ? 'selected' : '' }}>Rental</option>
                                <option value="damage" {{ old('type') === 'damage' ? 'selected' : '' }}>Damage</option>
                                <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Invoice Date -->
                        <div>
                            <x-input-label for="invoice_date" :value="__('Invoice Date')" />
                            <x-text-input id="invoice_date" name="invoice_date" type="date" class="mt-1 block w-full" :value="old('invoice_date', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('invoice_date')" class="mt-2" />
                        </div>

                        <!-- Due Date -->
                        <div>
                            <x-input-label for="due_date" :value="__('Due Date')" />
                            <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" :value="old('due_date', date('Y-m-d', strtotime('+30 days')))" required />
                            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                        </div>

                        <!-- Tax Rate -->
                        <div>
                            <x-input-label for="tax_rate" :value="__('Tax Rate (%)')" />
                            <x-text-input id="tax_rate" name="tax_rate" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full" :value="old('tax_rate', \App\Models\Setting::get('tax_rate', 0))" />
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
                            <div class="item-row grid grid-cols-12 gap-4 items-end p-4 bg-gray-50 dark:bg-gray-900 rounded-md">
                                <div class="col-span-12 md:col-span-5">
                                    <x-input-label for="items.0.description" :value="__('Description')" />
                                    <x-text-input id="items.0.description" name="items[0][description]" type="text" class="mt-1 block w-full" value="{{ old('items.0.description') }}" required />
                                    <x-input-error :messages="$errors->get('items.0.description')" class="mt-2" />
                                </div>
                                <div class="col-span-6 md:col-span-2">
                                    <x-input-label for="items.0.quantity" :value="__('Quantity')" />
                                    <x-text-input id="items.0.quantity" name="items[0][quantity]" type="number" step="0.01" min="0.01" class="mt-1 block w-full" value="{{ old('items.0.quantity', 1) }}" required />
                                    <x-input-error :messages="$errors->get('items.0.quantity')" class="mt-2" />
                                </div>
                                <div class="col-span-6 md:col-span-3">
                                    <x-input-label for="items.0.unit_price" :value="__('Unit Price (N$)')" />
                                    <x-text-input id="items.0.unit_price" name="items[0][unit_price]" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('items.0.unit_price') }}" required />
                                    <x-input-error :messages="$errors->get('items.0.unit_price')" class="mt-2" />
                                </div>
                                <div class="col-span-12 md:col-span-2 flex items-end">
                                    <button type="button" class="removeItem bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm w-full hidden">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <x-input-label for="notes" :value="__('Notes (Optional)')" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end">
                        <x-primary-button>
                            {{ __('Create Invoice') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        let itemIndex = 1;

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

        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Helper function to create item row
        function createItemRow(index, description, quantity, unitPrice) {
            const row = document.createElement('div');
            row.className = 'item-row grid grid-cols-12 gap-4 items-end p-4 bg-gray-50 dark:bg-gray-900 rounded-md';
            
            const descEscaped = escapeHtml(description || '');
            const qty = quantity || 1;
            const price = unitPrice || 0;
            
            row.innerHTML = `
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <input type="text" name="items[${index}][description]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" value="${descEscaped}" required />
                </div>
                <div class="col-span-6 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                    <input type="number" step="0.01" min="0.01" name="items[${index}][quantity]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" value="${qty}" required />
                </div>
                <div class="col-span-6 md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price (N$)</label>
                    <input type="number" step="0.01" min="0" name="items[${index}][unit_price]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" value="${price}" required />
                </div>
                <div class="col-span-12 md:col-span-2 flex items-end">
                    <button type="button" class="removeItem bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm w-full ${index === 0 ? 'hidden' : ''}">
                        Remove
                    </button>
                </div>
            `;
            return row;
        }

        // Auto-populate customer and items when booking is selected
        const bookingSelect = document.getElementById('booking_id');
        const customerSelect = document.getElementById('customer_id');
        const itemsContainer = document.getElementById('itemsContainer');
        
        if (bookingSelect) {
            bookingSelect.addEventListener('change', function() {
                const bookingId = this.value;
                
                if (bookingId) {
                    // Disable customer selection and show loading state
                    customerSelect.disabled = true;
                    customerSelect.style.opacity = '0.6';
                    
                    // Show loading indicator
                    const loadingMsg = document.createElement('div');
                    loadingMsg.id = 'loadingMsg';
                    loadingMsg.className = 'mb-4 p-3 bg-blue-50 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-md';
                    loadingMsg.textContent = 'Loading booking details...';
                    itemsContainer.parentElement.insertBefore(loadingMsg, itemsContainer);
                    
                    // Fetch booking details via AJAX
                    fetch(`/api/bookings/${bookingId}/invoice-data`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Remove loading message
                            const loadingMsgEl = document.getElementById('loadingMsg');
                            if (loadingMsgEl) {
                                loadingMsgEl.remove();
                            }
                            
                            // Set customer
                            if (data.customer_id) {
                                customerSelect.value = data.customer_id;
                            }
                            
                            // Clear existing items
                            itemsContainer.innerHTML = '';
                            itemIndex = 0;
                            
                            // Populate items from booking
                            if (data.items && data.items.length > 0) {
                                data.items.forEach((item) => {
                                    const newRow = createItemRow(
                                        itemIndex,
                                        item.description || '',
                                        item.quantity || 1,
                                        item.unit_price || 0
                                    );
                                    itemsContainer.appendChild(newRow);
                                    itemIndex++;
                                });
                            } else {
                                // If no items, add one empty row
                                const newRow = createItemRow(itemIndex, '', 1, 0);
                                itemsContainer.appendChild(newRow);
                                itemIndex++;
                            }
                            
                            updateRemoveButtons();
                            
                            // Show success message
                            const successMsg = document.createElement('div');
                            successMsg.className = 'mb-4 p-3 bg-green-50 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-md';
                            successMsg.textContent = 'Booking details loaded successfully!';
                            itemsContainer.parentElement.insertBefore(successMsg, itemsContainer);
                            setTimeout(() => successMsg.remove(), 3000);
                        })
                        .catch(error => {
                            console.error('Error fetching booking data:', error);
                            
                            // Remove loading message
                            const loadingMsgEl = document.getElementById('loadingMsg');
                            if (loadingMsgEl) {
                                loadingMsgEl.remove();
                            }
                            
                            // Show error message
                            const errorMsg = document.createElement('div');
                            errorMsg.className = 'mb-4 p-3 bg-red-50 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-md';
                            errorMsg.textContent = 'Error loading booking details. Please try again.';
                            itemsContainer.parentElement.insertBefore(errorMsg, itemsContainer);
                            setTimeout(() => errorMsg.remove(), 5000);
                            
                            customerSelect.disabled = false;
                            customerSelect.style.opacity = '1';
                        });
                } else {
                    // Enable customer selection and clear items
                    customerSelect.disabled = false;
                    customerSelect.style.opacity = '1';
                    customerSelect.value = '';
                    
                    // Clear items and add one empty row
                    itemsContainer.innerHTML = '';
                    itemIndex = 0;
                    const newRow = createItemRow(itemIndex, '', 1, 0);
                    itemsContainer.appendChild(newRow);
                    itemIndex++;
                    updateRemoveButtons();
                }
            });
        }

        // Initialize: If booking is pre-selected, populate items
        if (bookingSelect && bookingSelect.value) {
            bookingSelect.dispatchEvent(new Event('change'));
        }
        updateRemoveButtons();
        }); // End DOMContentLoaded
    </script>
    @endpush
</x-app-layout>
