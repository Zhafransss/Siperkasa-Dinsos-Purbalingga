<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request): View
    {
        $tab = $request->query('tab') === 'riwayat' ? 'riwayat' : 'baru';
        $term = trim((string) $request->query('q', ''));

        $query = Booking::query()->with(['vehicle.photos', 'employee']);

        if ($tab === 'baru') {
            // Oldest request first: the queue is worked first-come, first-served.
            $query->where('status', BookingStatus::Menunggu)->orderBy('created_at')->orderBy('id');
        } else {
            $query->whereIn('status', BookingStatus::processed())->latest('updated_at')->latest('id');
        }

        if ($term !== '') {
            $like = '%'.addcslashes($term, '\%_').'%';
            $query->where(fn (Builder $q) => $q
                ->where('code', 'like', $like)
                ->orWhere('destination', 'like', $like)
                ->orWhereHas('employee', fn (Builder $e) => $e->where('name', 'like', $like)->orWhere('nip', 'like', $like))
                ->orWhereHas('vehicle', fn (Builder $v) => $v->where('name', 'like', $like)->orWhere('plate', 'like', $like)));
        }

        return view('admin.bookings.index', [
            'tab' => $tab,
            'term' => $term,
            'bookings' => $query->paginate(self::PER_PAGE)->withQueryString(),
            'pendingCount' => Booking::where('status', BookingStatus::Menunggu)->count(),
        ]);
    }

    public function show(Booking $booking): View
    {
        $booking->load(['vehicle.photos', 'employee']);

        return view('admin.bookings.show', [
            'booking' => $booking,
            'conflict' => $booking->status === BookingStatus::Menunggu ? $this->conflictMessage($booking) : null,
        ]);
    }

    public function approve(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate(['admin_note' => ['nullable', 'string', 'max:500']]);

        return DB::transaction(function () use ($booking, $data) {
            $booking = Booking::query()->lockForUpdate()->findOrFail($booking->id);

            if ($booking->status !== BookingStatus::Menunggu) {
                return back()->with('toast', 'Pengajuan ini sudah diproses sebelumnya.');
            }

            // Re-check at the moment of approval: the vehicle may have gone into the workshop since it was requested.
            if ($message = $this->conflictMessage($booking)) {
                return back()->with('toast', $message);
            }

            $booking->update(['status' => BookingStatus::Disetujui, 'admin_note' => $data['admin_note'] ?? null]);

            return redirect()->route('admin.bookings.index')->with('toast', 'Pengajuan '.$booking->displayId().' disetujui.');
        });
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate(
            ['admin_note' => ['required', 'string', 'max:500']],
            ['admin_note.required' => 'Alasan penolakan wajib diisi.'],
        );

        if ($booking->status !== BookingStatus::Menunggu) {
            return back()->with('toast', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $booking->update(['status' => BookingStatus::Ditolak, 'admin_note' => $data['admin_note']]);

        return redirect()->route('admin.bookings.index')->with('toast', 'Pengajuan '.$booking->displayId().' ditolak.');
    }

    /** Why a pending booking can no longer be approved, or null when it is still free. */
    private function conflictMessage(Booking $booking): ?string
    {
        $vehicle = $booking->vehicle;

        if (! $vehicle->isBookable()) {
            return 'Kendaraan sedang tidak tersedia (status '.$vehicle->status->adminLabel().'). Pengajuan tidak dapat disetujui.';
        }

        if ($vehicle->maintenances()->overlappingDates($booking->departs_on, $booking->returns_on)->exists()) {
            return 'Kendaraan dijadwalkan perawatan pada tanggal tersebut. Pengajuan tidak dapat disetujui.';
        }

        $clash = $vehicle->bookings()->blocking()->whereKeyNot($booking->id)
            ->overlappingDates($booking->departs_on, $booking->returns_on)->exists();

        return $clash ? 'Kendaraan sudah dipesan pada rentang tanggal tersebut oleh pengajuan lain.' : null;
    }
}
