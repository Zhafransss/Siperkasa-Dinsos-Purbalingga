<?php

namespace App\Enums;

enum FuelType: string
{
    case Pertalite = 'pertalite';
    case Pertamax = 'pertamax';
    case Solar = 'solar';
    case Dexlite = 'dexlite';
    case Hybrid = 'hybrid';
    case Listrik = 'listrik';

    public function label(): string
    {
        return match ($this) {
            self::Pertalite => 'Pertalite',
            self::Pertamax => 'Pertamax',
            self::Solar => 'Solar (Diesel)',
            self::Dexlite => 'Dexlite',
            self::Hybrid => 'Hybrid',
            self::Listrik => 'Listrik',
        };
    }
}
