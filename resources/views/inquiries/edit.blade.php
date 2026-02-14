<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Lead: {{ $inquiry->inquiry_number }}
            </h2>
            <a href="{{ route('inquiries.show', $inquiry) }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('inquiries.update', $inquiry) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Source & Status -->
                        <div>
                            <x-input-label for="source" :value="__('Source')" />
                            <select id="source" name="source" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600" required>
                                <option value="phone" {{ old('source', $inquiry->source) === 'phone' ? 'selected' : '' }}>Phone</option>
                                <option value="website" {{ old('source', $inquiry->source) === 'website' ? 'selected' : '' }}>Website</option>
                                <option value="walk_in" {{ old('source', $inquiry->source) === 'walk_in' ? 'selected' : '' }}>Walk-in</option>
                                <option value="referral" {{ old('source', $inquiry->source) === 'referral' ? 'selected' : '' }}>Referral</option>
                                <option value="social_media" {{ old('source', $inquiry->source) === 'social_media' ? 'selected' : '' }}>Social Media</option>
                                <option value="google_ads" {{ old('source', $inquiry->source) === 'google_ads' ? 'selected' : '' }}>Google Ads</option>
                                <option value="other" {{ old('source', $inquiry->source) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('source')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600" required>
                                <option value="new" {{ old('status', $inquiry->status) === 'new' ? 'selected' : '' }}>New</option>
                                <option value="contacted" {{ old('status', $inquiry->status) === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                <option value="quoted" {{ old('status', $inquiry->status) === 'quoted' ? 'selected' : '' }}>Quoted</option>
                                <option value="follow_up" {{ old('status', $inquiry->status) === 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                                <option value="converted" {{ old('status', $inquiry->status) === 'converted' ? 'selected' : '' }}>Converted</option>
                                <option value="lost" {{ old('status', $inquiry->status) === 'lost' ? 'selected' : '' }}>Lost</option>
                                <option value="on_hold" {{ old('status', $inquiry->status) === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="priority" :value="__('Priority')" />
                            <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600" required>
                                <option value="high" {{ old('priority', $inquiry->priority) === 'high' ? 'selected' : '' }}>High</option>
                                <option value="medium" {{ old('priority', $inquiry->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="low" {{ old('priority', $inquiry->priority) === 'low' ? 'selected' : '' }}>Low</option>
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>

                        <!-- Customer Link -->
                        <div>
                            <x-input-label for="customer_id" :value="__('Customer')" />
                            <select id="customer_id" name="customer_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                                <option value="">-- None --</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id', $inquiry->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->phone ?? $customer->email }})
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="assigned_to" :value="__('Assign To')" />
                            <select id="assigned_to" name="assigned_to" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                                <option value="">-- Unassigned --</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to', $inquiry->assigned_to) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
                        </div>

                        <!-- Contact Information -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $inquiry->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $inquiry->email)" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="phone" :value="__('Phone')" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $inquiry->phone)" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="whatsapp_number" :value="__('WhatsApp Number')" />
                            <x-text-input id="whatsapp_number" name="whatsapp_number" type="text" class="mt-1 block w-full" :value="old('whatsapp_number', $inquiry->whatsapp_number)" />
                            <x-input-error :messages="$errors->get('whatsapp_number')" class="mt-2" />
                        </div>

                        <!-- Rental Dates -->
                        <div>
                            <x-input-label for="preferred_start_date" :value="__('Preferred Start Date')" />
                            <x-text-input id="preferred_start_date" name="preferred_start_date" type="date" class="mt-1 block w-full" :value="old('preferred_start_date', $inquiry->preferred_start_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('preferred_start_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="preferred_end_date" :value="__('Preferred End Date')" />
                            <x-text-input id="preferred_end_date" name="preferred_end_date" type="date" class="mt-1 block w-full" :value="old('preferred_end_date', $inquiry->preferred_end_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('preferred_end_date')" class="mt-2" />
                        </div>

                        <!-- Trailer Interests -->
                        <div class="md:col-span-2">
                            <x-input-label for="trailer_interests" :value="__('Interested Trailers')" />
                            <select id="trailer_interests" name="trailer_interests[]" multiple class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                                @foreach($trailers as $trailer)
                                <option value="{{ $trailer->id }}" {{ in_array($trailer->id, old('trailer_interests', $inquiry->trailer_interests ?? [])) ? 'selected' : '' }}>
                                    {{ $trailer->name }} - {{ $trailer->type }} (N${{ number_format($trailer->rate_per_day, 2) }}/day)
                                </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Hold Ctrl/Cmd to select multiple</p>
                            <x-input-error :messages="$errors->get('trailer_interests')" class="mt-2" />
                        </div>

                        <!-- Rental Purpose & Budget -->
                        <div class="md:col-span-2">
                            <x-input-label for="rental_purpose" :value="__('Rental Purpose')" />
                            <textarea id="rental_purpose" name="rental_purpose" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">{{ old('rental_purpose', $inquiry->rental_purpose) }}</textarea>
                            <x-input-error :messages="$errors->get('rental_purpose')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="budget_range" :value="__('Budget Range')" />
                            <x-text-input id="budget_range" name="budget_range" type="text" class="mt-1 block w-full" placeholder="e.g., N$500-1000" :value="old('budget_range', $inquiry->budget_range)" />
                            <x-input-error :messages="$errors->get('budget_range')" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">{{ old('notes', $inquiry->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button>
                            {{ __('Update Lead') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
