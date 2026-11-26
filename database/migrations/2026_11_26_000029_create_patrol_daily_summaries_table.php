<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrol_daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('guard_id')->constrained('users')->restrictOnDelete();
            $table->date('report_date');
            $table->unsignedInteger('scheduled_patrols')->default(0);
            $table->unsignedInteger('completed_patrols')->default(0);
            $table->unsignedInteger('incomplete_patrols')->default(0);
            $table->unsignedInteger('missed_patrols')->default(0);
            $table->unsignedInteger('expected_points')->default(0);
            $table->unsignedInteger('completed_points')->default(0);
            $table->unsignedInteger('incidents_count')->default(0);
            $table->unsignedInteger('late_scans')->default(0);
            $table->decimal('compliance_percentage', 5, 2)->default(0);
            $table->dateTime('generated_at');
            $table->timestamps();
            $table->unique(['company_id', 'guard_id', 'report_date'], 'uq_daily_summary');
            $table->index(['report_date', 'company_id'], 'idx_daily_company_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_daily_summaries');
    }
};