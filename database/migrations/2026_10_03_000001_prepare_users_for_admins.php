<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `users` are the administrators. They sign in with their NIP ("ID Administrator").
     * The leftover `no_hp` column (unique, required) is dropped: it is not part of this application and would
     * block creating admins.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['no_hp']);
            $table->dropColumn('no_hp');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 18)->unique()->after('name');
            $table->string('bidang')->nullable()->after('nip');
            $table->boolean('is_active')->default(true)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nip']);
            $table->dropColumn(['nip', 'bidang', 'is_active']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('no_hp')->unique()->after('email');
        });
    }
};
