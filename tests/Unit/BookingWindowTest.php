<?php

use App\Services\BookingWindow;
use Illuminate\Support\Carbon;

// 2030-03-04 is a Monday.
it('allows departing the next day when filing on a working day', function (string $filedOn, string $earliest) {
    expect(BookingWindow::earliestDeparture(Carbon::parse($filedOn.' 10:00'))->toDateString())->toBe($earliest);
})->with([
    'Monday' => ['2030-03-04', '2030-03-05'],
    'Tuesday' => ['2030-03-05', '2030-03-06'],
    'Wednesday' => ['2030-03-06', '2030-03-07'],
    'Thursday' => ['2030-03-07', '2030-03-08'],
    'Friday (Saturday use is allowed)' => ['2030-03-08', '2030-03-09'],
]);

it('treats filing on a weekend as filing on the next working day', function (string $filedOn) {
    // Admin is back on Monday 11th, so the earliest use is Tuesday 12th.
    expect(BookingWindow::earliestDeparture(Carbon::parse($filedOn.' 10:00'))->toDateString())->toBe('2030-03-12');
})->with(['Saturday' => '2030-03-09', 'Sunday' => '2030-03-10']);

it('ignores the time of day', function () {
    expect(BookingWindow::earliestDeparture(Carbon::parse('2030-03-06 23:59'))->toDateString())->toBe('2030-03-07')
        ->and(BookingWindow::earliestDeparture(Carbon::parse('2030-03-06 00:00'))->toDateString())->toBe('2030-03-07');
});

it('does not mutate the given date', function () {
    $now = Carbon::parse('2030-03-06 10:00');

    BookingWindow::earliestDeparture($now);

    expect($now->toDateTimeString())->toBe('2030-03-06 10:00:00');
});
