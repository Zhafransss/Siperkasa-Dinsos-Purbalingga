<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Material Symbols names shown next to the two spec labels on the vehicle card.
            $table->string('capacity_icon')->default('groups')->after('capacity_label');
            $table->string('spec_icon')->default('settings_input_component')->after('spec_label');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['capacity_icon', 'spec_icon']);
        });
    }
};
