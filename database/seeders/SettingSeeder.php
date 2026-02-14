<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'default_deposit_amount',
                'value' => '1000',
                'type' => 'integer',
                'description' => 'Default deposit amount in N$ (used when trailer has no specific deposit)',
            ],
            [
                'key' => 'company_name',
                'value' => 'IronAxel Rentals',
                'type' => 'string',
                'description' => 'Company name for invoices and contracts',
            ],
            [
                'key' => 'company_address',
                'value' => 'Kransneus, Namibia',
                'type' => 'string',
                'description' => 'Company address',
            ],
            [
                'key' => 'company_phone',
                'value' => '+264 61 123 4567',
                'type' => 'string',
                'description' => 'Company phone number',
            ],
            [
                'key' => 'company_email',
                'value' => 'bookings@ironaxelrentals.com',
                'type' => 'string',
                'description' => 'Company email address',
            ],
            [
                'key' => 'tax_rate',
                'value' => '0',
                'type' => 'integer',
                'description' => 'Tax rate percentage (0-100)',
            ],
            [
                'key' => 'booking_reminder_days',
                'value' => '1',
                'type' => 'integer',
                'description' => 'Days before return date to send reminder',
            ],
            [
                'key' => 'notifications_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable email/SMS notifications',
            ],
            [
                'key' => 'bank_account_name',
                'value' => 'IronAxel Rentals',
                'type' => 'string',
                'description' => 'Bank account name for invoices',
            ],
            [
                'key' => 'bank_name',
                'value' => 'First National Bank Namibia',
                'type' => 'string',
                'description' => 'Bank name for invoices',
            ],
            [
                'key' => 'bank_account_number',
                'value' => '62114687059',
                'type' => 'string',
                'description' => 'Bank account number for invoices',
            ],
            [
                'key' => 'bank_branch_name',
                'value' => 'Private Clients',
                'type' => 'string',
                'description' => 'Bank branch name for invoices',
            ],
            [
                'key' => 'bank_branch_code',
                'value' => '280174',
                'type' => 'string',
                'description' => 'Bank branch code for invoices',
            ],
            [
                'key' => 'contract_terms',
                'value' => "1. RENTAL PERIOD: The Lessee agrees to return the trailer on or before the end date. Late returns may incur additional charges.\n2. CONDITION: The trailer shall be returned in the same condition as at pickup, subject to fair wear and tear. The Lessee is responsible for any damage or loss.\n3. DEPOSIT: A deposit may be required and will be refunded after inspection upon return, subject to deductions for damage or late fees.\n4. USE: The trailer shall be used only for lawful purposes. The Lessee shall not sublet or assign this agreement.\n5. INSURANCE: The Lessee is responsible for any loss or damage to the trailer during the rental period unless otherwise agreed in writing.\n6. INDEMNITY: The Lessee agrees to indemnify IronAxel Rentals against any claims arising from use of the trailer.\n7. CANCELLATION: Cancellation terms as per booking policy. Deposits may be non-refundable if cancelled within a specified period.\n8. GOVERNING LAW: This agreement is governed by the laws of Namibia.",
                'type' => 'text',
                'description' => 'Default terms and conditions for rental contracts',
            ],
            [
                'key' => 'company_registration_no',
                'value' => '',
                'type' => 'string',
                'description' => 'Company registration number for contracts',
            ],
            [
                'key' => 'late_return_fee',
                'value' => '',
                'type' => 'string',
                'description' => 'Late return fee (e.g. N$50 per hour/day) for contracts',
            ],
            [
                'key' => 'cleaning_fee',
                'value' => '',
                'type' => 'string',
                'description' => 'Cleaning fee if trailer returned dirty (e.g. N$200)',
            ],
            [
                'key' => 'trailer_replacement_value',
                'value' => '',
                'type' => 'string',
                'description' => 'Default trailer replacement value for liability (e.g. N$35000)',
            ],
            [
                'key' => 'max_load_capacity',
                'value' => '',
                'type' => 'string',
                'description' => 'Default max load capacity in kg for contracts',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
