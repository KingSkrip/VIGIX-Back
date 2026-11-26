<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrol_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('patrol_schedules')->restrictOnDelete();
            $table->foreignId('guard_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->restrictOnDelete();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->unsignedInteger('total_points')->default(0);
            $table->unsignedInteger('completed_points')->default(0);
            $table->enum('status', ['assigned', 'in_progress', 'completed', 'incomplete', 'missed', 'cancelled'])->default('assigned');
            $table->text('completion_notes')->nullable();
            $table->timestamps();
            $table->unique(['schedule_id', 'guard_id'], 'uq_run_schedule_guard');
            $table->index(['guard_id', 'status'], 'idx_runs_guard');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_runs');
    }
};