<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        $departs = now()->addDays(3)->startOfDay();

        return [
            'code' => 'DKS-'.$departs->format('Ymd').'-'.$this->faker->unique()->numerify('###'),
            'employee_id' => Employee::factory(),
            'vehicle_id' => Vehicle::factory(),
            'departs_on' => $departs,
            'returns_on' => $departs->copy(),
            'destination' => 'Puskesmas Kaligondang',
            'purpose' => 'Distribusi obat',
            'status' => BookingStatus::Menunggu,
            'admin_note' => null,
        ];
    }

    public function status(BookingStatus $status): static
    {
        return $this->state(['status' => $status]);
    }
}
