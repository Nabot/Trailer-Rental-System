<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main admin user (Nabot)
        $admin = User::firstOrCreate(
            ['email' => 'nabot123@gmail.com'],
            [
                'name' => 'Nabot',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Create fallback admin user
        $adminFallback = User::firstOrCreate(
            ['email' => 'admin@trailerrental.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $adminFallback->assignRole('admin');

        // Create staff user
        $staff = User::firstOrCreate(
            ['email' => 'staff@trailerrental.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
            ]
        );
        $staff->assignRole('staff');

        // Create customer user
        $customer = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Test Customer',
                'password' => Hash::make('password'),
            ]
        );
        $customer->assignRole('customer');
    }
}
