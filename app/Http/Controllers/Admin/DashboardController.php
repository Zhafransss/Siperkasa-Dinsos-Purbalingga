<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\VehicleStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** How many vehicles the "most used" chart shows. */
    private const CHART_SIZE = 5;

    public function __invoke(Request $request): View
    {
        $today = today();
        $month = $this->month($request->query('bulan'));

        // Approved bookings that cover today: the schedule of the day.
        $usedToday = Booking::query()
            ->where('status', BookingStatus::Disetujui)
            ->overlappingDates($today, $today)
            ->with(['vehicle.photos', 'employee'])
            ->orderBy('departs_on')
            ->orderBy('id')
            ->get();

        // "Tersedia" = bookable status, not out on an approved booking today, not in the workshop today.
        $available = Vehicle::query()
            ->where('status', VehicleStatus::Tersedia)
            ->whereNotIn('id', $usedToday->pluck('vehicle_id'))
            ->whereDoesntHave('maintenances', fn ($q) => $q->overlappingDates($today, $today))
            ->count();

        return view('admin.dashboard', [
            'stats' => [
                'total' => Vehicle::count(),
                'available' => $available,
                'pending' => Booking::where('status', BookingStatus::Menunggu)->count(),
                'today' => $usedToday->count(),
            ],
            'usedToday' => $usedToday,
            'today' => $today,
            'chart' => $this->mostUsed($month),
            'month' => $month,
            'previousMonth' => $month->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $month->copy()->addMonth()->format('Y-m'),
            'isCurrentMonth' => $month->isSameMonth($today),
        ]);
    }

    /** Selected month from ?bulan=YYYY-MM, falling back to the current one for anything else. */
    private function month(mixed $value): Carbon
    {
        if (is_string($value) && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)) {
            return Carbon::createFromFormat('Y-m', $value)->startOfMonth();
        }

        return today()->startOfMonth();
    }

    /**
     * The most used vehicles of a month: number of approved bookings whose dates touch that month.
     *
     * @return list<array{vehicle: Vehicle, total: int}>
     */
    private function mostUsed(Carbon $month): array
    {
        $rows = Booking::query()
            ->selectRaw('vehicle_id, COUNT(*) as total')
            ->where('status', BookingStatus::Disetujui)
            ->overlappingDates($month, $month->copy()->endOfMonth())
            ->groupBy('vehicle_id')
            ->orderByDesc('total')
            ->orderBy('vehicle_id')
            ->limit(self::CHART_SIZE)
            ->get();

        $vehicles = Vehicle::whereIn('id', $rows->pluck('vehicle_id'))->get()->keyBy('id');

        return $rows->map(fn ($row) => ['vehicle' => $vehicles[$row->vehicle_id], 'total' => (int) $row->total])->all();
    }
}
