<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /** Demo bookings relative to "today" so the calendar always shows upcoming data. */
    public function run(): void
    {
        if (Booking::query()->exists()) {
            return;
        }

        // Avanza is in the workshop for a few days so the calendar's "Perawatan" chip has data.
        VehicleMaintenance::updateOrCreate(
            ['vehicle_id' => Vehicle::where('plate', 'R 9911 PE')->value('id'), 'starts_on' => now()->addDays(8)->toDateString()],
            ['ends_on' => now()->addDays(10)->toDateString(), 'reason' => 'Servis rutin'],
        );

        $bambang = Employee::where('nip', '198811042012021001')->firstOrFail();
        $siti = Employee::where('nip', '198203152008012004')->firstOrFail();
        $vehicle = fn (string $plate) => Vehicle::where('plate', $plate)->firstOrFail();

        $rows = [
            [$bambang, 'R 1234 PA', 3, 3, 'Puskesmas Kaligondang', 'Distribusi obat parasetamol', BookingStatus::Disetujui, 'Harap menjaga protokol berkendara dan mengembalikan unit tepat waktu.'],
            [$siti, 'R 5678 QB', 3, 3, 'Dinas Kesehatan Provinsi Jawa Tengah (Semarang)', 'Rapat Koordinasi Evaluasi Anggaran & DAK Kesehatan', BookingStatus::Disetujui, 'Silahkan ambil kunci ke bagian umum'],
            [$bambang, 'R 3344 PD', 10, 11, 'Rumah Sakit Margono', 'Monitoring layanan rujukan', BookingStatus::Menunggu, null],
            [$bambang, 'R 7788 PF', 5, 5, 'Dinkes Provinsi Jateng', 'Kegiatan lintas bidang', BookingStatus::Ditolak, 'Mobil sudah dipinjam untuk tanggal tersebut oleh Bidang Kesmas untuk agenda prioritas.'],
        ];

        foreach ($rows as [$employee, $plate, $fromDay, $toDay, $destination, $purpose, $status, $note]) {
            Booking::create([
                'code' => Booking::nextCode(),
                'employee_id' => $employee->id,
                'vehicle_id' => $vehicle($plate)->id,
                'departs_on' => now()->addDays($fromDay)->toDateString(),
                'returns_on' => now()->addDays($toDay)->toDateString(),
                'destination' => $destination,
                'purpose' => $purpose,
                'status' => $status,
                'admin_note' => $note,
            ]);
        }
    }
}
