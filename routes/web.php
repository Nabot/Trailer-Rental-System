<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TrailerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Trailers
    Route::resource('trailers', TrailerController::class);

    // Customers
    Route::resource('customers', CustomerController::class);
    Route::post('/customers/{customer}/upload-document', [CustomerController::class, 'uploadDocument'])->name('customers.upload-document');

    // Bookings
    Route::get('/bookings/calendar', [BookingController::class, 'calendar'])->name('bookings.calendar');
    Route::resource('bookings', BookingController::class);
    Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/start-rental', [BookingController::class, 'startRental'])->name('bookings.start-rental');
    Route::post('/bookings/{booking}/return', [BookingController::class, 'returnTrailer'])->name('bookings.return');

    // Payments
    Route::resource('payments', PaymentController::class);
    Route::get('/payments/{payment}/download', [PaymentController::class, 'download'])->name('payments.download');
    Route::post('/payments/{payment}/send-whatsapp', [PaymentController::class, 'sendWhatsApp'])->name('payments.send-whatsapp');

    // Inspections
    Route::resource('inspections', InspectionController::class);

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::post('/invoices/{invoice}/send-whatsapp', [InvoiceController::class, 'sendWhatsApp'])->name('invoices.send-whatsapp');
    Route::get('/api/bookings/{booking}/invoice-data', [InvoiceController::class, 'getBookingInvoiceData'])->name('api.bookings.invoice-data');
    Route::post('/bookings/{booking}/generate-invoice', [InvoiceController::class, 'generateForBooking'])->name('bookings.generate-invoice');

    // Contracts
    Route::post('/bookings/{booking}/generate-contract', [ContractController::class, 'generate'])->name('bookings.contract.generate');
    Route::get('/bookings/{booking}/contract/download', [ContractController::class, 'downloadForBooking'])->name('bookings.contract.download');
    Route::get('/contracts/{contract}/download', [ContractController::class, 'download'])->name('contracts.download');

    // Inquiries (Leads)
    Route::resource('inquiries', InquiryController::class);
    Route::post('/inquiries/{inquiry}/add-activity', [InquiryController::class, 'addActivity'])->name('inquiries.add-activity');
    Route::patch('/inquiries/{inquiry}/status', [InquiryController::class, 'updateStatus'])->name('inquiries.update-status');
    Route::post('/inquiries/{inquiry}/activities/{activity}/complete', [InquiryController::class, 'completeActivity'])->name('inquiries.activities.complete');
    Route::post('/inquiries/{inquiry}/convert-to-customer', [InquiryController::class, 'convertToCustomer'])->name('inquiries.convert-to-customer');

    // Quotes
    Route::resource('quotes', QuoteController::class);
    Route::post('/quotes/{quote}/mark-as-sent', [QuoteController::class, 'markAsSent'])->name('quotes.mark-as-sent');
    Route::post('/quotes/{quote}/convert-to-booking', [QuoteController::class, 'convertToBooking'])->name('quotes.convert-to-booking');
    Route::get('/quotes/{quote}/download', [QuoteController::class, 'download'])->name('quotes.download');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/utilization', [ReportController::class, 'utilization'])->name('utilization');
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('/leads', [ReportController::class, 'leads'])->name('leads');
        Route::get('/balances', [ReportController::class, 'balances'])->name('balances');
    });

    // Customer Portal
    Route::get('/portal', function () {
        return view('customers.portal');
    })->name('portal');

    // Users (Admin only)
    Route::resource('users', UserController::class);

    // Company information (Admin only)
    Route::get('/settings/company', [SettingController::class, 'company'])->name('settings.company');
    Route::put('/settings/company', [SettingController::class, 'updateCompany'])->name('settings.company.update');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
