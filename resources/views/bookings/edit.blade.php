<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Booking') }}
            </h2>
            <a href="{{ route('bookings.show', $booking) }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('bookings.update', $booking) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="trailer_id" :value="__('Trailer')" />
                            <select id="trailer_id" name="trailer_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" required>
                                @foreach($trailers as $trailer)
                                <option value="{{ $trailer->id }}" {{ old('trailer_id', $booking->trailer_id) == $trailer->id ? 'selected' : '' }}>
                                    {{ $trailer->name }} - {{ $trailer->type }} (N${{ number_format($trailer->rate_per_day, 2) }}/day)
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('trailer_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', $booking->start_date->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="end_date" :value="__('End Date')" />
                            <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date', $booking->end_date->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="pickup_time" :value="__('Pickup Time (Optional)')" />
                            <x-text-input id="pickup_time" name="pickup_time" type="time" class="mt-1 block w-full" :value="old('pickup_time', $booking->pickup_time ? \Carbon\Carbon::parse($booking->pickup_time)->format('H:i') : '')" />
                            <x-input-error :messages="$errors->get('pickup_time')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="whatsapp_number" :value="__('WhatsApp Number (Optional)')" />
                            <x-text-input id="whatsapp_number" name="whatsapp_number" type="text" class="mt-1 block w-full" :value="old('whatsapp_number', $booking->whatsapp_number)" placeholder="e.g., +264811234567" />
                            <x-input-error :messages="$errors->get('whatsapp_number')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Include country code (e.g., +264 for Namibia)</p>
                        </div>

                        <div>
                            <x-input-label for="delivery_fee" :value="__('Delivery Fee (N$)')" />
                            <x-text-input id="delivery_fee" name="delivery_fee" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('delivery_fee', $booking->delivery_fee)" />
                            <x-input-error :messages="$errors->get('delivery_fee')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="straps_fee" :value="__('Straps Fee (N$)')" />
                            <x-text-input id="straps_fee" name="straps_fee" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('straps_fee', $booking->straps_fee)" />
                            <x-input-error :messages="$errors->get('straps_fee')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="damage_waiver_fee" :value="__('Damage Waiver Fee (N$)')" />
                            <x-text-input id="damage_waiver_fee" name="damage_waiver_fee" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('damage_waiver_fee', $booking->damage_waiver_fee)" />
                            <x-input-error :messages="$errors->get('damage_waiver_fee')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">{{ old('notes', $booking->notes) }}</textarea>
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
                            {{ __('Update Booking') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
