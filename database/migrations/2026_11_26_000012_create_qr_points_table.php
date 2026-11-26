<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wing_id')->constrained('wings')->restrictOnDelete();
            $table->string('name', 120);
            $table->string('code', 40);
            $table->text('description')->nullable();
            $table->char('token_hash', 64)->unique();
            $table->unsignedInteger('sequence_number');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedSmallInteger('geofence_radius')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['wing_id', 'code'], 'uq_qr_wing_code');
            $table->index(['wing_id', 'is_active'], 'idx_qr_wing_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_points');
    }
};