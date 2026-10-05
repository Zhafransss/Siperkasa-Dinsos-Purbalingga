<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            ['nip' => '198811042012021001', 'name' => 'dr. Bambang Sulistyo, Sp.P', 'email' => 'bambang.s@purbalinggakab.go.id', 'bidang' => 'Bidang P2P', 'jabatan' => 'Kepala Seksi Surveilans', 'pangkat' => 'Penata / IIIc', 'is_active' => true],
            ['nip' => '198203152008012004', 'name' => 'Siti Rahmawati, S.ST, M.Si', 'email' => 'siti.r@purbalinggakab.go.id', 'bidang' => 'Sekretariat', 'jabatan' => 'Kepala Subbagian Umum', 'pangkat' => 'Penata Tk. I / IIId', 'is_active' => true],
            ['nip' => '198501102009021002', 'name' => 'Apt. Hendra Setiawan, S.Farm', 'email' => 'hendra.s@purbalinggakab.go.id', 'bidang' => 'Bidang SDK', 'jabatan' => 'Apoteker Ahli Muda', 'pangkat' => 'Penata Muda Tk. I / IIIb', 'is_active' => true],
            ['nip' => '197508122002121003', 'name' => 'dr. Jatmiko Yudo, M.Kes', 'email' => 'jatmiko.y@purbalinggakab.go.id', 'bidang' => 'Sekretariat', 'jabatan' => 'Kepala Bidang', 'pangkat' => 'Pembina / IVa', 'is_active' => true],
            // Inactive on purpose: lets you try the "NIP non-aktif" error state.
            ['nip' => '196512311990031009', 'name' => 'Pegawai Non-Aktif (Demo)', 'email' => null, 'bidang' => 'Sekretariat', 'jabatan' => null, 'pangkat' => null, 'is_active' => false],
        ];

        foreach ($employees as $employee) {
            Employee::updateOrCreate(['nip' => $employee['nip']], $employee);
        }
    }
}
