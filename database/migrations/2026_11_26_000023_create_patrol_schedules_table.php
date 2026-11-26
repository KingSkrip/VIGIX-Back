<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrol_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained('patrol_policies')->restrictOnDelete();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->date('scheduled_date');
            $table->unsignedSmallInteger('patrol_number');
            $table->dateTime('planned_start_at');
            $table->dateTime('planned_end_at');
            $table->string('generation_reference', 100)->nullable();
            $table->enum('status', ['generated', 'assigned', 'in_progress', 'completed', 'incomplete', 'cancelled'])->default('generated');
            $table->dateTime('generated_at');
            $table->timestamps();
            $table->unique(['policy_id', 'scheduled_date', 'patrol_number'], 'uq_schedule_policy_day');
            $table->index(['company_id', 'scheduled_date'], 'idx_schedule_company_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_schedules');
    }
};