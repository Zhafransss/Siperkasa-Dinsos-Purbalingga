<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use Illuminate\Support\Carbon;

it('counts booked vehicles per day', function () {
    $day = Carbon::parse('2030-03-10');

    // Two different vehicles on the same day, plus one rejected booking that must not count.
    Booking::factory()->create(['departs_on' => $day->copy(), 'returns_on' => $day->copy()]);
    Booking::factory()->status(BookingStatus::Disetujui)->create(['departs_on' => $day->copy(), 'returns_on' => $day->copy()]);
    Booking::factory()->status(BookingStatus::Ditolak)->create(['departs_on' => $day->copy(), 'returns_on' => $day->copy()]);

    $json = $this->getJson('/kalender?month=2030-03')->assertOk()->json();

    expect($json['booked'])->toBe(['2030-03-10' => 2])
        ->and($json['maintenance'])->toBe([])
        ->and($json)->not->toHaveKey('fleet');
});

it('reports vehicles in maintenance per day, clipped to the requested month', function () {
    // Runs from 28 Feb to 2 Mar, so only 1–2 March belong to the March calendar.
    VehicleMaintenance::factory()->create(['starts_on' => '2030-02-28', 'ends_on' => '2030-03-02']);
    VehicleMaintenance::factory()->create(['starts_on' => '2030-03-02', 'ends_on' => '2030-03-02']);

    $maintenance = $this->getJson('/kalender?month=2030-03')->json('maintenance');

    expect($maintenance)->toBe(['2030-03-01' => 1, '2030-03-02' => 2]);
});

it('spreads a multi-day booking over every day from departure to return, both included', function () {
    Booking::factory()->create([
        'departs_on' => Carbon::parse('2030-03-10'),
        'returns_on' => Carbon::parse('2030-03-12'),
    ]);

    $booked = $this->getJson('/kalender?month=2030-03')->json('booked');

    expect(array_keys($booked))->toBe(['2030-03-10', '2030-03-11', '2030-03-12']);
});

it('clips a booking that crosses the month boundary', function () {
    Booking::factory()->create([
        'departs_on' => Carbon::parse('2030-02-27'),
        'returns_on' => Carbon::parse('2030-03-02'),
    ]);

    expect(array_keys($this->getJson('/kalender?month=2030-03')->json('booked')))->toBe(['2030-03-01', '2030-03-02'])
        ->and(array_keys($this->getJson('/kalender?month=2030-02')->json('booked')))->toBe(['2030-02-27', '2030-02-28']);
});

it('shows a multi-day booking in the detail of each of its days', function () {
    Booking::factory()->status(BookingStatus::Disetujui)->create([
        'departs_on' => Carbon::parse('2030-03-10'),
        'returns_on' => Carbon::parse('2030-03-12'),
        'destination' => 'Tujuan Tiga Hari',
    ]);

    foreach (['2030-03-10', '2030-03-11', '2030-03-12'] as $day) {
        $this->get('/kalender/detail?date='.$day)->assertSee('Tujuan Tiga Hari')->assertSee('2030-03-10 s/d 2030-03-12');
    }
    $this->get('/kalender/detail?date=2030-03-13')->assertDontSee('Tujuan Tiga Hari');
});

it('validates the month format', function () {
    $this->getJson('/kalender?month=maret')->assertStatus(422);
    $this->getJson('/kalender')->assertStatus(422);
});

it('lists the pending and approved bookings of the day, not the rejected ones, and masks the NIP', function () {
    $employee = Employee::factory()->create(['nip' => '198811042012021001', 'name' => 'dr. Bambang']);
    $date = Carbon::parse('2030-03-10');
    $slot = ['departs_on' => $date->copy(), 'returns_on' => $date->copy()];

    Booking::factory()->for($employee)->status(BookingStatus::Disetujui)->create($slot + [
        'destination' => 'Tujuan Disetujui', 'admin_note' => 'Ambil kunci di bagian umum',
    ]);
    Booking::factory()->status(BookingStatus::Menunggu)->create($slot + ['destination' => 'Tujuan Menunggu']);
    Booking::factory()->status(BookingStatus::Ditolak)->create($slot + ['destination' => 'Tujuan Ditolak']);

    $this->get('/kalender/detail?date=2030-03-10')
        ->assertOk()
        ->assertSee('Tujuan Disetujui')
        ->assertSee('Ambil kunci di bagian umum')
        ->assertSee('Tujuan Menunggu')
        ->assertDontSee('Tujuan Ditolak')
        ->assertSee('198811')
        ->assertDontSee('198811042012021001');
});

it('lists vehicles in maintenance on the day', function () {
    $vehicle = Vehicle::factory()->create(['name' => 'Mobil Servis']);
    VehicleMaintenance::factory()->for($vehicle)->create([
        'starts_on' => '2030-03-09', 'ends_on' => '2030-03-11', 'reason' => 'Ganti oli & rem',
    ]);

    $this->get('/kalender/detail?date=2030-03-10')->assertOk()->assertSee('Mobil Servis')->assertSee('Ganti oli &amp; rem', false);
    $this->get('/kalender/detail?date=2030-03-12')->assertOk()->assertDontSee('Mobil Servis');
});

it('shows in the day detail exactly what the month chips count', function () {
    Booking::factory()->status(BookingStatus::Menunggu)->create([
        'departs_on' => '2030-03-10', 'returns_on' => '2030-03-10',
    ]);
    VehicleMaintenance::factory()->create(['starts_on' => '2030-03-10', 'ends_on' => '2030-03-10']);

    $month = $this->getJson('/kalender?month=2030-03')->json();
    $html = $this->get('/kalender/detail?date=2030-03-10')->getContent();

    expect($month['booked']['2030-03-10'])->toBe(1)
        ->and($month['maintenance']['2030-03-10'])->toBe(1)
        ->and(substr_count($html, 'PLAT NO'))->toBe(2); // one booking card + one maintenance card
});

it('shows a friendly message for a day without bookings or maintenance', function () {
    $this->get('/kalender/detail?date=2030-03-10')->assertOk()->assertSee('Belum ada peminjaman atau perawatan');
});

it('escapes user-provided text in the day detail', function () {
    $date = Carbon::parse('2030-03-10');
    Booking::factory()->status(BookingStatus::Disetujui)->create([
        'departs_on' => $date->copy(), 'returns_on' => $date->copy(),
        'destination' => '<script>alert(1)</script>',
    ]);

    $this->get('/kalender/detail?date=2030-03-10')->assertDontSee('<script>alert(1)</script>', false);
});
