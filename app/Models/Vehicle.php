<?php

namespace App\Models;

use App\Enums\FuelType;
use App\Enums\PhotoSide;
use App\Enums\VehicleCategory;
use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'brand_model', 'plate', 'year', 'fuel_type', 'capacity', 'odometer_km', 'color',
        'last_serviced_on', 'category', 'status', 'image_path',
    ];

    protected function casts(): array
    {
        return [
            'category' => VehicleCategory::class,
            'fuel_type' => FuelType::class,
            'status' => VehicleStatus::class,
            'last_serviced_on' => 'date',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(VehiclePhoto::class);
    }

    public function isBookable(): bool
    {
        return $this->status === VehicleStatus::Tersedia;
    }

    /** "7 Kursi", or null when no capacity is recorded (e.g. an ambulance). */
    public function capacityLabel(): ?string
    {
        return $this->capacity ? $this->capacity.' Kursi' : null;
    }

    /** "Toyota Hiace Premio 2023" — the model line shown under the plate on admin cards. */
    public function modelLine(): string
    {
        return trim(($this->brand_model ?: $this->name).' '.($this->year ?: ''));
    }

    /** One photo per side; a photo of the given side, if uploaded. */
    public function photoFor(PhotoSide $side): ?VehiclePhoto
    {
        return $this->photos->firstWhere('side', $side);
    }

    /**
     * Main picture: the front photo, else any uploaded photo, else the bundled image of the seeded fleet.
     * Eager-load `photos` on lists to avoid one query per card.
     */
    public function imageUrl(): ?string
    {
        $photo = $this->photoFor(PhotoSide::Depan) ?? $this->photos->sortBy('id')->first();

        if ($photo) {
            return $photo->url();
        }

        return $this->image_path ? asset($this->image_path) : null;
    }

    /** Free-text search over name, brand/model and plate. */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $like = '%'.addcslashes(trim($term), '\%_').'%';

        return $query->when(trim($term) !== '', fn (Builder $q) => $q->where(
            fn (Builder $w) => $w->where('name', 'like', $like)->orWhere('brand_model', 'like', $like)->orWhere('plate', 'like', $like)
        ));
    }
}
