<?php

namespace Database\Seeders;

use App\Models\Trailer;
use Illuminate\Database\Seeder;

class TrailerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trailers = [
            [
                'name' => 'Trailer A',
                'type' => 'Single Axle',
                'axle' => 'Single',
                'size_m' => 2.5,
                'rate_per_day' => 600.00,
                'required_deposit' => null, // Use global setting
                'status' => 'available',
                'description' => '2.5m Single Axle Trailer - Perfect for small loads',
            ],
            [
                'name' => 'Trailer B',
                'type' => 'Double Axle',
                'axle' => 'Double',
                'size_m' => 3.0,
                'rate_per_day' => 900.00,
                'required_deposit' => null,
                'status' => 'available',
                'description' => '3.0m Double Axle Trailer - Ideal for medium loads',
            ],
            [
                'name' => 'Trailer C',
                'type' => 'Double Axle',
                'axle' => 'Double',
                'size_m' => 3.7,
                'rate_per_day' => 900.00,
                'required_deposit' => null,
                'status' => 'available',
                'description' => '3.7m Double Axle Trailer - Great for larger loads',
            ],
        ];

        foreach ($trailers as $trailer) {
            Trailer::firstOrCreate(
                ['name' => $trailer['name']],
                $trailer
            );
        }
    }
}
