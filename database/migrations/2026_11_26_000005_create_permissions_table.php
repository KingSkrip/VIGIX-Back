<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 150)->unique();
            $table->string('module', 80);
            $table->string('description', 255)->nullable();
            $table->string('guard_name', 50)->default('web');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['module', 'is_active'], 'idx_permissions_module');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};