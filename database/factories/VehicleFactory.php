<?php

namespace Database\Factories;

use App\Enums\FuelType;
use App\Enums\VehicleCategory;
use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    public function definition(): array
    {
        $model = $this->faker->randomElement(['Toyota Innova', 'Toyota Avanza', 'Suzuki Ertiga']);

        return [
            'name' => $model,
            'description' => 'Kendaraan Dinas Operasional',
            'brand_model' => $model,
            'plate' => 'R '.$this->faker->unique()->numerify('####').' '.strtoupper($this->faker->lexify('??')),
            'year' => 2023,
            'fuel_type' => FuelType::Pertalite,
            'capacity' => 7,
            'odometer_km' => 12000,
            'color' => 'Putih',
            'last_serviced_on' => null,
            'category' => VehicleCategory::Operasional,
            'status' => VehicleStatus::Tersedia,
            'image_path' => null,
        ];
    }

    public function status(VehicleStatus $status): static
    {
        return $this->state(['status' => $status]);
    }

    public function category(VehicleCategory $category): static
    {
        return $this->state(['category' => $category]);
    }
}
