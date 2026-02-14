<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'trailers.view',
            'trailers.create',
            'trailers.edit',
            'trailers.delete',
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'bookings.view',
            'bookings.create',
            'bookings.edit',
            'bookings.delete',
            'bookings.confirm',
            'bookings.cancel',
            'payments.view',
            'payments.create',
            'payments.edit',
            'inspections.view',
            'inspections.create',
            'inspections.edit',
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'inquiries.view',
            'inquiries.create',
            'inquiries.edit',
            'inquiries.delete',
            'reports.view',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->givePermissionTo([
            'trailers.view',
            'trailers.create',
            'trailers.edit',
            'customers.view',
            'customers.create',
            'customers.edit',
            'bookings.view',
            'bookings.create',
            'bookings.edit',
            'bookings.confirm',
            'bookings.cancel',
            'payments.view',
            'payments.create',
            'payments.edit',
            'inspections.view',
            'inspections.create',
            'inspections.edit',
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'inquiries.view',
            'inquiries.create',
            'inquiries.edit',
            'inquiries.delete',
            'reports.view',
        ]);

        $customer = Role::firstOrCreate(['name' => 'customer']);
        $customer->givePermissionTo([
            'bookings.view',
            'bookings.create',
            'invoices.view',
        ]);
    }
}
