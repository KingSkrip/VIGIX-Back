<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_point_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qr_point_id')->nullable()->constrained('qr_points')->nullOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['qr_point_id', 'created_at'], 'idx_qr_history');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_point_history');
    }
};