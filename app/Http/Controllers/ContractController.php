<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Contract;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ContractController extends Controller
{
    /**
     * Generate a contract for a booking (creates record if not exists).
     */
    public function generate(Booking $booking)
    {
        $this->authorize('view', $booking);

        $contract = $booking->contract;
        if (!$contract) {
            $contract = Contract::create([
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'contract_date' => now()->toDateString(),
                'status' => 'draft',
                'terms_conditions' => \App\Models\Setting::get('contract_terms', $this->defaultTerms()),
            ]);
        }

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Contract ' . $contract->contract_number . ' generated. You can download it below.');
    }

    /**
     * Download contract as PDF.
     */
    public function download(Contract $contract)
    {
        $this->authorize('view', $contract->booking);

        $contract->load(['booking.trailer', 'customer']);
        $booking = $contract->booking;

        $companyName = \App\Models\Setting::get('company_name', 'IronAxel Rentals');
        $companyAddress = \App\Models\Setting::get('company_address', 'Kransneus, Namibia');
        $companyPhone = \App\Models\Setting::get('company_phone', '');
        $companyEmail = \App\Models\Setting::get('company_email', 'bookings@ironaxelrentals.com');
        $companyRegistrationNo = \App\Models\Setting::get('company_registration_no', '');
        $lateReturnFee = \App\Models\Setting::get('late_return_fee', '');
        $cleaningFee = \App\Models\Setting::get('cleaning_fee', '');
        $trailerReplacementValue = \App\Models\Setting::get('trailer_replacement_value', '');
        $maxLoadCapacity = \App\Models\Setting::get('max_load_capacity', '');

        $pdf = Pdf::loadView('contracts.pdf', compact(
            'contract',
            'booking',
            'companyName',
            'companyAddress',
            'companyPhone',
            'companyEmail',
            'companyRegistrationNo',
            'lateReturnFee',
            'cleaningFee',
            'trailerReplacementValue',
            'maxLoadCapacity'
        ));

        return $pdf->download("contract-{$contract->contract_number}.pdf");
    }

    /**
     * Generate contract if needed and download PDF for a booking.
     */
    public function downloadForBooking(Booking $booking)
    {
        $this->authorize('view', $booking);

        $contract = $booking->contract;
        if (!$contract) {
            $contract = Contract::create([
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'contract_date' => now()->toDateString(),
                'status' => 'draft',
                'terms_conditions' => \App\Models\Setting::get('contract_terms', $this->defaultTerms()),
            ]);
        }

        return $this->download($contract);
    }

    private function defaultTerms(): string
    {
        return "1. RENTAL PERIOD: The Lessee agrees to return the trailer on or before the end date. Late returns may incur additional charges.
2. CONDITION: The trailer shall be returned in the same condition as at pickup, subject to fair wear and tear. The Lessee is responsible for any damage or loss.
3. DEPOSIT: A deposit may be required and will be refunded after inspection upon return, subject to deductions for damage or late fees.
4. USE: The trailer shall be used only for lawful purposes. The Lessee shall not sublet or assign this agreement.
5. INSURANCE: The Lessee is responsible for any loss or damage to the trailer during the rental period unless otherwise agreed in writing.
6. INDEMNITY: The Lessee agrees to indemnify IronAxel Rentals against any claims arising from use of the trailer.
7. CANCELLATION: Cancellation terms as per booking policy. Deposits may be non-refundable if cancelled within a specified period.
8. GOVERNING LAW: This agreement is governed by the laws of Namibia.";
    }
}
