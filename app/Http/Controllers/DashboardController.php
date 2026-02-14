<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Trailer;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isCustomer()) {
            return $this->customerDashboard($user);
        }

        return $this->adminDashboard();
    }

    private function adminDashboard()
    {
        $today = now();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        // Revenue this month
        $revenueThisMonth = Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Active bookings
        $activeBookings = Booking::where('status', 'active')->count();

        // Pending bookings
        $pendingBookings = Booking::where('status', 'pending')->count();

        // Total trailers
        $totalTrailers = Trailer::count();
        $availableTrailers = Trailer::where('status', 'available')->count();

        // Recent bookings
        $recentBookings = Booking::with(['trailer', 'customer'])
            ->latest()
            ->limit(10)
            ->get();

        // Revenue chart data (last 6 months)
        $dbDriver = config('database.default');
        $dateFormat = $dbDriver === 'sqlite' 
            ? "strftime('%Y-%m', payment_date) as month"
            : "DATE_FORMAT(payment_date, '%Y-%m') as month";
        
        $revenueData = Payment::select(
                DB::raw($dateFormat),
                DB::raw('SUM(amount) as total')
            )
            ->where('payment_date', '>=', $today->copy()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top customers by spend
        $topCustomers = Customer::with(['bookings.payments'])
            ->get()
            ->map(function ($customer) {
                $customer->total_spent = $customer->bookings->sum(function ($booking) {
                    return $booking->payments->sum('amount');
                });
                return $customer;
            })
            ->sortByDesc('total_spent')
            ->take(5);

        // Trailer utilization
        $trailerUtilization = Trailer::withCount(['bookings' => function ($query) {
            $query->where('status', '!=', 'cancelled');
        }])->get();

        // Additional stats
        $totalBookings = Booking::count();
        $totalCustomers = Customer::count();
        $totalInvoices = \App\Models\Invoice::count();
        $pendingInvoices = \App\Models\Invoice::where('status', 'pending')->count();
        $overdueInvoices = \App\Models\Invoice::where('status', 'overdue')->count();
        
        // Revenue last month for comparison
        $lastMonthStart = $today->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $today->copy()->subMonth()->endOfMonth();
        $revenueLastMonth = Payment::whereBetween('payment_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');
        $revenueChange = $revenueLastMonth > 0 
            ? (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100 
            : 0;

        // Today's pickups and returns
        $todayPickups = Booking::where('start_date', $today->toDateString())
            ->whereIn('status', ['confirmed', 'active'])
            ->count();
        $todayReturns = Booking::where('end_date', $today->toDateString())
            ->whereIn('status', ['active', 'returned'])
            ->count();

        return view('dashboard.index', compact(
            'revenueThisMonth',
            'revenueLastMonth',
            'revenueChange',
            'activeBookings',
            'pendingBookings',
            'totalBookings',
            'totalTrailers',
            'availableTrailers',
            'totalCustomers',
            'totalInvoices',
            'pendingInvoices',
            'overdueInvoices',
            'todayPickups',
            'todayReturns',
            'recentBookings',
            'revenueData',
            'topCustomers',
            'trailerUtilization'
        ));
    }

    private function customerDashboard($user)
    {
        $customer = $user->customer;

        if (!$customer) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please complete your customer profile.');
        }

        $bookings = Booking::where('customer_id', $customer->id)
            ->with('trailer')
            ->latest()
            ->get();

        $upcomingBookings = $bookings->where('status', 'confirmed')
            ->where('start_date', '>=', now());

        $activeBooking = $bookings->where('status', 'active')->first();

        $totalSpent = Payment::whereHas('booking', function ($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })->sum('amount');

        return view('dashboard.customer', compact(
            'bookings',
            'upcomingBookings',
            'activeBooking',
            'totalSpent'
        ));
    }
}
