<?php

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Vehicle;

it('asks a visitor for their NIP before showing any booking', function () {
    Booking::factory()->create(['destination' => 'Tujuan Rahasia']);

    $this->get('/status')
        ->assertOk()
        ->assertSee('Masukkan NIP Anda')
        ->assertDontSee('Tujuan Rahasia');
});

it('logs in with a valid NIP and lists only that employee\'s bookings', function () {
    $mine = Employee::factory()->create();
    Booking::factory()->for($mine)->create(['destination' => 'Tujuan Saya']);
    Booking::factory()->create(['destination' => 'Tujuan Orang Lain']);

    $this->post('/status/masuk', ['nip' => $mine->nip])->assertRedirect(route('status.index'));

    $this->get('/status')
        ->assertOk()
        ->assertSee('Tujuan Saya')
        ->assertDontSee('Tujuan Orang Lain')
        ->assertViewHas('bookings', fn ($b) => $b->total() === 1);
});

it('shows an error for an unknown NIP', function () {
    $this->post('/status/masuk', ['nip' => '123456789012345678'])->assertSessionHasErrors('nip');
});

it('lets the owner cancel a pending booking', function () {
    $employee = Employee::factory()->create();
    $booking = Booking::factory()->for($employee)->create();

    $this->withSession(['employee_id' => $employee->id])
        ->delete(route('booking.cancel', $booking))
        ->assertRedirect(route('status.index'))
        ->assertSessionHas('toast');

    expect($booking->fresh()->status)->toBe(BookingStatus::Dibatalkan);
});

it('does not cancel a booking that is already processed', function (BookingStatus $status) {
    $employee = Employee::factory()->create();
    $booking = Booking::factory()->for($employee)->status($status)->create();

    $this->withSession(['employee_id' => $employee->id])->delete(route('booking.cancel', $booking));

    expect($booking->fresh()->status)->toBe($status);
})->with([BookingStatus::Disetujui, BookingStatus::Ditolak]);

it('forbids cancelling someone else\'s booking', function () {
    $booking = Booking::factory()->create();
    $stranger = Employee::factory()->create();

    $this->withSession(['employee_id' => $stranger->id])->delete(route('booking.cancel', $booking))->assertForbidden();
    $this->delete(route('booking.cancel', $booking))->assertForbidden();

    expect($booking->fresh()->status)->toBe(BookingStatus::Menunggu);
});

it('forgets the NIP on logout', function () {
    $employee = Employee::factory()->create();

    $this->withSession(['employee_id' => $employee->id])->post('/status/keluar')->assertRedirect(route('status.index'));

    expect(session('employee_id'))->toBeNull();
});

it('shows the applicant name and NIP on every booking card', function () {
    $employee = Employee::factory()->create(['nip' => '198811042012021001', 'name' => 'dr. Bambang Sulistyo']);
    Booking::factory()->for($employee)->count(2)->create();

    $html = $this->withSession(['employee_id' => $employee->id])->get('/status')->assertOk()->getContent();

    expect(substr_count($html, 'dr. Bambang Sulistyo'))->toBeGreaterThanOrEqual(2 + 1) // 2 cards + the "Masuk sebagai" line
        ->and(substr_count($html, 'NIP: 198811042012021001'))->toBe(2);
});

/** Creates `$count` bookings for the employee, newest last, each with a distinct vehicle/destination. */
function bookingsFor(Employee $employee, int $count): void
{
    foreach (range(1, $count) as $i) {
        Booking::factory()->for($employee)->for(Vehicle::factory()->create(['name' => "Kendaraan $i"]))->create([
            'code' => sprintf('DKS-20261002-%03d', $i),
            'destination' => "Tujuan $i",
            'created_at' => now()->subMinutes($count - $i), // higher number = newer
        ]);
    }
}

describe('pagination', function () {
    it('shows five bookings per page, newest first, with a working pager', function () {
        $employee = Employee::factory()->create();
        bookingsFor($employee, 12);

        $page = fn (int $n) => $this->withSession(['employee_id' => $employee->id])->get('/status?page='.$n);

        $page(1)->assertOk()
            ->assertViewHas('bookings', fn ($b) => $b->count() === 5 && $b->total() === 12 && $b->lastPage() === 3)
            ->assertSeeInOrder(['DKS-20261002-012', 'DKS-20261002-011', 'DKS-20261002-010', 'DKS-20261002-009', 'DKS-20261002-008'])
            ->assertDontSee('DKS-20261002-007')
            ->assertSee('Menampilkan 1–5 dari 12 Pengajuan Peminjaman')
            ->assertSee('aria-label="Navigasi halaman"', false)
            ->assertSee('?page=2', false);

        $page(3)->assertOk()
            ->assertViewHas('bookings', fn ($b) => $b->count() === 2)
            ->assertSeeInOrder(['DKS-20261002-002', 'DKS-20261002-001'])
            ->assertSee('Menampilkan 11–12 dari 12 Pengajuan Peminjaman');
    });

    it('shows no pager when everything fits on one page', function () {
        $employee = Employee::factory()->create();
        bookingsFor($employee, 5);

        $this->withSession(['employee_id' => $employee->id])->get('/status')
            ->assertOk()
            ->assertDontSee('aria-label="Navigasi halaman"', false)
            ->assertSee('Menampilkan 1–5 dari 5 Pengajuan Peminjaman');
    });

    it('only paginates the signed-in employee\'s own bookings', function () {
        $mine = Employee::factory()->create();
        bookingsFor($mine, 6);
        Booking::factory()->count(10)->create(); // other people's bookings

        $this->withSession(['employee_id' => $mine->id])->get('/status')
            ->assertViewHas('bookings', fn ($b) => $b->total() === 6 && $b->lastPage() === 2);
    });

    it('sends a page number beyond the last page back to the first page', function () {
        $employee = Employee::factory()->create();
        bookingsFor($employee, 3);

        $this->withSession(['employee_id' => $employee->id])->get('/status?page=9')->assertRedirect(route('status.index'));
        $this->withSession(['employee_id' => $employee->id])->get('/status?page=9&q=tujuan')->assertRedirect(route('status.index', ['q' => 'tujuan']));
    });

    it('shows the empty state for an employee without bookings', function () {
        $employee = Employee::factory()->create();

        $this->withSession(['employee_id' => $employee->id])->get('/status')
            ->assertOk()
            ->assertSee('Anda belum memiliki pengajuan peminjaman')
            ->assertSee('Menampilkan 0 Pengajuan Peminjaman');
    });
});

describe('search (server side, across all pages)', function () {
    beforeEach(function () {
        $this->employee = Employee::factory()->create(['nip' => '198811042012021001', 'name' => 'dr. Bambang Sulistyo']);
        bookingsFor($this->employee, 12); // spans three pages
    });

    $search = fn (string $q, int $page = 1) => test()->withSession(['employee_id' => test()->employee->id])->get('/status?'.http_build_query(['q' => $q, 'page' => $page]));

    it('finds a booking that is on a later page, by code', function () use ($search) {
        // DKS-20261002-001 is the oldest, i.e. on page 3 of the unfiltered list.
        $search('dks-20261002-001')->assertOk()
            ->assertViewHas('bookings', fn ($b) => $b->total() === 1)
            ->assertSee('DKS-20261002-001')
            ->assertSee('Menampilkan 1–1 dari 1 Pengajuan Peminjaman');
    });

    it('finds bookings by destination and by vehicle name', function () use ($search) {
        $search('Tujuan 7')->assertViewHas('bookings', fn ($b) => $b->total() === 1 && $b->first()->destination === 'Tujuan 7');
        $search('kendaraan 12')->assertViewHas('bookings', fn ($b) => $b->total() === 1 && $b->first()->vehicle->name === 'Kendaraan 12');
    });

    it('is case-insensitive and needs every word to match, in any order', function () use ($search) {
        $search('TUJUAN')->assertViewHas('bookings', fn ($b) => $b->total() === 12);
        $search('tujuan 3 kendaraan 3')->assertViewHas('bookings', fn ($b) => $b->total() === 1);
        $search('3 kendaraan')->assertViewHas('bookings', fn ($b) => $b->total() === 1);
        $search('tujuan 3 kendaraan 4')->assertViewHas('bookings', fn ($b) => $b->total() === 0);
    });

    it('matches the applicant\'s own NIP and name, which are the same on every booking', function () use ($search) {
        $search('198811042012021001')->assertViewHas('bookings', fn ($b) => $b->total() === 12);
        $search('1988110420')->assertViewHas('bookings', fn ($b) => $b->total() === 12); // 6+ digits of the NIP
        $search('bambang')->assertViewHas('bookings', fn ($b) => $b->total() === 12);
        $search('bambang tujuan 5')->assertViewHas('bookings', fn ($b) => $b->total() === 1);
        $search('sulistyo 999')->assertViewHas('bookings', fn ($b) => $b->total() === 0);
    });

    it('does not let short digits of the NIP hijack a search for a booking code', function () use ($search) {
        // The NIP ends in "001" and contains "12"; both are also booking code fragments.
        $search('001')->assertViewHas('bookings', fn ($b) => $b->total() === 1 && $b->first()->code === 'DKS-20261002-001');
        $search('012')->assertViewHas('bookings', fn ($b) => $b->total() === 1 && $b->first()->code === 'DKS-20261002-012');
        $search('kendaraan 12')->assertViewHas('bookings', fn ($b) => $b->total() === 1);
        $search('2012')->assertViewHas('bookings', fn ($b) => $b->total() === 0); // 4 digits: not treated as the NIP
        $search('dr')->assertViewHas('bookings', fn ($b) => $b->total() === 0); // 2 letters: not treated as the name
    });

    it('does not search other fields such as the purpose, the plate or the status', function () use ($search) {
        Booking::query()->first()->update(['purpose' => 'Rapat koordinasi rahasia']);

        $search('rahasia')->assertViewHas('bookings', fn ($b) => $b->total() === 0);
        $search('menunggu')->assertViewHas('bookings', fn ($b) => $b->total() === 0);
        $search(Booking::first()->vehicle->plate)->assertViewHas('bookings', fn ($b) => $b->total() === 0);
    });

    it('treats % and _ as plain characters, not wildcards', function () use ($search) {
        $search('%')->assertViewHas('bookings', fn ($b) => $b->total() === 0);
        $search('tujuan_1')->assertViewHas('bookings', fn ($b) => $b->total() === 0);
        $search('\\')->assertViewHas('bookings', fn ($b) => $b->total() === 0);
    });

    it('never returns another employee\'s bookings', function () use ($search) {
        Booking::factory()->create(['destination' => 'Tujuan Orang Lain', 'code' => 'DKS-20261002-999']);

        $search('orang lain')->assertViewHas('bookings', fn ($b) => $b->total() === 0)->assertDontSee('DKS-20261002-999');
        $search('dks-20261002-999')->assertViewHas('bookings', fn ($b) => $b->total() === 0);
    });

    it('keeps the search term in the pager links and the search box, and offers a way to clear it', function () use ($search) {
        $search('tujuan')
            ->assertOk()
            ->assertSee('q=tujuan', false)
            ->assertSee('value="tujuan"', false)
            ->assertSee('Hapus pencarian', false)
            ->assertViewHas('bookings', fn ($b) => $b->total() === 12 && str_contains($b->nextPageUrl(), 'q=tujuan'));
    });

    it('explains an empty result and names the search term', function () use ($search) {
        $search('tidak-ada-ini')->assertOk()
            ->assertSee('Tidak ada pengajuan yang cocok dengan pencarian "tidak-ada-ini"', false)
            ->assertSee('Menampilkan 0 Pengajuan Peminjaman');
    });

    it('escapes the search term when it is shown back', function () use ($search) {
        $search('<script>alert(1)</script>')->assertOk()->assertDontSee('<script>alert(1)</script>', false);
    });

    it('treats an empty or blank search as no search', function () use ($search) {
        $search('')->assertViewHas('bookings', fn ($b) => $b->total() === 12);
        $search('   ')->assertViewHas('bookings', fn ($b) => $b->total() === 12);
    });

    it('rejects a search term longer than 100 characters', function () use ($search) {
        $search(str_repeat('a', 101))->assertSessionHasErrors('q');
    });

    it('tells the user what the search covers', function () use ($search) {
        $search('')->assertSee('Cari NIP, nama, kendaraan, kode booking, atau tujuan', false);
    });
});
