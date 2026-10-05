<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Figma "Manajemen Pegawai": email dinas, jabatan, pangkat/golongan. */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('name');
            $table->string('jabatan')->nullable()->after('bidang');
            $table->string('pangkat')->nullable()->after('jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropColumn(['email', 'jabatan', 'pangkat']);
        });
    }
};
