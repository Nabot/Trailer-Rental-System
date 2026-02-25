<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Company Information') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="company_name" :value="__('Company Name')" />
                            <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name', $settings['company_name'])" required />
                            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="company_logo" :value="__('Logo')" />
                            @if($settings['company_logo'] && file_exists(public_path($settings['company_logo'])))
                            <div class="mt-2 mb-2">
                                <img src="{{ asset($settings['company_logo']) }}" alt="Current logo" class="h-20 w-auto object-contain border border-gray-200 dark:border-gray-600 rounded" />
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Current logo. Upload a new image to replace.</p>
                            </div>
                            @endif
                            <input id="company_logo" name="company_logo" type="file" accept="image/jpeg,image/png,image/gif,image/svg+xml,image/webp" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-orange-50 file:text-orange-700 dark:file:bg-orange-900/30 dark:file:text-orange-300 hover:file:bg-orange-100 dark:hover:file:bg-orange-900/50" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF, SVG or WebP. Max 2MB.</p>
                            <x-input-error :messages="$errors->get('company_logo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="company_registration_no" :value="__('Registration No')" />
                            <x-text-input id="company_registration_no" name="company_registration_no" type="text" class="mt-1 block w-full" :value="old('company_registration_no', $settings['company_registration_no'])" />
                            <x-input-error :messages="$errors->get('company_registration_no')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="company_phone" :value="__('Cellphone / Phone')" />
                            <x-text-input id="company_phone" name="company_phone" type="text" class="mt-1 block w-full" :value="old('company_phone', $settings['company_phone'])" />
                            <x-input-error :messages="$errors->get('company_phone')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="company_address" :value="__('Address')" />
                            <textarea id="company_address" name="company_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">{{ old('company_address', $settings['company_address']) }}</textarea>
                            <x-input-error :messages="$errors->get('company_address')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="company_email" :value="__('Email')" />
                            <x-text-input id="company_email" name="company_email" type="email" class="mt-1 block w-full" :value="old('company_email', $settings['company_email'])" />
                            <x-input-error :messages="$errors->get('company_email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="company_website" :value="__('Website')" />
                            <x-text-input id="company_website" name="company_website" type="url" class="mt-1 block w-full" :value="old('company_website', $settings['company_website'])" placeholder="https://..." />
                            <x-input-error :messages="$errors->get('company_website')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button>
                            {{ __('Save Company Information') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
