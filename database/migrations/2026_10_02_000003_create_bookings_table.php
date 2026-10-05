<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete();
            $table->dateTime('departs_at');
            $table->dateTime('returns_at');
            $table->string('destination');
            $table->text('purpose');
            $table->string('status')->default('menunggu');
            $table->text('admin_note')->nullable();
            $table->timestamps();

            // Overlap lookups (availability check + calendar) filter by vehicle and time range.
            $table->index(['vehicle_id', 'departs_at', 'returns_at']);
            $table->index(['employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
