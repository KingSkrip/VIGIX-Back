<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->char('uuid', 36)->unique();
            $table->string('name', 150)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('model', 150)->nullable();
            $table->string('device_type', 40)->nullable();
            $table->string('operating_system', 80)->nullable();
            $table->string('os_version', 80)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('browser_version', 80)->nullable();
            $table->string('app_version', 40)->nullable();
            $table->string('platform', 40)->nullable();
            $table->string('last_ip', 45)->nullable();
            $table->string('registration_ip', 45)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'revoked'])->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status'], 'idx_devices_user_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};