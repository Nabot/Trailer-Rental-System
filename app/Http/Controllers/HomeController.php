<?php

namespace App\Http\Controllers;

use App\Models\Trailer;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Public homepage: trailers and availability.
     */
    public function index(Request $request)
    {
        // Authenticated users can be redirected to dashboard when visiting /
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        $trailers = Trailer::where('status', 'available')
            ->with(['photos' => fn ($q) => $q->orderBy('is_primary', 'desc')->orderBy('order')])
            ->orderBy('name')
            ->get();

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // If date range provided, add availability for each trailer
        if ($startDate && $endDate) {
            $trailers->each(function (Trailer $trailer) use ($startDate, $endDate) {
                $trailer->available_for_dates = $trailer->isAvailableForDates($startDate, $endDate);
            });
        } else {
            $trailers->each(fn (Trailer $trailer) => $trailer->available_for_dates = null);
        }

        $companyName = \App\Models\Setting::get('company_name', config('app.name', 'IronAxle Trailers'));

        return view('home', compact('trailers', 'startDate', 'endDate', 'companyName'));
    }
}
