<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Quote: {{ $quote->quote_number }}
            </h2>
            <a href="{{ route('quotes.show', $quote) }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        <strong>Note:</strong> Only draft quotes can be edited. Once sent, quotes cannot be modified.
                    </p>
                </div>

                <form method="POST" action="{{ route('quotes.update', $quote) }}" id="quoteForm">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Trailer Selection -->
                        <div>
                            <x-input-label for="trailer_id" :value="__('Trailer')" />
                            <select id="trailer_id" name="trailer_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600" required>
                                <option value="">Select Trailer</option>
                                @foreach($trailers as $trailer)
                                <option value="{{ $trailer->id }}" data-rate="{{ $trailer->rate_per_day }}" {{ old('trailer_id', $quote->trailer_id) == $trailer->id ? 'selected' : '' }}>
                                    {{ $trailer->name }} - {{ $trailer->type }} (N${{ number_format($trailer->rate_per_day, 2) }}/day)
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('trailer_id')" class="mt-2" />
                        </div>

                        <!-- Dates -->
                        <div>
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', $quote->start_date?->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="end_date" :value="__('End Date')" />
                            <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date', $quote->end_date?->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                        </div>

                        <!-- Rate & Fees -->
                        <div>
                            <x-input-label for="rate_per_day" :value="__('Rate Per Day (N$)')" />
                            <x-text-input id="rate_per_day" name="rate_per_day" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('rate_per_day', $quote->rate_per_day)" required />
                            <x-input-error :messages="$errors->get('rate_per_day')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="delivery_fee" :value="__('Delivery Fee (N$)')" />
                            <x-text-input id="delivery_fee" name="delivery_fee" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('delivery_fee', $quote->delivery_fee)" />
                            <x-input-error :messages="$errors->get('delivery_fee')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="straps_fee" :value="__('Straps Fee (N$)')" />
                            <x-text-input id="straps_fee" name="straps_fee" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('straps_fee', $quote->straps_fee)" />
                            <x-input-error :messages="$errors->get('straps_fee')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="damage_waiver_fee" :value="__('Damage Waiver Fee (N$)')" />
                            <x-text-input id="damage_waiver_fee" name="damage_waiver_fee" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('damage_waiver_fee', $quote->damage_waiver_fee)" />
                            <x-input-error :messages="$errors->get('damage_waiver_fee')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tax" :value="__('Tax (N$)')" />
                            <x-text-input id="tax" name="tax" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('tax', $quote->tax)" />
                            <x-input-error :messages="$errors->get('tax')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="validity_days" :value="__('Validity (Days)')" />
                            <x-text-input id="validity_days" name="validity_days" type="number" min="1" max="90" class="mt-1 block w-full" :value="old('validity_days', $quote->validity_days)" />
                            <x-input-error :messages="$errors->get('validity_days')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Notes & Terms -->
                    <div class="mb-6">
                        <x-input-label for="notes" :value="__('Notes (Optional)')" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">{{ old('notes', $quote->notes) }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="terms_conditions" :value="__('Terms & Conditions (Optional)')" />
                        <textarea id="terms_conditions" name="terms_conditions" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">{{ old('terms_conditions', $quote->terms_conditions) }}</textarea>
                        <x-input-error :messages="$errors->get('terms_conditions')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button>
                            {{ __('Update Quote') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trailerSelect = document.getElementById('trailer_id');
            const rateInput = document.getElementById('rate_per_day');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            // Auto-fill rate when trailer is selected
            trailerSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const rate = selectedOption.getAttribute('data-rate');
                if (rate) {
                    rateInput.value = rate;
                }
            });

            // Calculate days when dates change
            function calculateDays() {
                if (startDateInput.value && endDateInput.value) {
                    const start = new Date(startDateInput.value);
                    const end = new Date(endDateInput.value);
                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                }
            }

            startDateInput.addEventListener('change', calculateDays);
            endDateInput.addEventListener('change', calculateDays);
        });
    </script>
    @endpush
</x-app-layout>
