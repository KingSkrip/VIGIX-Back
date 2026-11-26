<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120);
            $table->string('description', 255)->nullable();
            $table->string('guard_name', 50)->default('web');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['slug', 'guard_name'], 'uq_roles_slug_guard');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};