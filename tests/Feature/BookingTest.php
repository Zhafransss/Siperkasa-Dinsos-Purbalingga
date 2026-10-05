<?php

use App\Enums\BookingStatus;
use App\Enums\VehicleStatus;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;

function bookingPayload(Employee $employee, Vehicle $vehicle, array $overrides = []): array
{
    return array_merge([
        'nip' => $employee->nip,
        'vehicle_id' => $vehicle->id,
        'departs_on' => now()->addDays(3)->toDateString(),
        'returns_on' => now()->addDays(3)->toDateString(),
        'destination' => 'Puskesmas Kaligondang',
        'purpose' => 'Distribusi obat',
    ], $overrides);
}

// Wednesday 6 March 2030, so "now + 3 days" (a Saturday) is always a valid departure.
beforeEach(fn () => $this->travelTo(Carbon::parse('2030-03-06')));

describe('NIP verification', function () {
    it('accepts an active employee and remembers them in the session', function () {
        $employee = Employee::factory()->create(['name' => 'dr. Bambang', 'bidang' => 'Bidang P2P']);

        $this->postJson('/peminjaman/verifikasi-nip', ['nip' => $employee->nip])
            ->assertOk()
            ->assertJson(['name' => 'dr. Bambang', 'bidang' => 'P2P']);

        $this->assertSame($employee->id, session('employee_id'));
    });

    it('rejects an unknown NIP', function () {
        $this->postJson('/peminjaman/verifikasi-nip', ['nip' => '123456789012345678'])
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'tidak terdaftar'));

        $this->assertNull(session('employee_id'));
    });

    it('rejects an inactive employee', function () {
        $employee = Employee::factory()->inactive()->create();

        $this->postJson('/peminjaman/verifikasi-nip', ['nip' => $employee->nip])->assertStatus(422);
    });

    it('requires exactly 18 digits', function (string $nip) {
        $this->postJson('/peminjaman/verifikasi-nip', ['nip' => $nip])->assertStatus(422)->assertJsonValidationErrors('nip');
    })->with(['too short' => '1234', 'letters' => 'abcdefghijklmnopqr', 'too long' => '1234567890123456789']);
});

describe('submitting a booking', function () {
    it('creates a pending booking and shows the confirmation to its owner', function () {
        $employee = Employee::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $response = $this->post('/peminjaman', bookingPayload($employee, $vehicle));

        $booking = Booking::firstOrFail();
        $response->assertRedirect(route('booking.success', $booking));
        expect($booking->status)->toBe(BookingStatus::Menunggu)
            ->and($booking->code)->toMatch('/^DKS-\d{8}-001$/')
            ->and($booking->employee_id)->toBe($employee->id);

        $this->get(route('booking.success', $booking))
            ->assertOk()
            ->assertSee('Pengajuan Berhasil Terkirim!')
            ->assertSee($booking->displayId());
    });

    it('numbers bookings sequentially per day', function () {
        $employee = Employee::factory()->create();

        foreach ([3, 4] as $days) {
            $this->post('/peminjaman', bookingPayload($employee, Vehicle::factory()->create(), [
                'departs_on' => now()->addDays($days)->toDateString(),
                'returns_on' => now()->addDays($days)->toDateString(),
            ]));
        }

        expect(Booking::orderBy('id')->pluck('code')->map(fn ($c) => substr($c, -3))->all())->toBe(['001', '002']);
    });

    it('hides the confirmation from anyone but the owner', function () {
        $booking = Booking::factory()->create();

        $this->get(route('booking.success', $booking))->assertForbidden();

        $other = Employee::factory()->create();
        $this->withSession(['employee_id' => $other->id])->get(route('booking.success', $booking))->assertForbidden();
    });

    it('validates the required fields', function () {
        $employee = Employee::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $this->post('/peminjaman', bookingPayload($employee, $vehicle, ['destination' => '', 'purpose' => '']))
            ->assertSessionHasErrors(['destination', 'purpose']);

        expect(Booking::count())->toBe(0);
    });

    it('does not allow booking on the day of use (H-0)', function () {
        $employee = Employee::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $this->post('/peminjaman', bookingPayload($employee, $vehicle, [
            'departs_on' => now()->toDateString(),
            'returns_on' => now()->toDateString(),
        ]))->assertSessionHasErrors('departs_on');

        expect(Booking::count())->toBe(0);
    });

    it('does not allow booking in the past', function () {
        $this->post('/peminjaman', bookingPayload(Employee::factory()->create(), Vehicle::factory()->create(), [
            'departs_on' => now()->subDay()->toDateString(),
            'returns_on' => now()->subDay()->toDateString(),
        ]))->assertSessionHasErrors('departs_on');
    });

    it('rejects a return before the departure', function () {
        $employee = Employee::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $this->post('/peminjaman', bookingPayload($employee, $vehicle, [
            'returns_on' => now()->addDays(2)->toDateString(), // departure is +3 days
        ]))->assertSessionHasErrors('returns_on');
    });

    it('allows a same-day trip and a multi-day trip', function (int $returnAfterDays) {
        $this->post('/peminjaman', bookingPayload(Employee::factory()->create(), Vehicle::factory()->create(), [
            'returns_on' => now()->addDays(3 + $returnAfterDays)->toDateString(),
        ]))->assertSessionDoesntHaveErrors();

        $booking = Booking::firstOrFail();
        expect($booking->departs_on->toDateString())->toBe(now()->addDays(3)->toDateString())
            ->and($booking->returns_on->toDateString())->toBe(now()->addDays(3 + $returnAfterDays)->toDateString());
    })->with(['same day' => 0, 'three days' => 2]);

    it('rejects a date that is not a plain Y-m-d date', function () {
        $this->post('/peminjaman', bookingPayload(Employee::factory()->create(), Vehicle::factory()->create(), [
            'departs_on' => now()->addDays(3)->format('Y-m-d\TH:i'),
        ]))->assertSessionHasErrors('departs_on');
    });

    it('rejects an inactive NIP at submit time', function () {
        $employee = Employee::factory()->inactive()->create();

        $this->post('/peminjaman', bookingPayload($employee, Vehicle::factory()->create()))->assertSessionHasErrors('nip');
    });

    it('rejects a vehicle that is not available', function (VehicleStatus $status) {
        $employee = Employee::factory()->create();
        $vehicle = Vehicle::factory()->status($status)->create();

        $this->post('/peminjaman', bookingPayload($employee, $vehicle))->assertSessionHasErrors('vehicle_id');
        expect(Booking::count())->toBe(0);
    })->with([VehicleStatus::Dipakai, VehicleStatus::Perawatan]);

    it('rejects overlapping bookings of the same vehicle', function () {
        $vehicle = Vehicle::factory()->create();
        Booking::factory()->for($vehicle)->create([
            'departs_on' => now()->addDays(3),
            'returns_on' => now()->addDays(3),
        ]);

        $this->post('/peminjaman', bookingPayload(Employee::factory()->create(), $vehicle))
            ->assertSessionHasErrors('departs_on');

        expect(Booking::count())->toBe(1);
    });

    it('rejects a booking that overlaps a scheduled maintenance of the vehicle', function () {
        $vehicle = Vehicle::factory()->create();
        VehicleMaintenance::factory()->for($vehicle)->create([
            'starts_on' => now()->addDays(2)->toDateString(),
            'ends_on' => now()->addDays(3)->toDateString(),
        ]);

        $this->post('/peminjaman', bookingPayload(Employee::factory()->create(), $vehicle))
            ->assertSessionHasErrors('departs_on');

        expect(Booking::count())->toBe(0);
    });

    it('allows the same vehicle once the earlier booking was rejected or cancelled', function (BookingStatus $status) {
        $vehicle = Vehicle::factory()->create();
        Booking::factory()->for($vehicle)->status($status)->create([
            'departs_on' => now()->addDays(3),
            'returns_on' => now()->addDays(3),
        ]);

        $this->post('/peminjaman', bookingPayload(Employee::factory()->create(), $vehicle))->assertSessionDoesntHaveErrors();

        expect(Booking::count())->toBe(2);
    })->with([BookingStatus::Ditolak, BookingStatus::Dibatalkan]);

    it('allows the same vehicle on the next day', function () {
        $vehicle = Vehicle::factory()->create();
        Booking::factory()->for($vehicle)->create([
            'departs_on' => now()->addDays(3),
            'returns_on' => now()->addDays(3),
        ]);

        $this->post('/peminjaman', bookingPayload(Employee::factory()->create(), $vehicle, [
            'departs_on' => now()->addDays(4)->toDateString(),
            'returns_on' => now()->addDays(4)->toDateString(),
        ]))->assertSessionDoesntHaveErrors();

        expect(Booking::count())->toBe(2);
    });

    it('holds the vehicle for the whole day, so a second booking on the same day is rejected', function () {
        $vehicle = Vehicle::factory()->create();
        Booking::factory()->for($vehicle)->create(['departs_on' => now()->addDays(3), 'returns_on' => now()->addDays(3)]);

        $this->post('/peminjaman', bookingPayload(Employee::factory()->create(), $vehicle))->assertSessionHasErrors('departs_on');
        expect(Booking::count())->toBe(1);
    });

    it('rejects a trip whose range touches an existing multi-day booking', function (int $from, int $to) {
        $vehicle = Vehicle::factory()->create();
        // Existing booking holds +4 .. +6 days.
        Booking::factory()->for($vehicle)->create(['departs_on' => now()->addDays(4), 'returns_on' => now()->addDays(6)]);

        $this->post('/peminjaman', bookingPayload(Employee::factory()->create(), $vehicle, [
            'departs_on' => now()->addDays($from)->toDateString(),
            'returns_on' => now()->addDays($to)->toDateString(),
        ]))->assertSessionHasErrors('departs_on');
    })->with([
        'ends on its first day' => [3, 4],
        'starts on its last day' => [6, 7],
        'inside' => [5, 5],
        'around' => [3, 8],
    ]);
});

describe('lead time (one working day before departure)', function () {
    function fileFor(string $departDate, array $overrides = []): TestResponse
    {
        return test()->post('/peminjaman', bookingPayload(Employee::factory()->create(), Vehicle::factory()->create(), [
            'departs_on' => $departDate,
            'returns_on' => $departDate,
        ] + $overrides));
    }

    it('accepts departing tomorrow when filing on a working day (H-1)', function () {
        fileFor('2030-03-07')->assertSessionDoesntHaveErrors();

        expect(Booking::count())->toBe(1);
    });

    it('lets the vehicle be used on a weekend when filed in time', function (string $departDate) {
        fileFor($departDate)->assertSessionDoesntHaveErrors();

        expect(Booking::count())->toBe(1);
    })->with(['Saturday' => '2030-03-09', 'Sunday' => '2030-03-10']);

    it('accepts a Saturday trip filed on the Friday before', function () {
        $this->travelTo(Carbon::parse('2030-03-08')); // Friday

        fileFor('2030-03-09')->assertSessionDoesntHaveErrors();

        expect(Booking::count())->toBe(1);
    });

    it('rejects a Monday trip filed on the weekend, but accepts Tuesday', function (string $filedOn) {
        $this->travelTo(Carbon::parse($filedOn.' 10:00'));

        fileFor('2030-03-11')->assertSessionHasErrors('departs_on'); // Monday
        expect(Booking::count())->toBe(0);

        fileFor('2030-03-12')->assertSessionDoesntHaveErrors(); // Tuesday
        expect(Booking::count())->toBe(1);
    })->with(['filed Saturday' => '2030-03-09', 'filed Sunday' => '2030-03-10']);

    it('tells the user the earliest possible departure date', function () {
        fileFor('2030-03-06')->assertSessionHasErrors('departs_on');

        expect(session('errors')->first('departs_on'))->toContain('paling cepat: Kamis, 7 Maret 2030');
    });

    it('starts the return-date picker at the earliest departure, or at the departure date after an error', function () {
        // 2030-03-06 is "today" in this file, so the earliest departure is 2030-03-07.
        $this->get(route('booking.create'))->assertSee('name="returns_on" type="date" value="" min="2030-03-07"', false);

        $this->withSession(['_old_input' => ['departs_on' => '2030-03-12']])
            ->get(route('booking.create'))
            ->assertSee('name="returns_on" type="date" value="" min="2030-03-12"', false);
    });

    it('shows the earliest date on the form', function () {
        $this->get(route('booking.create'))
            ->assertOk()
            ->assertSee('Paling cepat')
            ->assertSee('Kamis, 7 Maret 2030')
            ->assertSee('min="2030-03-07"', false);
    });
});

describe('confirmation step', function () {
    it('has no agreement checkbox and does not require one', function () {
        $this->get(route('booking.create'))
            ->assertOk()
            ->assertDontSee('Saya menyatakan')
            ->assertDontSee('name="terms"', false);

        $this->post('/peminjaman', bookingPayload(Employee::factory()->create(), Vehicle::factory()->create()))
            ->assertSessionDoesntHaveErrors();
    });

    it('lists the employee name above the NIP in the order summary', function () {
        $html = $this->get(route('booking.create'))->assertOk()->getContent();

        expect($html)->toContain('data-summary="employee"')
            ->and(strpos($html, 'Nama Pegawai'))->toBeLessThan(strpos($html, 'data-summary="nip"'));
    });

    it('hands the verified name to the page when the form is shown again with the same NIP', function () {
        $employee = Employee::factory()->create(['name' => 'dr. Bambang']);

        $this->withSession(['employee_id' => $employee->id, '_old_input' => ['nip' => $employee->nip]])
            ->get(route('booking.create'))
            ->assertSee('data-employee-name="dr. Bambang"', false);
    });

    it('does not hand over a name for a different or missing NIP', function () {
        $employee = Employee::factory()->create(['name' => 'dr. Bambang']);

        $this->withSession(['employee_id' => $employee->id, '_old_input' => ['nip' => '000000000000000000']])
            ->get(route('booking.create'))
            ->assertSee('data-employee-name=""', false);

        $this->get(route('booking.create'))->assertSee('data-employee-name=""', false);
    });
});

it('pre-selects the vehicle from the catalog link and ignores unavailable ones', function () {
    $free = Vehicle::factory()->create();
    $busy = Vehicle::factory()->status(VehicleStatus::Dipakai)->create();

    $this->get(route('booking.create', ['vehicle' => $free->id]))->assertViewHas('vehicle', fn ($v) => $v->is($free));
    $this->get(route('booking.create', ['vehicle' => $busy->id]))->assertViewHas('vehicle', null);
    $this->get(route('booking.create'))->assertOk();
});
