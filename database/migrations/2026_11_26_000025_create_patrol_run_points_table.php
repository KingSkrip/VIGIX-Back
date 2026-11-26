<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrol_run_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patrol_run_id')->constrained('patrol_runs')->restrictOnDelete();
            $table->foreignId('qr_point_id')->nullable()->constrained('qr_points')->restrictOnDelete();
            $table->string('point_name', 120);
            $table->string('point_code', 40);
            $table->string('wing_name', 120);
            $table->unsignedInteger('sequence_number');
            $table->dateTime('planned_at')->nullable();
            $table->dateTime('scanned_at')->nullable();
            $table->enum('status', ['pending', 'completed', 'late', 'missed', 'exception'])->default('pending');
            $table->timestamps();
            $table->unique(['patrol_run_id', 'sequence_number'], 'uq_run_sequence');
            $table->unique(['patrol_run_id', 'qr_point_id'], 'uq_run_qr');
            $table->index(['patrol_run_id', 'status'], 'idx_run_point_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_run_points');
    }
};