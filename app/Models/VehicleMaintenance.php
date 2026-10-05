<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class VehicleMaintenance extends Model
{
    use HasFactory;

    protected $fillable = ['vehicle_id', 'starts_on', 'ends_on', 'reason'];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /** Maintenance periods that touch the (inclusive) date range [$from, $to]. */
    public function scopeOverlappingDates(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereDate('starts_on', '<=', $to->toDateString())
            ->whereDate('ends_on', '>=', $from->toDateString());
    }
}
