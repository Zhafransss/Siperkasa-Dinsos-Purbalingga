<?php

namespace App\Services;

use Illuminate\Support\Carbon;

/**
 * Lead-time rule of the booking form.
 *
 * A request must be filed at least one *working day* before the departure date, so the admin has a working
 * day to process it; the day of use itself can never be requested. The vehicle may still be used on a weekend
 * or holiday — only the filing day has to be a working day.
 *
 * Working days are Monday–Friday for now. National holidays are not modelled yet; add them in isWorkingDay().
 */
class BookingWindow
{
    public static function isWorkingDay(Carbon $date): bool
    {
        return $date->isWeekday();
    }

    /**
     * First departure date that can still be requested when filing at $now.
     *
     * Filing on a working day allows the next calendar day (even a Saturday or Sunday); filing on a
     * non-working day counts as filing on the next working day, so one more day is needed after it.
     */
    public static function earliestDeparture(?Carbon $now = null): Carbon
    {
        $day = ($now ?? now())->copy()->startOfDay();

        while (! self::isWorkingDay($day)) {
            $day->addDay();
        }

        return $day->addDay();
    }
}
