<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->string('model_type', 100);
            $table->unsignedBigInteger('model_id');
            $table->enum('effect', ['allow', 'deny'])->default('allow');
            $table->primary(['permission_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type'], 'idx_model_permissions_model');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_has_permissions');
    }
};