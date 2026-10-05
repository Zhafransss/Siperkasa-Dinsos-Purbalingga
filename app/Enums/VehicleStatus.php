<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case Tersedia = 'tersedia';
    case Dipakai = 'dipakai';
    case Perawatan = 'perawatan';

    public function label(): string
    {
        return match ($this) {
            self::Tersedia => 'Tersedia',
            self::Dipakai => 'Sedang Dipakai',
            self::Perawatan => 'Perawatan',
        };
    }

    /** Wording used on the admin side ("Dalam Tugas" / "Maintenance"). */
    public function adminLabel(): string
    {
        return match ($this) {
            self::Tersedia => 'Tersedia',
            self::Dipakai => 'Dalam Tugas',
            self::Perawatan => 'Maintenance',
        };
    }

    /** Solid pill on the admin vehicle cards (Figma "Manajemen Armada"). */
    public function adminPillClasses(): string
    {
        return match ($this) {
            self::Tersedia => 'bg-success/90 text-white',
            self::Dipakai => 'bg-navy/90 text-white',
            self::Perawatan => 'bg-warning/90 text-on-background',
        };
    }

    /** Colour of the dot in the legend / pill. */
    public function dotClasses(): string
    {
        return match ($this) {
            self::Tersedia => 'bg-success',
            self::Dipakai => 'bg-navy',
            self::Perawatan => 'bg-warning',
        };
    }

    /** Text of the disabled button on a vehicle card that cannot be booked. */
    public function unavailableButtonLabel(): ?string
    {
        return match ($this) {
            self::Tersedia => null,
            self::Dipakai => 'Tidak Tersedia',
            self::Perawatan => 'Sedang Servis',
        };
    }

    /** Badge colours from the Figma catalog cards. */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Tersedia => 'bg-[#dcfce7] text-[#166534]',
            self::Dipakai => 'bg-[#fef3c7] text-[#92400e]',
            self::Perawatan => 'bg-[#fee2e2] text-[#991b1b]',
        };
    }
}
