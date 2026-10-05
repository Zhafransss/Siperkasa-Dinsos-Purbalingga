<?php

use App\Enums\BookingStatus;
use App\Enums\VehicleStatus;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;

function adminUser(array $attributes = []): User
{
    return User::factory()->create($attributes + ['password' => 'Admin@12345']);
}

it('sends guests to the admin login page', function () {
    $this->get('/admin')->assertRedirect(route('admin.login'));
    $this->get('/admin/kendaraan')->assertRedirect(route('admin.login'));
});

it('has no registration page', function () {
    $this->get('/admin/daftar')->assertNotFound();
    $this->get('/register')->assertNotFound();
});

it('signs an active admin in with NIP and password', function () {
    $admin = adminUser(['nip' => '199001012015011001']);

    $this->post('/admin/masuk', ['nip' => '199001012015011001', 'password' => 'Admin@12345'])
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
});

it('gives the same generic error for a wrong password, an unknown NIP and an inactive account', function () {
    adminUser(['nip' => '199001012015011001']);
    adminUser(['nip' => '199001012015011002', 'is_active' => false]);

    $message = 'NIP atau kata sandi salah, atau akun tidak aktif.';

    $this->post('/admin/masuk', ['nip' => '199001012015011001', 'password' => 'salah'])->assertSessionHasErrors(['nip' => $message]);
    $this->post('/admin/masuk', ['nip' => '199001012015019999', 'password' => 'Admin@12345'])->assertSessionHasErrors(['nip' => $message]);
    $this->post('/admin/masuk', ['nip' => '199001012015011002', 'password' => 'Admin@12345'])->assertSessionHasErrors(['nip' => $message]);

    $this->assertGuest();
});

it('locks the login form after five failed attempts', function () {
    adminUser(['nip' => '199001012015011001']);

    foreach (range(1, 5) as $i) {
        $this->post('/admin/masuk', ['nip' => '199001012015011001', 'password' => 'salah']);
    }

    $this->post('/admin/masuk', ['nip' => '199001012015011001', 'password' => 'Admin@12345'])
        ->assertSessionHasErrors('nip');
    $this->assertGuest();
});

it('signs out an admin who is deactivated while logged in', function () {
    $admin = adminUser();
    $this->actingAs($admin)->get('/admin')->assertOk();

    $admin->update(['is_active' => false]);

    $this->get('/admin')->assertRedirect(route('admin.login'));
    $this->assertGuest();
});

it('logs out', function () {
    $this->actingAs(adminUser())->post('/admin/keluar')->assertRedirect(route('admin.login'));
    $this->assertGuest();
});

it('shows the dashboard stats', function () {
    Vehicle::factory()->count(3)->create();
    Vehicle::factory()->create(['status' => VehicleStatus::Perawatan]);
    $inUse = Vehicle::factory()->create();
    Booking::factory()->status(BookingStatus::Disetujui)->create(['vehicle_id' => $inUse->id, 'departs_on' => today(), 'returns_on' => today()]);
    Booking::factory()->count(2)->create(); // pending

    $this->actingAs(adminUser())->get('/admin')
        ->assertOk()
        ->assertViewHas('stats', fn ($s) => $s['total'] === 7 && $s['pending'] === 2 && $s['today'] === 1 && $s['available'] >= 3);
});

it('ranks the most used vehicles of the month by approved bookings only', function () {
    $busy = Vehicle::factory()->create(['name' => 'Paling Sibuk']);
    $quiet = Vehicle::factory()->create(['name' => 'Jarang']);
    $date = today()->startOfMonth()->addDays(2);

    Booking::factory()->count(3)->status(BookingStatus::Disetujui)->create(['vehicle_id' => $busy->id, 'departs_on' => $date, 'returns_on' => $date]);
    Booking::factory()->status(BookingStatus::Disetujui)->create(['vehicle_id' => $quiet->id, 'departs_on' => $date, 'returns_on' => $date]);
    Booking::factory()->count(5)->status(BookingStatus::Ditolak)->create(['vehicle_id' => $quiet->id, 'departs_on' => $date, 'returns_on' => $date]);

    $this->actingAs(adminUser())->get('/admin')
        ->assertViewHas('chart', fn ($c) => $c[0]['vehicle']->is($busy) && $c[0]['total'] === 3 && $c[1]['total'] === 1);
});

it('falls back to the current month for an invalid ?bulan', function () {
    $this->actingAs(adminUser())->get('/admin?bulan=bukan-bulan')
        ->assertOk()
        ->assertViewHas('month', fn ($m) => $m->isSameMonth(today()));
});

it('renders every admin page without errors', function () {
    $this->actingAs($admin = adminUser());
    $vehicle = Vehicle::factory()->create();
    $booking = Booking::factory()->create();

    $paths = [
        '/admin/masuk' => 302, // already signed in
        '/admin/kendaraan/baru', "/admin/kendaraan/{$vehicle->id}",
        "/admin/peminjaman/{$booking->code}",
        '/admin/pegawai', '/admin/pegawai/baru', "/admin/pegawai/{$booking->employee_id}",
        '/admin/admin', '/admin/admin/baru', "/admin/admin/{$admin->id}",
    ];

    foreach ($paths as $path => $status) {
        is_int($path) ? $this->get($status)->assertOk() : $this->get($path)->assertStatus($status);
    }
});

it('shows the SIPERKASA brand on user and admin pages', function () {
    $this->get('/')->assertSee('SIPERKASA');
    $this->actingAs(adminUser())->get('/admin')->assertSee('SIPERKASA');
});
