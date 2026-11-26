<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete();
            $table->char('session_uuid', 36)->unique();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->enum('status', ['active', 'expired', 'closed', 'revoked'])->default('active');
            $table->string('termination_reason', 100)->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status'], 'idx_sessions_user');
            $table->index(['device_id', 'status'], 'idx_sessions_device');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};