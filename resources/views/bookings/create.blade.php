<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create Booking') }}
            </h2>
            <a href="{{ route('bookings.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('bookings.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Trailer Selection -->
                        <div>
                            <x-input-label for="trailer_id" :value="__('Trailer')" />
                            <select id="trailer_id" name="trailer_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" required>
                                <option value="">Select Trailer</option>
                                @foreach($trailers as $trailer)
                                <option value="{{ $trailer->id }}" {{ old('trailer_id', request('trailer_id')) == $trailer->id ? 'selected' : '' }}>
                                    {{ $trailer->name }} - {{ $trailer->type }} (N${{ number_format($trailer->rate_per_day, 2) }}/day)
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('trailer_id')" class="mt-2" />
                        </div>

                        <!-- Customer Selection -->
                        <div>
                            <x-input-label for="customer_id" :value="__('Customer')" />
                            <select id="customer_id" name="customer_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} @if($customer->email)({{ $customer->email }})@endif
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                        </div>

                        <!-- Start Date -->
                        <div>
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date')" required />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        <!-- End Date -->
                        <div>
                            <x-input-label for="end_date" :value="__('End Date')" />
                            <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date')" required />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                        </div>

                        <!-- Pickup Time -->
                        <div>
                            <x-input-label for="pickup_time" :value="__('Pickup Time (Optional)')" />
                            <x-text-input id="pickup_time" name="pickup_time" type="time" class="mt-1 block w-full" :value="old('pickup_time')" />
                            <x-input-error :messages="$errors->get('pickup_time')" class="mt-2" />
                        </div>

                        <!-- WhatsApp Number -->
                        <div>
                            <x-input-label for="whatsapp_number" :value="__('WhatsApp Number (Optional)')" />
                            <x-text-input id="whatsapp_number" name="whatsapp_number" type="text" class="mt-1 block w-full" :value="old('whatsapp_number')" placeholder="e.g., +264811234567" />
                            <x-input-error :messages="$errors->get('whatsapp_number')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Include country code (e.g., +264 for Namibia)</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Delivery Fee -->
                        <div>
                            <x-input-label for="delivery_fee" :value="__('Delivery Fee (N$)')" />
                            <x-text-input id="delivery_fee" name="delivery_fee" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('delivery_fee', 0)" />
                            <x-input-error :messages="$errors->get('delivery_fee')" class="mt-2" />
                        </div>

                        <!-- Straps Fee -->
                        <div>
                            <x-input-label for="straps_fee" :value="__('Straps Fee (N$)')" />
                            <x-text-input id="straps_fee" name="straps_fee" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('straps_fee', 0)" />
                            <x-input-error :messages="$errors->get('straps_fee')" class="mt-2" />
                        </div>

                        <!-- Damage Waiver Fee -->
                        <div>
                            <x-input-label for="damage_waiver_fee" :value="__('Damage Waiver Fee (N$)')" />
                            <x-text-input id="damage_waiver_fee" name="damage_waiver_fee" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('damage_waiver_fee', 0)" />
                            <x-input-error :messages="$errors->get('damage_waiver_fee')" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>

                    @if(session('error'))
                    <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button>
                            {{ __('Create Booking') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
