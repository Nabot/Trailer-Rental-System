<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CommissionPayout;
use App\Models\Payment;
use App\Models\Trailer;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Display reports index.
     */
    public function index()
    {
        $this->authorize('reports.view');
        return view('reports.index');
    }

    /**
     * Revenue report.
     */
    public function revenue(Request $request)
    {
        $this->authorize('reports.view');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $groupBy = $request->get('group_by', 'day'); // day, week, month, year

        $query = Payment::whereBetween('payment_date', [$startDate, $endDate]);

        // Group by period
        $dbDriver = DB::connection()->getDriverName();
        if ($dbDriver === 'sqlite') {
            $dateFormat = match($groupBy) {
                'day' => "strftime('%Y-%m-%d', payment_date)",
                'week' => "strftime('%Y-W%W', payment_date)",
                'month' => "strftime('%Y-%m', payment_date)",
                'year' => "strftime('%Y', payment_date)",
                default => "strftime('%Y-%m-%d', payment_date)",
            };
        } else {
            $dateFormat = match($groupBy) {
                'day' => "DATE_FORMAT(payment_date, '%Y-%m-%d')",
                'week' => "DATE_FORMAT(payment_date, '%Y-%u')",
                'month' => "DATE_FORMAT(payment_date, '%Y-%m')",
                'year' => "DATE_FORMAT(payment_date, '%Y')",
                default => "DATE_FORMAT(payment_date, '%Y-%m-%d')",
            };
        }

        $revenueData = $query->select(
            DB::raw("{$dateFormat} as period"),
            DB::raw('SUM(amount) as total')
        )
        ->groupBy('period')
        ->orderBy('period')
        ->get();

        $totalRevenue = Payment::whereBetween('payment_date', [$startDate, $endDate])->sum('amount');
        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $averageBookingValue = Booking::whereBetween('created_at', [$startDate, $endDate])->avg('total_amount');

        return view('reports.revenue', compact('revenueData', 'startDate', 'endDate', 'groupBy', 'totalRevenue', 'totalBookings', 'averageBookingValue'));
    }

    /**
     * Trailer utilization report.
     */
    public function utilization(Request $request)
    {
        $this->authorize('reports.view');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $trailers = Trailer::with(['bookings' => function ($query) use ($startDate, $endDate) {
            $query->where('status', '!=', 'cancelled')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q2) use ($startDate, $endDate) {
                            $q2->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                });
        }])->get();

        $totalDays = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;

        $utilizationData = $trailers->map(function ($trailer) use ($startDate, $endDate, $totalDays) {
            $rentedDays = $trailer->bookings->sum(function ($booking) use ($startDate, $endDate) {
                $bookingStart = max($booking->start_date, \Carbon\Carbon::parse($startDate));
                $bookingEnd = min($booking->end_date, \Carbon\Carbon::parse($endDate));
                return max(0, $bookingStart->diffInDays($bookingEnd) + 1);
            });

            $utilizationRate = $totalDays > 0 ? ($rentedDays / $totalDays) * 100 : 0;
            $revenue = $trailer->bookings->sum('total_amount');

            return [
                'trailer' => $trailer,
                'rented_days' => $rentedDays,
                'total_days' => $totalDays,
                'utilization_rate' => $utilizationRate,
                'revenue' => $revenue,
            ];
        })->sortByDesc('utilization_rate');

        $totalTrailers = $utilizationData->count();
        $avgUtilization = $totalTrailers > 0 ? $utilizationData->avg('utilization_rate') : 0;
        $totalRentedDays = $utilizationData->sum('rented_days');
        $totalRevenue = $utilizationData->sum('revenue');

        return view('reports.utilization', compact('utilizationData', 'startDate', 'endDate', 'totalDays', 'totalTrailers', 'avgUtilization', 'totalRentedDays', 'totalRevenue'));
    }

    /**
     * Customer report.
     */
    public function customers(Request $request)
    {
        $this->authorize('reports.view');
        $sortBy = $request->get('sort_by', 'revenue'); // revenue, bookings, balance

        $customers = Customer::with(['bookings.payments', 'invoices'])->get();

        $customerData = $customers->map(function ($customer) {
            // Calculate total revenue from all payments for this customer's bookings
            $totalRevenue = $customer->bookings->sum(function ($booking) {
                return $booking->payments->sum('amount');
            });

            $totalBookings = $customer->bookings->count();
            $outstandingBalance = $customer->invoices->sum('balance');
            
            // Calculate total spent (from bookings total_amount)
            $totalSpent = $customer->bookings->sum('total_amount');
            
            // Calculate average booking value
            $averageBookingValue = $totalBookings > 0 ? $totalSpent / $totalBookings : 0;

            return [
                'customer' => $customer,
                'total_revenue' => $totalRevenue,
                'total_spent' => $totalSpent,
                'total_bookings' => $totalBookings,
                'outstanding_balance' => $outstandingBalance,
                'average_booking_value' => $averageBookingValue,
            ];
        });

        // Sort
        $customerData = match($sortBy) {
            'revenue' => $customerData->sortByDesc('total_revenue'),
            'bookings' => $customerData->sortByDesc('total_bookings'),
            'balance' => $customerData->sortByDesc('outstanding_balance'),
            default => $customerData->sortByDesc('total_revenue'),
        };

        // Calculate summary statistics
        $totalCustomers = $customerData->count();
        $totalRevenue = $customerData->sum('total_revenue');
        $totalOutstanding = $customerData->sum('outstanding_balance');
        $totalBookings = $customerData->sum('total_bookings');

        return view('reports.customers', compact('customerData', 'sortBy', 'totalCustomers', 'totalRevenue', 'totalOutstanding', 'totalBookings'));
    }

    /**
     * Lead (inquiry) report.
     */
    public function leads(Request $request)
    {
        $this->authorize('reports.view');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $statusFilter = $request->get('status', '');
        $sourceFilter = $request->get('source', '');

        $query = Inquiry::with(['customer', 'assignedTo', 'createdBy'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }
        if ($sourceFilter !== '') {
            $query->where('source', $sourceFilter);
        }

        $leads = $query->orderByDesc('created_at')->get();

        // Summary: counts by status (for the date range)
        $byStatus = Inquiry::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $bySource = Inquiry::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('source, count(*) as count')
            ->groupBy('source')
            ->pluck('count', 'source');

        $totalLeads = Inquiry::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();
        $convertedCount = $byStatus->get('converted', 0);
        $conversionRate = $totalLeads > 0 ? round(($convertedCount / $totalLeads) * 100, 1) : 0;

        $statuses = ['new', 'contacted', 'quoted', 'follow_up', 'converted', 'lost', 'on_hold'];
        $sources = Inquiry::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->distinct()
            ->pluck('source')
            ->filter()
            ->sort()
            ->values()
            ->all();
        if (empty($sources)) {
            $sources = ['website', 'phone', 'referral', 'walk_in', 'social_media', 'google_ads', 'other'];
        }

        return view('reports.leads', compact(
            'leads',
            'startDate',
            'endDate',
            'statusFilter',
            'sourceFilter',
            'byStatus',
            'bySource',
            'totalLeads',
            'convertedCount',
            'conversionRate',
            'statuses',
            'sources'
        ));
    }

    /**
     * Outstanding balances report.
     */
    public function balances(Request $request)
    {
        $this->authorize('reports.view');
        $query = Invoice::with(['customer', 'booking'])
            ->where('balance', '>', 0);

        if ($request->has('overdue_only')) {
            $query->where('due_date', '<', now());
        }

        $invoices = $query->orderBy('due_date')->get();

        $totalOutstanding = $invoices->sum('balance');
        $overdueAmount = $invoices->where('due_date', '<', now())->sum('balance');

        // Group by customer for summary
        $byCustomer = $invoices->groupBy('customer_id')->map(function ($custInvoices) {
            $customer = $custInvoices->first()->customer;
            return [
                'customer' => $customer,
                'balance' => $custInvoices->sum('balance'),
                'invoice_count' => $custInvoices->count(),
                'oldest_due' => $custInvoices->min('due_date'),
            ];
        })->sortByDesc('balance')->values();

        return view('reports.balances', compact('invoices', 'totalOutstanding', 'overdueAmount', 'byCustomer'));
    }

    /**
     * Commission report: leads assigned to rep and % of booking value when lead converts.
     * Sales reps see only their data; admin can select another rep and record payouts.
     */
    public function commission(Request $request)
    {
        $currentUser = $request->user();
        if (!$currentUser->hasRole('sales_rep') && !$currentUser->can('reports.view')) {
            abort(403, 'You do not have access to this report.');
        }

        $commissionRate = config('app.commission_rate', 0.10);
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $salesReps = User::role('sales_rep')->orderBy('name')->get();
        $canViewOtherRep = $currentUser->can('reports.view') && $salesReps->isNotEmpty();
        if ($currentUser->hasRole('sales_rep') && !$currentUser->can('reports.view')) {
            $targetUser = $currentUser;
        } elseif ($canViewOtherRep && $request->filled('user_id')) {
            $targetUser = $salesReps->firstWhere('id', (int) $request->user_id) ?? $salesReps->first() ?? $currentUser;
        } elseif ($canViewOtherRep && $salesReps->isNotEmpty()) {
            $targetUser = $salesReps->first();
        } else {
            $targetUser = $currentUser;
        }
        $targetUserId = $targetUser->id;

        $myLeads = Inquiry::where('assigned_to', $targetUserId)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['convertedToBooking.trailer', 'customer'])
            ->orderByDesc('created_at')
            ->get();

        $convertedLeads = $myLeads->filter(function ($inquiry) {
            return $inquiry->converted_to_booking_id !== null
                && $inquiry->convertedToBooking
                && $inquiry->convertedToBooking->status !== 'cancelled';
        });
        $totalBookingValue = $convertedLeads->sum(fn ($inquiry) => (float) $inquiry->convertedToBooking->total_amount);
        $totalCommission = round($totalBookingValue * $commissionRate, 2);

        $totalPaid = (float) CommissionPayout::where('user_id', $targetUserId)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('period_start', [$startDate, $endDate])
                    ->orWhereBetween('period_end', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('period_start', '<=', $startDate)->where('period_end', '>=', $endDate);
                    });
            })
            ->sum('amount');
        $outstanding = round(max(0, $totalCommission - $totalPaid), 2);

        return view('reports.commission', [
            'user' => $targetUser,
            'currentUser' => $currentUser,
            'leads' => $myLeads,
            'convertedLeads' => $convertedLeads,
            'totalLeads' => $myLeads->count(),
            'convertedCount' => $convertedLeads->count(),
            'totalBookingValue' => $totalBookingValue,
            'commissionRate' => $commissionRate,
            'totalCommission' => $totalCommission,
            'totalPaid' => $totalPaid,
            'outstanding' => $outstanding,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'salesReps' => $salesReps,
            'canViewOtherRep' => $canViewOtherRep,
            'canRecordPayout' => $currentUser->can('reports.view') && $targetUserId !== null,
        ]);
    }

    /**
     * Record a commission payout (admin only).
     */
    public function storeCommissionPayout(Request $request)
    {
        $this->authorize('reports.view');
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'paid_at' => 'required|date',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'notes' => 'nullable|string|max:500',
        ]);
        CommissionPayout::create([
            'user_id' => $validated['user_id'],
            'amount' => $validated['amount'],
            'paid_at' => $validated['paid_at'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'notes' => $validated['notes'] ?? null,
            'recorded_by' => $request->user()->id,
        ]);
        return redirect()->route('reports.commission', [
            'user_id' => $validated['user_id'],
            'start_date' => $validated['period_start'],
            'end_date' => $validated['period_end'],
        ])->with('success', 'Commission payout recorded.');
    }

    /**
     * Export revenue report as PDF.
     */
    public function exportRevenue(Request $request)
    {
        $this->authorize('reports.view');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $revenueData = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->select(DB::raw('DATE(payment_date) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalRevenue = Payment::whereBetween('payment_date', [$startDate, $endDate])->sum('amount');

        $pdf = Pdf::loadView('reports.pdf.revenue', compact('revenueData', 'startDate', 'endDate', 'totalRevenue'));
        return $pdf->download("revenue-report-{$startDate}-to-{$endDate}.pdf");
    }
}
