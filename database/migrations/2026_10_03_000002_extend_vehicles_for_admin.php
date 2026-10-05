<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Admin vehicle form (Figma): brand/model, year, fuel, capacity, odometer, colour, last service, category.
     * `category` (Gawat Darurat / Jabatan / Operasional) replaces the old `type` (mpv/suv/ambulans/bus); the free-text
     * `capacity_label`/`capacity_icon` give way to a numeric `capacity` (the "N Kursi" label is derived).
     */
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('brand_model')->nullable()->after('name');
            $table->unsignedSmallInteger('year')->nullable()->after('plate');
            $table->string('fuel_type')->nullable()->after('year');
            $table->unsignedSmallInteger('capacity')->nullable()->after('fuel_type');
            $table->unsignedInteger('odometer_km')->nullable()->after('capacity');
            $table->string('color')->nullable()->after('odometer_km');
            $table->date('last_serviced_on')->nullable()->after('color');
            $table->string('category')->default('operasional')->after('last_serviced_on')->index();
        });

        // Carry existing rows over before the old columns disappear.
        foreach (DB::table('vehicles')->get(['id', 'name', 'type', 'capacity_label']) as $row) {
            DB::table('vehicles')->where('id', $row->id)->update([
                'brand_model' => $row->name,
                'category' => $row->type === 'ambulans' ? 'gawat_darurat' : 'operasional',
                'capacity' => preg_match('/\d+/', (string) $row->capacity_label, $m) ? (int) $m[0] : null,
            ]);
        }

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn(['type', 'capacity_label', 'capacity_icon']);
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('type')->default('mpv')->index();
            $table->string('capacity_label')->nullable();
            $table->string('capacity_icon')->default('groups');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn(['brand_model', 'year', 'fuel_type', 'capacity', 'odometer_km', 'color', 'last_serviced_on', 'category']);
        });
    }
};
