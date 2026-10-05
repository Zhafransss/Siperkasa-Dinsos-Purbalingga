<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bookings hold a vehicle for whole days: the form no longer asks for a time of day.
     * Existing rows keep their date; the time part is dropped by the column change.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('departs_at', 'departs_on');
            $table->renameColumn('returns_at', 'returns_on');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->date('departs_on')->change();
            $table->date('returns_on')->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dateTime('departs_on')->change();
            $table->dateTime('returns_on')->change();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('departs_on', 'departs_at');
            $table->renameColumn('returns_on', 'returns_at');
        });
    }
};
