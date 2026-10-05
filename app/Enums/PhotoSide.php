<?php

namespace App\Enums;

/** The four photo slots of a vehicle, in the order shown on the form. */
enum PhotoSide: string
{
    case Depan = 'depan';
    case Samping = 'samping';
    case Belakang = 'belakang';
    case Interior = 'interior';

    public function label(): string
    {
        return match ($this) {
            self::Depan => 'Sisi Depan',
            self::Samping => 'Sisi Samping',
            self::Belakang => 'Sisi Belakang',
            self::Interior => 'Interior',
        };
    }
}
