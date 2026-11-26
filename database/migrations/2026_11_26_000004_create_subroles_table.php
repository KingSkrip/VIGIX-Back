<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subroles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->restrictOnDelete();
            $table->string('name', 100);
            $table->string('slug', 120);
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['role_id', 'slug'], 'uq_subroles_role_slug');
            $table->index(['role_id', 'is_active'], 'idx_subroles_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subroles');
    }
};