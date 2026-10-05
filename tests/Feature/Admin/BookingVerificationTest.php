<?php

use App\Enums\BookingStatus;
use App\Enums\VehicleStatus;
use App\Models\Booking;
use App\Models\User;
use App\Models\VehicleMaintenance;

beforeEach(fn () => $this->actingAs(User::factory()->create()));

it('lists pending requests in the first tab and processed ones in Riwayat', function () {
    $pending = Booking::factory()->create(['destination' => 'Tujuan Menunggu']);
    $done = Booking::factory()->status(BookingStatus::Ditolak)->create(['destination' => 'Tujuan Ditolak']);

    $this->get('/admin/peminjaman')->assertSee('Tujuan Menunggu')->assertDontSee('Tujuan Ditolak');
    $this->get('/admin/peminjaman?tab=riwayat')->assertSee('Tujuan Ditolak')->assertDontSee('Tujuan Menunggu');
});

it('shows an approved booking in the past as Selesai in Riwayat', function () {
    Booking::factory()->status(BookingStatus::Disetujui)->create([
        'departs_on' => today()->subDays(5), 'returns_on' => today()->subDays(3),
    ]);

    $this->get('/admin/peminjaman?tab=riwayat')->assertSee('Selesai');
});

it('searches by employee name, NIP, vehicle and code', function () {
    $booking = Booking::factory()->create();
    Booking::factory()->create();

    foreach ([$booking->employee->name, $booking->employee->nip, $booking->vehicle->plate, $booking->code] as $term) {
        $this->get('/admin/peminjaman?q='.urlencode($term))->assertViewHas('bookings', fn ($b) => $b->contains($booking));
    }
});

it('shows the booking detail', function () {
    $booking = Booking::factory()->create(['purpose' => 'Mengantar sampel laboratorium']);

    $this->get("/admin/peminjaman/{$booking->code}")->assertOk()->assertSee('Mengantar sampel laboratorium')->assertSee($booking->employee->nip);
});

it('approves a pending booking with an optional note', function () {
    $booking = Booking::factory()->create();

    $this->post("/admin/peminjaman/{$booking->code}/setujui", ['admin_note' => ''])->assertRedirect(route('admin.bookings.index'));

    expect($booking->fresh()->status)->toBe(BookingStatus::Disetujui)->and($booking->fresh()->admin_note)->toBeNull();
});

it('stores the approval note', function () {
    $booking = Booking::factory()->create();

    $this->post("/admin/peminjaman/{$booking->code}/setujui", ['admin_note' => 'Ambil kunci di resepsionis.']);

    expect($booking->fresh()->admin_note)->toBe('Ambil kunci di resepsionis.');
});

it('requires a reason to reject', function () {
    $booking = Booking::factory()->create();

    $this->post("/admin/peminjaman/{$booking->code}/tolak", ['admin_note' => ''])->assertSessionHasErrors('admin_note');
    expect($booking->fresh()->status)->toBe(BookingStatus::Menunggu);

    $this->post("/admin/peminjaman/{$booking->code}/tolak", ['admin_note' => 'Kendaraan dipakai kegiatan lain.'])->assertRedirect();
    expect($booking->fresh()->status)->toBe(BookingStatus::Ditolak)->and($booking->fresh()->admin_note)->toBe('Kendaraan dipakai kegiatan lain.');
});

it('does not process the same request twice', function () {
    $booking = Booking::factory()->status(BookingStatus::Ditolak)->create(['admin_note' => 'Alasan awal']);

    $this->post("/admin/peminjaman/{$booking->code}/setujui")->assertSessionHas('toast');
    $this->post("/admin/peminjaman/{$booking->code}/tolak", ['admin_note' => 'Lagi'])->assertSessionHas('toast');

    expect($booking->fresh()->status)->toBe(BookingStatus::Ditolak)->and($booking->fresh()->admin_note)->toBe('Alasan awal');
});

it('refuses to approve when the vehicle went into maintenance after the request', function () {
    $booking = Booking::factory()->create();
    VehicleMaintenance::factory()->create([
        'vehicle_id' => $booking->vehicle_id,
        'starts_on' => $booking->departs_on->toDateString(),
        'ends_on' => $booking->returns_on->toDateString(),
    ]);

    $this->post("/admin/peminjaman/{$booking->code}/setujui")->assertSessionHas('toast');

    expect($booking->fresh()->status)->toBe(BookingStatus::Menunggu);
    $this->get("/admin/peminjaman/{$booking->code}")->assertSee('perawatan');
});

it('refuses to approve when the vehicle is not bookable any more', function () {
    $booking = Booking::factory()->create();
    $booking->vehicle->update(['status' => VehicleStatus::Perawatan]);

    $this->post("/admin/peminjaman/{$booking->code}/setujui")->assertSessionHas('toast');

    expect($booking->fresh()->status)->toBe(BookingStatus::Menunggu);
});
