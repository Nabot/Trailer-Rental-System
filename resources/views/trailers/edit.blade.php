<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Trailer') }}
            </h2>
            <a href="{{ route('trailers.show', $trailer) }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('trailers.update', $trailer) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Trailer Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $trailer->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="type" :value="__('Type')" />
                            <x-text-input id="type" name="type" type="text" class="mt-1 block w-full" :value="old('type', $trailer->type)" required />
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="axle" :value="__('Axle')" />
                            <select id="axle" name="axle" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" required>
                                <option value="Single" {{ old('axle', $trailer->axle) === 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Double" {{ old('axle', $trailer->axle) === 'Double' ? 'selected' : '' }}>Double</option>
                            </select>
                            <x-input-error :messages="$errors->get('axle')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="size_m" :value="__('Size (meters)')" />
                            <x-text-input id="size_m" name="size_m" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('size_m', $trailer->size_m)" required />
                            <x-input-error :messages="$errors->get('size_m')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="rate_per_day" :value="__('Rate per Day (N$)')" />
                            <x-text-input id="rate_per_day" name="rate_per_day" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('rate_per_day', $trailer->rate_per_day)" required />
                            <x-input-error :messages="$errors->get('rate_per_day')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="required_deposit" :value="__('Required Deposit (N$)')" />
                            <x-text-input id="required_deposit" name="required_deposit" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('required_deposit', $trailer->required_deposit)" />
                            <x-input-error :messages="$errors->get('required_deposit')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" required>
                                <option value="available" {{ old('status', $trailer->status) === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="maintenance" {{ old('status', $trailer->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="unavailable" {{ old('status', $trailer->status) === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="registration_number" :value="__('Registration Number')" />
                            <x-text-input id="registration_number" name="registration_number" type="text" class="mt-1 block w-full" :value="old('registration_number', $trailer->registration_number)" />
                            <x-input-error :messages="$errors->get('registration_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="colour" :value="__('Colour')" />
                            <x-text-input id="colour" name="colour" type="text" class="mt-1 block w-full" :value="old('colour', $trailer->colour)" placeholder="e.g. Silver, White" />
                            <x-input-error :messages="$errors->get('colour')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="load_capacity_kg" :value="__('Load Capacity (kg)')" />
                            <x-text-input id="load_capacity_kg" name="load_capacity_kg" type="number" min="0" step="1" class="mt-1 block w-full" :value="old('load_capacity_kg', $trailer->load_capacity_kg)" />
                            <x-input-error :messages="$errors->get('load_capacity_kg')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="trailer_value" :value="__('Trailer Value (N$)')" />
                            <x-text-input id="trailer_value" name="trailer_value" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('trailer_value', $trailer->trailer_value)" />
                            <x-input-error :messages="$errors->get('trailer_value')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">{{ old('description', $trailer->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600">{{ old('notes', $trailer->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button>
                            {{ __('Update Trailer') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
