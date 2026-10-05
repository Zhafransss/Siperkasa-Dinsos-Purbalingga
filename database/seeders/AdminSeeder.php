<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * First administrator (there is no public sign-up page; further admins are created in "Manajemen Admin").
     * DEMO credentials for local development — change the password before any real deployment.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['nip' => '199001012015011001'],
            [
                'name' => 'Administrator Utama',
                'bidang' => 'Sekretariat',
                'email' => 'admin@purbalinggakab.go.id',
                'password' => 'Admin@12345', // hashed by the model cast
                'is_active' => true,
            ],
        );
    }
}
