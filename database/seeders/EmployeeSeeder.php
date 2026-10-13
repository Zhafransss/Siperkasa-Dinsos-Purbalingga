<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            ['nip' => '198811042012021001', 'name' => 'dr. Bambang Sulistyo, Sp.P', 'bidang' => 'Bidang Pembinaan Sekolah Dasar', 'jabatan' => 'Kepala Seksi Surveilans', 'pangkat' => 'Penata / IIIc', 'is_active' => true],
            ['nip' => '198203152008012004', 'name' => 'Siti Rahmawati, S.ST, M.Si', 'bidang' => 'Sekretariat', 'jabatan' => 'Kepala Subbagian Umum', 'pangkat' => 'Penata Tk. I / IIId', 'is_active' => true],
            ['nip' => '198501102009021002', 'name' => 'Apt. Hendra Setiawan, S.Farm', 'bidang' => 'Bidang Pembinaan Ketenagaan', 'jabatan' => 'Apoteker Ahli Muda', 'pangkat' => 'Penata Muda Tk. I / IIIb', 'is_active' => true],
            ['nip' => '197508122002121003', 'name' => 'dr. Jatmiko Yudo, M.Kes', 'bidang' => 'Sekretariat', 'jabatan' => 'Kepala Bidang', 'pangkat' => 'Pembina / IVa', 'is_active' => true],
            // Inactive on purpose: lets you try the "NIP non-aktif" error state.
            ['nip' => '196512311990031009', 'name' => 'Pegawai Non-Aktif (Demo)', 'bidang' => 'Sekretariat', 'jabatan' => null, 'pangkat' => null, 'is_active' => false],
        ];

        foreach ($employees as $employee) {
            Employee::updateOrCreate(['nip' => $employee['nip']], $employee);
        }
    }
}
