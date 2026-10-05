<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // The card now only shows name, description and (optionally) capacity.
            $table->dropColumn(['spec_label', 'spec_icon']);
            $table->string('capacity_label')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('spec_label')->default('');
            $table->string('spec_icon')->default('settings_input_component');
            $table->string('capacity_label')->nullable(false)->change();
        });
    }
};
