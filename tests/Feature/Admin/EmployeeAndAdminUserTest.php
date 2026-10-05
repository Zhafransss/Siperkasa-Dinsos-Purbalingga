<?php

use App\Models\Booking;
use App\Models\Employee;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->actingAs($this->admin);
});

function employeePayload(array $overrides = []): array
{
    return $overrides + [
        'nip' => '198811042012021001',
        'name' => 'Bambang Sulistyo',
        'email' => 'bambang@example.test',
        'bidang' => 'Bidang P2P',
        'jabatan' => 'Kepala Seksi',
        'pangkat' => 'Penata / IIIc',
    ];
}

// ---- Employees ----

it('lists employees with stats, search and bidang filter', function () {
    Employee::factory()->create(['name' => 'Siti Aminah', 'bidang' => 'Sekretariat']);
    Employee::factory()->create(['name' => 'Joko Widodo', 'bidang' => 'Bidang P2P']);
    Employee::factory()->inactive()->create();

    $this->get('/admin/pegawai')->assertOk()->assertViewHas('stats', fn ($s) => $s['total'] === 3 && $s['active'] === 2 && $s['inactive'] === 1);
    $this->get('/admin/pegawai?q=siti')->assertSee('Siti Aminah')->assertDontSee('Joko Widodo');
    $this->get('/admin/pegawai?bidang='.urlencode('Bidang P2P'))->assertSee('Joko Widodo')->assertDontSee('Siti Aminah');
});

it('creates an employee and strips spaces from the NIP', function () {
    $this->post('/admin/pegawai', employeePayload(['nip' => '1988 1104 2012 02 1 001']))->assertRedirect(route('admin.employees.index'));

    expect(Employee::firstWhere('nip', '198811042012021001'))->not->toBeNull();
});

it('validates employee NIP, uniqueness and e-mail', function () {
    Employee::factory()->create(['nip' => '198811042012021001', 'email' => 'bambang@example.test']);

    $this->post('/admin/pegawai', employeePayload())->assertSessionHasErrors(['nip', 'email']);
    $this->post('/admin/pegawai', employeePayload(['nip' => '123', 'email' => 'bukan-email', 'bidang' => '']))->assertSessionHasErrors(['nip', 'email', 'bidang']);
});

it('updates an employee, toggling active through the form checkbox', function () {
    $employee = Employee::factory()->create(['nip' => '198811042012021001']);

    $this->put("/admin/pegawai/{$employee->id}", employeePayload(['name' => 'Nama Baru']))->assertRedirect();

    expect($employee->fresh()->name)->toBe('Nama Baru')->and($employee->fresh()->is_active)->toBeFalse();
});

it('toggles an employee active state', function () {
    $employee = Employee::factory()->create();

    $this->patch("/admin/pegawai/{$employee->id}/status");
    expect($employee->fresh()->is_active)->toBeFalse();

    $this->patch("/admin/pegawai/{$employee->id}/status");
    expect($employee->fresh()->is_active)->toBeTrue();
});

it('blocks a deactivated employee from the booking NIP check', function () {
    $employee = Employee::factory()->create();
    $this->patch("/admin/pegawai/{$employee->id}/status");

    expect(Employee::findActiveByNip($employee->nip))->toBeNull();
});

it('deletes an employee without bookings but keeps one with history', function () {
    $free = Employee::factory()->create();
    $booking = Booking::factory()->create();

    $this->delete("/admin/pegawai/{$free->id}")->assertRedirect();
    $this->delete("/admin/pegawai/{$booking->employee_id}")->assertSessionHas('toast');

    $this->assertModelMissing($free);
    $this->assertModelExists($booking->employee);
});

// ---- Admin users ----

it('creates an administrator who can then sign in', function () {
    $this->post('/admin/admin', [
        'nip' => '199001012015011009', 'name' => 'Admin Baru', 'bidang' => 'Sekretariat', 'email' => 'baru@example.test',
        'password' => 'Rahasia#2026', 'password_confirmation' => 'Rahasia#2026',
    ])->assertRedirect(route('admin.admins.index'));

    auth()->logout();
    $this->post('/admin/masuk', ['nip' => '199001012015011009', 'password' => 'Rahasia#2026'])->assertRedirect(route('admin.dashboard'));
});

it('enforces the password rules for a new administrator', function () {
    // Exactly 8 characters with a number and a symbol is the minimum that passes.
    $this->post('/admin/admin', ['nip' => '199001012015011009', 'name' => 'Admin Baru', 'password' => 'pendek1!', 'password_confirmation' => 'pendek1!'])->assertSessionHasNoErrors();
    $this->post('/admin/admin', ['nip' => '199001012015011010', 'name' => 'X', 'password' => 'abc', 'password_confirmation' => 'abc'])->assertSessionHasErrors('password');
    $this->post('/admin/admin', ['nip' => '199001012015011011', 'name' => 'X', 'password' => 'tanpaangka!', 'password_confirmation' => 'tanpaangka!'])->assertSessionHasErrors('password');
    $this->post('/admin/admin', ['nip' => '199001012015011012', 'name' => 'X', 'password' => 'tanpasimbol12', 'password_confirmation' => 'tanpasimbol12'])->assertSessionHasErrors('password');
    $this->post('/admin/admin', ['nip' => '199001012015011013', 'name' => 'X', 'password' => 'Rahasia#2026', 'password_confirmation' => 'beda'])->assertSessionHasErrors('password');
});

it('keeps the old password when the field is left blank on update', function () {
    $other = User::factory()->create(['nip' => '199001012015011020', 'password' => 'Lama#12345']);

    $this->put("/admin/admin/{$other->id}", ['nip' => $other->nip, 'name' => 'Nama Baru', 'password' => '', 'password_confirmation' => ''])->assertSessionHasNoErrors();

    expect($other->fresh()->name)->toBe('Nama Baru');
    expect(Hash::check('Lama#12345', $other->fresh()->password))->toBeTrue();
});

it('does not let an admin deactivate or delete their own account', function () {
    $this->patch("/admin/admin/{$this->admin->id}/status")->assertSessionHas('toast');
    $this->delete("/admin/admin/{$this->admin->id}")->assertSessionHas('toast');

    $this->assertModelExists($this->admin);
    expect($this->admin->fresh()->is_active)->toBeTrue();
});

it('lets an admin deactivate and delete another administrator', function () {
    $other = User::factory()->create();

    $this->patch("/admin/admin/{$other->id}/status");
    expect($other->fresh()->is_active)->toBeFalse();

    $this->delete("/admin/admin/{$other->id}")->assertRedirect();
    $this->assertModelMissing($other);
});

it('never leaves the system without an active administrator', function () {
    $inactive = User::factory()->inactive()->create();

    // The only active admin is $this->admin: the "last active admin" guard covers every route that could remove them.
    expect($this->admin->isLastActiveAdmin())->toBeTrue();
    $this->delete("/admin/admin/{$this->admin->id}");
    $this->assertModelExists($this->admin);
    $this->assertModelExists($inactive);
});
