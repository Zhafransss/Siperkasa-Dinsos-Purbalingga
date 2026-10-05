<?php

use App\Enums\VehicleStatus;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake(VehiclePhoto::DISK);
    $this->actingAs(App\Models\User::factory()->create());
});

function vehiclePayload(array $overrides = []): array
{
    return $overrides + [
        'name' => 'Avanza Operasional 01',
        'description' => 'MPV Operasional',
        'brand_model' => 'Toyota Avanza G 2023',
        'plate' => 'r 1234 aa',
        'year' => now()->year,
        'fuel_type' => 'pertalite',
        'category' => 'operasional',
        'status' => 'tersedia',
        'capacity' => 7,
        'odometer_km' => 1200,
        'color' => 'Putih',
    ];
}

it('lists vehicles and filters by search, status and category', function () {
    Vehicle::factory()->create(['name' => 'Innova Dinas', 'plate' => 'R 1 AA', 'brand_model' => 'Toyota Innova', 'status' => VehicleStatus::Tersedia, 'category' => 'operasional']);
    Vehicle::factory()->create(['name' => 'Ambulans', 'plate' => 'R 2 BB', 'brand_model' => 'Toyota Hiace', 'status' => VehicleStatus::Perawatan, 'category' => 'gawat_darurat']);

    $this->get('/admin/kendaraan')->assertOk()->assertSee('Innova Dinas')->assertSee('Ambulans');
    $this->get('/admin/kendaraan?q=innova')->assertSee('Innova Dinas')->assertDontSee('Ambulans');
    $this->get('/admin/kendaraan?status=perawatan')->assertSee('Ambulans')->assertDontSee('Innova Dinas');
    $this->get('/admin/kendaraan?category=gawat_darurat')->assertSee('Ambulans')->assertDontSee('Innova Dinas');
});

it('rejects an invalid filter value', function () {
    $this->get('/admin/kendaraan?status=ngawur')->assertSessionHasErrors('status');
});

it('creates a vehicle with normalised plate and photos', function () {
    $this->post('/admin/kendaraan', vehiclePayload([
        'photos' => ['depan' => UploadedFile::fake()->image('a.jpg'), 'interior' => UploadedFile::fake()->image('b.png')],
    ]))->assertRedirect(route('admin.vehicles.index'));

    $vehicle = Vehicle::firstWhere('plate', 'R 1234 AA');
    expect($vehicle)->not->toBeNull()->and($vehicle->photos)->toHaveCount(2);
    Storage::disk(VehiclePhoto::DISK)->assertExists($vehicle->photos->first()->path);
});

it('validates required fields, unique plate and photo rules', function () {
    Vehicle::factory()->create(['plate' => 'R 1234 AA']);

    $this->post('/admin/kendaraan', vehiclePayload(['name' => '', 'photos' => ['depan' => UploadedFile::fake()->create('x.pdf', 10)]]))
        ->assertSessionHasErrors(['name', 'plate', 'photos.depan']);

    $this->post('/admin/kendaraan', vehiclePayload(['plate' => 'R 9 ZZ', 'photos' => ['depan' => UploadedFile::fake()->image('big.jpg')->size(6000)]]))
        ->assertSessionHasErrors('photos.depan');
});

it('updates a vehicle, keeping its own plate, and replaces a photo', function () {
    $vehicle = Vehicle::factory()->create(['plate' => 'R 1234 AA']);
    $this->put("/admin/kendaraan/{$vehicle->id}", vehiclePayload(['photos' => ['depan' => UploadedFile::fake()->image('1.jpg')]]))->assertRedirect();
    $oldPath = $vehicle->photos()->first()->path;

    $this->put("/admin/kendaraan/{$vehicle->id}", vehiclePayload(['name' => 'Nama Baru', 'photos' => ['depan' => UploadedFile::fake()->image('2.jpg')]]))
        ->assertSessionHasNoErrors();

    expect($vehicle->fresh()->name)->toBe('Nama Baru')->and($vehicle->photos()->count())->toBe(1);
    Storage::disk(VehiclePhoto::DISK)->assertMissing($oldPath);
});

it('deletes a single photo', function () {
    $vehicle = Vehicle::factory()->create();
    $this->put("/admin/kendaraan/{$vehicle->id}", vehiclePayload(['plate' => $vehicle->plate, 'photos' => ['samping' => UploadedFile::fake()->image('s.jpg')]]));

    $this->delete("/admin/kendaraan/{$vehicle->id}/foto/samping")->assertRedirect();
    $this->delete("/admin/kendaraan/{$vehicle->id}/foto/atap")->assertNotFound();

    expect($vehicle->photos()->count())->toBe(0);
});

it('deletes a vehicle without bookings together with its photos', function () {
    $vehicle = Vehicle::factory()->create();
    $this->put("/admin/kendaraan/{$vehicle->id}", vehiclePayload(['plate' => $vehicle->plate, 'photos' => ['depan' => UploadedFile::fake()->image('d.jpg')]]));

    $this->delete("/admin/kendaraan/{$vehicle->id}")->assertRedirect(route('admin.vehicles.index'));

    $this->assertModelMissing($vehicle);
    expect(Storage::disk(VehiclePhoto::DISK)->allFiles())->toBeEmpty();
});

it('refuses to delete a vehicle that has booking history', function () {
    $booking = Booking::factory()->create();

    $this->delete("/admin/kendaraan/{$booking->vehicle_id}")->assertSessionHas('toast');

    $this->assertModelExists($booking->vehicle);
});
