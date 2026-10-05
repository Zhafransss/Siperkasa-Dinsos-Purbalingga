<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'employee_id', 'vehicle_id', 'departs_on', 'returns_on',
        'destination', 'purpose', 'status', 'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'departs_on' => 'date',
            'returns_on' => 'date',
            'status' => BookingStatus::class,
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /** Bookings that share at least one day with the (inclusive) date range [$from, $to]. */
    public function scopeOverlappingDates(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereDate('departs_on', '<=', $to->toDateString())
            ->whereDate('returns_on', '>=', $from->toDateString());
    }

    /** Bookings that still hold their vehicle (pending or approved). */
    public function scopeBlocking(Builder $query): Builder
    {
        return $query->whereIn('status', BookingStatus::blocking());
    }

    /**
     * Free-text search over an employee's own bookings: booking code, destination, vehicle name and the
     * employee's own name / NIP. Every word must match (in any order), so "innova purwokerto" narrows down.
     *
     * The applicant is always $employee, so a word that identifies them (their name or NIP) is true for every row
     * and adds no constraint; any other word has to appear in the code, the destination or the vehicle name.
     *
     * A short fragment must not count as "the applicant": every NIP contains digits such as "001" or "12", and
     * treating those as a match would make a search for a booking code ("...-001") return everything. So a NIP
     * only matches as a whole number of 6+ digits, and a name only for words of 3+ letters.
     */
    public function scopeMatching(Builder $query, string $terms, Employee $employee): Builder
    {
        $name = mb_strtolower($employee->name);

        foreach (preg_split('/\s+/', mb_strtolower(trim($terms)), -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $isNumber = ctype_digit($word);
            $isApplicant = $isNumber
                ? mb_strlen($word) >= 6 && str_contains($employee->nip, $word)
                : mb_strlen($word) >= 3 && str_contains($name, $word);

            if ($isApplicant) {
                continue;
            }

            $like = '%'.addcslashes($word, '\\%_').'%'; // user input must not act as a LIKE wildcard

            $query->where(fn (Builder $q) => $q
                ->where('code', 'like', $like)
                ->orWhere('destination', 'like', $like)
                ->orWhereHas('vehicle', fn (Builder $v) => $v->where('name', 'like', $like)));
        }

        return $query;
    }

    /**
     * Status as shown to people: an approved booking whose return date is in the past counts as "Selesai".
     * Purely derived — nothing extra is stored, so it needs no "mark as finished" action.
     */
    public function effectiveStatus(): BookingStatus
    {
        return $this->status === BookingStatus::Disetujui && $this->returns_on->isBefore(today())
            ? BookingStatus::Selesai
            : $this->status;
    }

    public function isCancellable(): bool
    {
        return $this->status === BookingStatus::Menunggu;
    }

    public function isSingleDay(): bool
    {
        return $this->departs_on->isSameDay($this->returns_on);
    }

    /** Public booking id shown on the confirmation page, e.g. "#BRV-20261002-001". */
    public function displayId(): string
    {
        return '#BRV-'.substr($this->code, 4);
    }

    /** "2026-10-03" or "2026-10-03 s/d 2026-10-05" */
    public function scheduleLabel(): string
    {
        return $this->isSingleDay()
            ? $this->departs_on->format('Y-m-d')
            : $this->departs_on->format('Y-m-d').' s/d '.$this->returns_on->format('Y-m-d');
    }

    /** "12 Okt 2026" or "12 Okt 2026 – 14 Okt 2026" */
    public function shortScheduleLabel(): string
    {
        $from = $this->departs_on->translatedFormat('j M Y');

        return $this->isSingleDay() ? $from : $from.' – '.$this->returns_on->translatedFormat('j M Y');
    }

    /** Next sequential code of the day: DKS-YYYYMMDD-NNN. Call inside a transaction. */
    public static function nextCode(?Carbon $date = null): string
    {
        $date ??= now();
        $prefix = 'DKS-'.$date->format('Ymd').'-';

        $last = static::query()
            ->where('code', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('code')
            ->value('code');

        $next = $last ? ((int) substr($last, -3)) + 1 : 1;

        return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
