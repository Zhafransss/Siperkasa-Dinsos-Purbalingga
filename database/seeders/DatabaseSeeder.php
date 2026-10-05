<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed demo data (fictional employees/NIPs, fleet and bookings) for local development.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            EmployeeSeeder::class,
            VehicleSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
