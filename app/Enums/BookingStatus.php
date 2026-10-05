<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Menunggu = 'menunggu';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';
    case Dibatalkan = 'dibatalkan';

    /**
     * Display-only: an approved booking whose return date has passed. It is derived by Booking::effectiveStatus()
     * and never stored in the database.
     */
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu Persetujuan',
            self::Disetujui => 'Disetujui',
            self::Ditolak => 'Ditolak',
            self::Dibatalkan => 'Dibatalkan',
            self::Selesai => 'Selesai',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Menunggu => 'pending',
            self::Disetujui => 'check_circle',
            self::Ditolak => 'cancel',
            self::Dibatalkan => 'block',
            self::Selesai => 'task_alt',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Menunggu => 'bg-[#fff8e1] text-amber-700',
            self::Disetujui => 'bg-[#e6f4ea] text-green-700',
            self::Ditolak => 'bg-[#fce8e8] text-red-700',
            self::Dibatalkan => 'bg-surface-container text-on-surface-variant',
            self::Selesai => 'bg-[#dcfce7] text-[#15803d]',
        };
    }

    /** Statuses that still hold the vehicle for the requested time range. */
    public static function blocking(): array
    {
        return [self::Menunggu, self::Disetujui];
    }

    /** Statuses of bookings an admin has already dealt with (the "Riwayat" tab); `selesai` is derived from `disetujui`. */
    public static function processed(): array
    {
        return [self::Disetujui, self::Ditolak, self::Dibatalkan];
    }
}
