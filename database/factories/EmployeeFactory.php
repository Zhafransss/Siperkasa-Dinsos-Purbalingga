<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nip' => $this->faker->unique()->numerify('##################'),
            'name' => $this->faker->name(),
            'bidang' => $this->faker->randomElement(['Sekretariat', 'Bidang P2P', 'Bidang SDK', 'Bidang Kesmas']),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
