<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleMaintenance>
 */
class VehicleMaintenanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'starts_on' => now()->addDays(5)->toDateString(),
            'ends_on' => now()->addDays(6)->toDateString(),
            'reason' => 'Servis rutin',
        ];
    }
}
