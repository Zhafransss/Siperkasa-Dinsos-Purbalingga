<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['nip', 'name', 'email', 'bidang', 'jabatan', 'pangkat', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** Active employee by NIP, or null when unknown / deactivated. */
    public static function findActiveByNip(string $nip): ?self
    {
        return static::query()->where('nip', $nip)->where('is_active', true)->first();
    }

    /** NIP with the middle digits hidden, for places visible to other staff (public calendar). */
    public function maskedNip(): string
    {
        return substr($this->nip, 0, 6).str_repeat('•', 8).substr($this->nip, -4);
    }

    /** "Bidang P2P" and "P2P" both render as "P2P". */
    public function bidangShort(): string
    {
        return trim(preg_replace('/^Bidang\s+/i', '', $this->bidang));
    }

    public function initials(): string
    {
        return User::initialsOf($this->name);
    }

    /** Search by name, NIP or e-mail. */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $like = '%'.addcslashes(trim($term), '\%_').'%';

        return $query->when(trim($term) !== '', fn (Builder $q) => $q->where(
            fn (Builder $w) => $w->where('name', 'like', $like)->orWhere('nip', 'like', $like)->orWhere('email', 'like', $like)
        ));
    }
}
