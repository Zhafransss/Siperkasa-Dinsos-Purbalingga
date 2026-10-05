<?php

use App\Enums\VehicleStatus;
use App\Models\Vehicle;

it('renders the home page with a vehicle preview', function () {
    Vehicle::factory()->count(4)->create();

    $this->get('/')
        ->assertOk()
        ->assertSee('Peminjaman Kendaraan Dinas Jadi Lebih Mudah.')
        ->assertSee('Katalog Armada');
});

it('lists every vehicle in the catalog, eight per page (two rows of four)', function () {
    Vehicle::factory()->count(10)->create();

    $this->get('/katalog')->assertOk()->assertViewHas('vehicles', fn ($v) => $v->count() === 8 && $v->lastPage() === 2);
    $this->get('/katalog?page=2')->assertOk()->assertViewHas('vehicles', fn ($v) => $v->count() === 2);
});

it('shows vehicles of every type and status, without a filter sidebar', function () {
    Vehicle::factory()->create(['name' => 'Mobil MPV Siap']);
    Vehicle::factory()->create(['name' => 'Bus Servis', 'status' => VehicleStatus::Perawatan]);

    $this->get('/katalog')
        ->assertOk()
        ->assertSee('Mobil MPV Siap')
        ->assertSee('Bus Servis')
        ->assertDontSee('Tipe Kendaraan')
        ->assertDontSee('Status Ketersediaan');
});

it('only offers a booking link for available vehicles', function () {
    $free = Vehicle::factory()->create(['name' => 'Siap Pakai']);
    Vehicle::factory()->status(VehicleStatus::Perawatan)->create(['name' => 'Lagi Servis']);

    $response = $this->get('/katalog')->assertOk();

    $response->assertSee(route('booking.create', ['vehicle' => $free->id]), false)
        ->assertSee('Sedang Servis');
});
