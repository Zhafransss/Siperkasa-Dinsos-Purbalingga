<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Vehicle;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(Request $request): View
    {
        $vehicle = $request->filled('vehicle') ? Vehicle::find($request->integer('vehicle')) : null;

        // After a server-side validation error the form is shown again with the NIP already verified in step 1;
        // the summary then needs the employee's name without asking for the NIP a second time.
        $employee = Employee::find($request->session()->get('employee_id'));

        return view('peminjaman.create', [
            // A vehicle that cannot be booked is treated as "not chosen".
            'vehicle' => $vehicle?->isBookable() ? $vehicle : null,
            'employeeName' => $employee && $employee->nip === $request->old('nip') ? $employee->name : '',
        ]);
    }

    public function store(StoreBookingRequest $request, BookingService $bookings): RedirectResponse
    {
        $employee = Employee::findActiveByNip($request->validated('nip'));

        $booking = $bookings->create($employee, $request->integer('vehicle_id'), $request->safe()->only([
            'departs_on', 'returns_on', 'destination', 'purpose',
        ]));

        $request->session()->put('employee_id', $employee->id);

        return redirect()->route('booking.success', $booking);
    }

    public function success(Request $request, Booking $booking): View
    {
        $this->authorizeOwner($request, $booking);

        return view('peminjaman.success', ['booking' => $booking->load(['employee', 'vehicle'])]);
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeOwner($request, $booking);

        if (! $booking->isCancellable()) {
            return redirect()->route('status.index')
                ->with('toast', 'Peminjaman ini sudah diproses dan tidak dapat dibatalkan.');
        }

        $booking->update(['status' => BookingStatus::Dibatalkan]);

        return redirect()->route('status.index')->with('toast', 'Peminjaman berhasil dibatalkan.');
    }

    /** Bookings are only visible to the employee who filed them (identified by the verified NIP in session). */
    private function authorizeOwner(Request $request, Booking $booking): void
    {
        abort_unless($request->session()->get('employee_id') === $booking->employee_id, 403);
    }
}
