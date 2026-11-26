<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrol_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patrol_run_id')->constrained('patrol_runs')->restrictOnDelete();
            $table->foreignId('guard_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('qr_point_id')->nullable()->constrained('qr_points')->restrictOnDelete();
            $table->string('incident_type', 80);
            $table->string('title', 150);
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['pending', 'reviewing', 'resolved', 'rejected'])->default('pending');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->text('resolution_notes')->nullable();
            $table->dateTime('reported_at');
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'priority'], 'idx_incidents_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_incidents');
    }
};