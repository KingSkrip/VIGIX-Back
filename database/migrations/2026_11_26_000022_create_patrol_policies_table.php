<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrol_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->restrictOnDelete();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('patrols_per_day');
            $table->enum('coverage_type', ['all'])->default('all');
            $table->boolean('randomize_order')->default(true);
            $table->boolean('randomize_schedule')->default(true);
            $table->time('window_start');
            $table->time('window_end');
            $table->unsignedSmallInteger('start_tolerance_minutes')->default(15);
            $table->unsignedSmallInteger('end_tolerance_minutes')->default(15);
            $table->unsignedSmallInteger('minimum_interval_minutes')->default(30);
            $table->json('active_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['company_id', 'is_active'], 'idx_policy_company');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_policies');
    }
};