<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $customer->name }}
            </h2>
            <div class="flex gap-2">
                @can('customers.edit')
                <a href="{{ route('customers.edit', $customer) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                    Edit
                </a>
                @endcan
                <a href="{{ route('customers.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Customer Details</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $customer->name }}</dd>
                            </div>
                            @if($customer->email)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $customer->email }}</dd>
                            </div>
                            @endif
                            @if($customer->phone)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $customer->phone }}</dd>
                            </div>
                            @endif
                            @if($customer->id_number)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Number</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $customer->id_number }}</dd>
                            </div>
                            @endif
                            @if($customer->driver_licence)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Driver's Licence No</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $customer->driver_licence }}</dd>
                            </div>
                            @endif
                            @if($customer->car_registration || $customer->vehicle_make || $customer->vehicle_model)
                            <div class="col-span-2 mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                                <dt class="text-sm font-medium text-gray-500 mb-1">Vehicle / Car</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                    @if($customer->car_registration)<span class="font-medium">{{ $customer->car_registration }}</span>@endif
                                    @if($customer->vehicle_make || $customer->vehicle_model)
                                        @if($customer->car_registration) — @endif
                                        {{ trim(($customer->vehicle_make ?? '') . ' ' . ($customer->vehicle_model ?? '')) }}
                                    @endif
                                </dd>
                            </div>
                            @endif
                            @if($customer->address)
                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Address</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $customer->address }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Bookings -->
                    @if($customer->bookings->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Bookings</h3>
                        <div class="space-y-3">
                            @foreach($customer->bookings as $booking)
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                                <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                    {{ $booking->booking_number }}
                                </a>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $booking->trailer->name }} • {{ $booking->start_date->format('M d') }} - {{ $booking->end_date->format('M d, Y') }}
                                </p>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status === 'active') bg-blue-100 text-blue-800
                                    @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Documents -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Documents</h3>
                            @can('customers.edit')
                            <button onclick="document.getElementById('uploadForm').classList.toggle('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                                Upload Document
                            </button>
                            @endcan
                        </div>

                        <!-- Upload Form -->
                        @can('customers.edit')
                        <form id="uploadForm" method="POST" action="{{ route('customers.upload-document', $customer) }}" enctype="multipart/form-data" class="hidden mb-4 p-4 bg-gray-50 rounded-lg">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Document Type</label>
                                    <select name="type" required class="w-full rounded-md border-gray-300">
                                        <option value="id_copy">ID Copy</option>
                                        <option value="proof_of_address">Proof of Address</option>
                                        <option value="license">License</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Document Name</label>
                                    <input type="text" name="name" required class="w-full rounded-md border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">File</label>
                                    <input type="file" name="document" required accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-md border-gray-300">
                                    <p class="text-xs text-gray-500 mt-1">Max 5MB. PDF, JPG, PNG</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Notes (Optional)</label>
                                    <textarea name="notes" rows="2" class="w-full rounded-md border-gray-300"></textarea>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">Upload</button>
                                    <button type="button" onclick="document.getElementById('uploadForm').classList.add('hidden')" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
                                </div>
                            </div>
                        </form>
                        @endcan

                        <!-- Documents List -->
                        @if($customer->documents->count() > 0)
                        <div class="space-y-2">
                            @foreach($customer->documents as $document)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium">{{ $document->name }}</p>
                                    <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $document->type)) }}</p>
                                </div>
                                <a href="{{ Storage::url($document->path) }}" target="_blank" class="text-blue-600 hover:underline text-sm">View</a>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500 text-center py-4">No documents uploaded</p>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                        <div class="space-y-2">
                            @can('bookings.create')
                            <a href="{{ route('bookings.create', ['customer_id' => $customer->id]) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded-md">
                                Create Booking
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
