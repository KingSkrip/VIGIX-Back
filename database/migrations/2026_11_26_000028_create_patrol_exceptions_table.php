<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrol_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patrol_run_id')->constrained('patrol_runs')->restrictOnDelete();
            $table->foreignId('run_point_id')->nullable()->constrained('patrol_run_points')->restrictOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('exception_type', 80);
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->dateTime('requested_at');
            $table->dateTime('decided_at')->nullable();
            $table->timestamps();
            $table->index(['patrol_run_id', 'status'], 'idx_exceptions_run');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_exceptions');
    }
};