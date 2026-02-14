<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Booking Calendar') }}
            </h2>
            <div class="flex gap-2">
                @can('bookings.create')
                <a href="{{ route('bookings.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md">
                    New Booking
                </a>
                @endcan
                <a href="{{ route('bookings.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">
                    List View
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6 p-4">
                <form method="GET" class="flex gap-4 flex-wrap items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Trailer</label>
                        <select name="trailer_id" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                            <option value="">All Trailers</option>
                            @foreach($trailers as $trailer)
                            <option value="{{ $trailer->id }}" {{ $trailerId == $trailer->id ? 'selected' : '' }}>
                                {{ $trailer->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                        <input type="month" name="month" value="{{ $year }}-{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600">
                    </div>
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md h-fit transition-colors">Filter</button>
                    <div class="flex gap-2">
                        <a href="{{ route('bookings.calendar', ['month' => $startDate->copy()->subMonth()->format('Y-m'), 'trailer_id' => $trailerId]) }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md h-fit transition-colors">Previous</a>
                        <a href="{{ route('bookings.calendar', ['trailer_id' => $trailerId]) }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md h-fit transition-colors">Today</a>
                        <a href="{{ route('bookings.calendar', ['month' => $startDate->copy()->addMonth()->format('Y-m'), 'trailer_id' => $trailerId]) }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md h-fit transition-colors">Next</a>
                    </div>
                </form>
            </div>

            <!-- Calendar -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 overflow-hidden">
                <h3 class="text-xl font-bold mb-6 text-center text-gray-900 dark:text-gray-100">{{ $startDate->format('F Y') }}</h3>
                
                <!-- Calendar Grid Header -->
                <div class="calendar-header gap-0 mb-2 border-b-2 border-gray-300 dark:border-gray-600 pb-2">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="text-center font-semibold text-gray-600 dark:text-gray-400 p-2 text-sm uppercase tracking-wide">
                        {{ $day }}
                    </div>
                    @endforeach
                </div>

                <!-- Calendar Days Grid -->
                <div class="calendar-grid-wrapper border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    @php
                    $firstDay = $startDate->copy()->startOfMonth();
                    $lastDay = $startDate->copy()->endOfMonth();
                    $startDay = $firstDay->copy()->startOfWeek();
                    $endDay = $lastDay->copy()->endOfWeek();
                    $currentDay = $startDay->copy();
                    @endphp

                    @while($currentDay->lte($endDay))
                    @php
                    $dateKey = $currentDay->format('Y-m-d');
                    $isCurrentMonth = $currentDay->month == $month;
                    $isToday = $currentDay->isToday();
                    $dayBookings = $calendarBookings[$dateKey] ?? [];
                    @endphp
                    
                    <div class="calendar-day-cell transition-colors
                        {{ !$isCurrentMonth ? 'bg-gray-50 dark:bg-gray-900/30' : 'bg-white dark:bg-gray-800' }} 
                        {{ $isToday ? 'today ring-2 ring-blue-500 dark:ring-blue-400' : '' }}
                        hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <div class="flex items-center justify-between mb-1">
                            <div class="text-sm font-semibold 
                                {{ !$isCurrentMonth ? 'text-gray-400 dark:text-gray-600' : 'text-gray-900 dark:text-gray-100' }} 
                                {{ $isToday ? 'text-blue-600 dark:text-blue-400 font-bold' : '' }}">
                                {{ $currentDay->day }}
                            </div>
                            @if($isToday)
                            <div class="w-2 h-2 bg-blue-500 dark:bg-blue-400 rounded-full"></div>
                            @endif
                        </div>
                        <div class="space-y-1 overflow-y-auto max-h-[90px]">
                            @foreach(array_slice($dayBookings, 0, 3) as $booking)
                            <a href="{{ route('bookings.show', $booking) }}" 
                               class="block text-xs p-1.5 rounded truncate transition-colors font-medium border
                               @if($booking->status === 'confirmed') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-800 border-green-200 dark:border-green-700
                               @elseif($booking->status === 'active') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800 border-blue-200 dark:border-blue-700
                               @elseif($booking->status === 'pending') bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 hover:bg-yellow-200 dark:hover:bg-yellow-800 border-yellow-200 dark:border-yellow-700
                               @elseif($booking->status === 'returned') bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 hover:bg-purple-200 dark:hover:bg-purple-800 border-purple-200 dark:border-purple-700
                               @elseif($booking->status === 'cancelled') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 hover:bg-red-200 dark:hover:bg-red-800 border-red-200 dark:border-red-700
                               @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 border-gray-200 dark:border-gray-600
                               @endif"
                               title="{{ $booking->booking_number }} - {{ $booking->trailer->name }} ({{ ucfirst($booking->status) }})">
                                <div class="truncate font-semibold">{{ $booking->trailer->name }}</div>
                                <div class="text-xs opacity-75 truncate">{{ $booking->customer->name }}</div>
                            </a>
                            @endforeach
                            @if(count($dayBookings) > 3)
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium pt-1 px-1.5">
                                +{{ count($dayBookings) - 3 }} more
                            </div>
                            @endif
                        </div>
                    </div>
                    @php $currentDay->addDay(); @endphp
                    @endwhile
                </div>

                <!-- Legend -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 text-center">Status Legend</h4>
                    <div class="flex flex-wrap gap-4 justify-center">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-green-100 dark:bg-green-900 border border-green-300 dark:border-green-700 rounded"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Confirmed</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-blue-100 dark:bg-blue-900 border border-blue-300 dark:border-blue-700 rounded"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-yellow-100 dark:bg-yellow-900 border border-yellow-300 dark:border-yellow-700 rounded"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Pending</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-purple-100 dark:bg-purple-900 border border-purple-300 dark:border-purple-700 rounded"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Returned</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Cancelled</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Ensure calendar grid displays correctly with proper CSS */
        .calendar-grid-wrapper {
            display: grid !important;
            grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
            gap: 0 !important;
        }
        
        /* Calendar header grid */
        .calendar-header {
            display: grid !important;
            grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
        }
        
        /* Calendar day cells */
        .calendar-day-cell {
            min-height: 120px;
            border-right: 1px solid rgb(229 231 235);
            border-bottom: 1px solid rgb(229 231 235);
            padding: 0.5rem;
            position: relative;
        }
        
        /* Dark mode borders */
        .dark .calendar-day-cell {
            border-right-color: rgb(55 65 81);
            border-bottom-color: rgb(55 65 81);
        }
        
        /* Remove right border from last column */
        .calendar-day-cell:nth-child(7n) {
            border-right: none;
        }
        
        /* Remove bottom border from last row */
        .calendar-day-cell:nth-last-child(-n+7) {
            border-bottom: none;
        }
        
        /* Today highlight */
        .calendar-day-cell.today {
            background-color: rgb(239 246 255);
            border-color: rgb(59 130 246);
        }
        
        .dark .calendar-day-cell.today {
            background-color: rgba(30, 58, 138, 0.3);
            border-color: rgb(96 165 250);
        }
        
        /* Responsive calendar */
        @media (max-width: 768px) {
            .calendar-day-cell {
                min-height: 100px;
                padding: 0.375rem;
                font-size: 0.875rem;
            }
        }
        
        @media (max-width: 640px) {
            .calendar-day-cell {
                min-height: 80px;
                padding: 0.25rem;
                font-size: 0.75rem;
            }
            
            .calendar-day-cell > div:last-child {
                max-height: 60px;
            }
        }
    </style>
    @endpush
</x-app-layout>
