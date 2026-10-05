<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('type')->index();
            $table->string('plate')->unique();
            // Two short spec labels shown with icons on the card (e.g. "7 Kursi" / "Otomatis").
            $table->string('capacity_label');
            $table->string('spec_label');
            $table->string('status')->default('tersedia')->index();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
