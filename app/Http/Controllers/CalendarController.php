<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\VehicleMaintenance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CalendarController extends Controller
{
    /**
     * Per-day number of booked vehicles and of vehicles in maintenance for one month,
     * so the client can render "N Dipesan" / "N Perawatan" chips.
     */
    public function month(Request $request): JsonResponse
    {
        $data = $request->validate(['month' => ['required', 'date_format:Y-m']]);

        $start = Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $bookings = Booking::query()
            ->blocking()
            ->overlappingDates($start, $end)
            ->get(['vehicle_id', 'departs_on', 'returns_on']);

        /** @var array<string, array<int, true>> $vehiclesPerDay */
        $vehiclesPerDay = [];
        foreach ($bookings as $booking) {
            // A booking holds its vehicle on every day from departure to return, both included.
            $first = $booking->departs_on->copy()->startOfDay()->max($start);
            $last = $booking->returns_on->copy()->startOfDay()->min($end);

            for ($day = $first->copy(); $day->lte($last); $day->addDay()) {
                $vehiclesPerDay[$day->toDateString()][$booking->vehicle_id] = true;
            }
        }

        /** @var array<string, array<int, true>> $maintenancePerDay */
        $maintenancePerDay = [];
        $maintenances = VehicleMaintenance::query()
            ->overlappingDates($start, $end)
            ->get(['vehicle_id', 'starts_on', 'ends_on']);

        foreach ($maintenances as $maintenance) {
            $first = $maintenance->starts_on->copy()->startOfDay()->max($start);
            $last = $maintenance->ends_on->copy()->startOfDay()->min($end);

            for ($day = $first->copy(); $day->lte($last); $day->addDay()) {
                $maintenancePerDay[$day->toDateString()][$maintenance->vehicle_id] = true;
            }
        }

        return response()->json([
            'month' => $data['month'],
            'booked' => array_map('count', $vehiclesPerDay),
            'maintenance' => array_map('count', $maintenancePerDay),
        ]);
    }

    /**
     * Everything behind the "Dipesan" / "Perawatan" chips of one day (same rules as month()),
     * rendered as an HTML fragment (server-escaped).
     */
    public function day(Request $request): View
    {
        $data = $request->validate(['date' => ['required', 'date_format:Y-m-d']]);

        $date = Carbon::createFromFormat('Y-m-d', $data['date'])->startOfDay();

        $bookings = Booking::query()
            ->blocking()
            ->overlappingDates($date, $date)
            ->with(['employee', 'vehicle'])
            ->orderBy('departs_on')->orderBy('id')
            ->get();

        $maintenances = VehicleMaintenance::query()
            ->overlappingDates($date, $date)
            ->with('vehicle')
            ->get();

        return view('partials.calendar-detail', ['bookings' => $bookings, 'maintenances' => $maintenances]);
    }
}
