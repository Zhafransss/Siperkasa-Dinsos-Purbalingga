<?php

namespace App\Models;

use App\Enums\PhotoSide;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiclePhoto extends Model
{
    /** Files live under public/uploads/vehicles (disk "vehicle_photos"), so no storage:link is needed. */
    public const DISK = 'vehicle_photos';

    protected $fillable = ['vehicle_id', 'side', 'path'];

    protected function casts(): array
    {
        return ['side' => PhotoSide::class];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function url(): string
    {
        return asset('uploads/vehicles/'.$this->path);
    }
}
