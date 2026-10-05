<?php

namespace App\Enums;

/** Purpose-based classification of the fleet (admin vehicle management). */
enum VehicleCategory: string
{
    case GawatDarurat = 'gawat_darurat';
    case Jabatan = 'jabatan';
    case Operasional = 'operasional';

    public function label(): string
    {
        return match ($this) {
            self::GawatDarurat => 'Gawat Darurat',
            self::Jabatan => 'Jabatan',
            self::Operasional => 'Operasional',
        };
    }

    /** Text of the dark pill on a vehicle photo ("Kendaraan Dinas Jabatan"). */
    public function pillLabel(): string
    {
        return match ($this) {
            self::GawatDarurat => 'Kendaraan Gawat Darurat',
            self::Jabatan => 'Kendaraan Dinas Jabatan',
            self::Operasional => 'Kendaraan Operasional',
        };
    }
}
