<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subrole_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('subrole_id')->constrained('subroles')->cascadeOnDelete();
            $table->primary(['permission_id', 'subrole_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subrole_has_permissions');
    }
};