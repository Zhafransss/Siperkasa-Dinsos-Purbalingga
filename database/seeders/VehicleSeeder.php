<?php

namespace Database\Seeders;

use App\Enums\FuelType;
use App\Enums\VehicleCategory;
use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            ['name' => 'Toyota Innova Zenix', 'description' => 'MPV Premium Operasional', 'brand_model' => 'Toyota Innova Zenix 2.0 V', 'plate' => 'R 1234 PA', 'year' => 2023, 'fuel_type' => FuelType::Hybrid, 'capacity' => 7, 'odometer_km' => 18450, 'color' => 'Putih Metalik', 'last_serviced_on' => '2026-08-12', 'category' => VehicleCategory::Operasional, 'status' => VehicleStatus::Tersedia, 'image_path' => 'images/vehicles/innova-zenix.png'],
            ['name' => 'Mitsubishi Pajero', 'description' => 'SUV Lapangan Tangguh', 'brand_model' => 'Mitsubishi Pajero Sport Dakar', 'plate' => 'R 5678 QB', 'year' => 2022, 'fuel_type' => FuelType::Dexlite, 'capacity' => 7, 'odometer_km' => 42300, 'color' => 'Hitam', 'last_serviced_on' => '2026-07-03', 'category' => VehicleCategory::Jabatan, 'status' => VehicleStatus::Dipakai, 'image_path' => 'images/vehicles/pajero.png'],
            // Ambulance: no seat capacity is shown.
            ['name' => 'Toyota Hiace Gawat Darurat', 'description' => 'Ambulans Medis Intensif', 'brand_model' => 'Toyota Hiace Premio', 'plate' => 'R 9012 PC', 'year' => 2023, 'fuel_type' => FuelType::Solar, 'capacity' => null, 'odometer_km' => 12450, 'color' => 'Putih Kristal', 'last_serviced_on' => '2026-09-15', 'category' => VehicleCategory::GawatDarurat, 'status' => VehicleStatus::Tersedia, 'image_path' => 'images/vehicles/ambulans.png'],
            ['name' => 'Suzuki Ertiga Hybrid', 'description' => 'Kendaraan Dinas Efisien', 'brand_model' => 'Suzuki Ertiga Hybrid', 'plate' => 'R 3344 PD', 'year' => 2021, 'fuel_type' => FuelType::Hybrid, 'capacity' => 7, 'odometer_km' => 56200, 'color' => 'Silver', 'last_serviced_on' => '2026-06-20', 'category' => VehicleCategory::Operasional, 'status' => VehicleStatus::Tersedia, 'image_path' => 'images/vehicles/ertiga-hybrid.png'],
            ['name' => 'Toyota Avanza', 'description' => 'Transportasi Staf Umum', 'brand_model' => 'Toyota Avanza 1.5 G', 'plate' => 'R 9911 PE', 'year' => 2022, 'fuel_type' => FuelType::Pertalite, 'capacity' => 7, 'odometer_km' => 38900, 'color' => 'Putih', 'last_serviced_on' => '2026-05-30', 'category' => VehicleCategory::Operasional, 'status' => VehicleStatus::Perawatan, 'image_path' => 'images/vehicles/avanza.png'],
            ['name' => 'Bus Puskesmas Keliling', 'description' => 'Layanan Kesehatan Masyarakat', 'brand_model' => 'Isuzu Elf Puskesmas Keliling', 'plate' => 'R 7788 PF', 'year' => 2020, 'fuel_type' => FuelType::Solar, 'capacity' => 15, 'odometer_km' => 91200, 'color' => 'Putih', 'last_serviced_on' => '2026-09-01', 'category' => VehicleCategory::GawatDarurat, 'status' => VehicleStatus::Tersedia, 'image_path' => 'images/vehicles/bus-puskesmas.png'],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::updateOrCreate(['plate' => $vehicle['plate']], $vehicle);
        }
    }
}
