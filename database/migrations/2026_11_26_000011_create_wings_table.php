<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('name', 120);
            $table->string('code', 40);
            $table->text('description')->nullable();
            $table->string('location_description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id', 'code'], 'uq_wings_company_code');
            $table->index(['company_id', 'is_active'], 'idx_wings_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wings');
    }
};