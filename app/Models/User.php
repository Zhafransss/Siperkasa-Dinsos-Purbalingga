<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * An administrator. Signs in with the NIP ("ID Administrator") and a password; created and managed by other admins.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'nip', 'bidang', 'email', 'password', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /** Two-letter avatar text: "Agus Setiawan" -> "AS" (titles like "dr." and "Drs." are skipped). */
    public function initials(): string
    {
        return self::initialsOf($this->name);
    }

    public static function initialsOf(string $name): string
    {
        $words = collect(preg_split('/\s+/', trim($name)))
            ->reject(fn ($w) => preg_match('/^(dr|drs|dra|ir|apt|prof|h|hj)\.?,?$/i', $w))
            ->values();

        $letters = $words->take(2)->map(fn ($w) => Str::upper(Str::substr($w, 0, 1)))->implode('');

        return $letters !== '' ? $letters : Str::upper(Str::substr($name, 0, 2));
    }

    /** True when this is the only active administrator left (deactivating or deleting it would lock everyone out). */
    public function isLastActiveAdmin(): bool
    {
        return $this->is_active && ! static::query()->where('is_active', true)->whereKeyNot($this->getKey())->exists();
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $like = '%'.addcslashes(trim($term), '\%_').'%';

        return $query->when(trim($term) !== '', fn (Builder $q) => $q->where(
            fn (Builder $w) => $w->where('name', 'like', $like)->orWhere('nip', 'like', $like)->orWhere('email', 'like', $like)
        ));
    }
}
