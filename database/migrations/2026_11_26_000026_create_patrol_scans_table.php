<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrol_scans', function (Blueprint $table) {
            $table->id();
            $table->char('sync_uuid', 36)->unique();
            $table->foreignId('patrol_run_id')->constrained('patrol_runs')->restrictOnDelete();
            $table->foreignId('run_point_id')->nullable()->constrained('patrol_run_points')->restrictOnDelete();
            $table->foreignId('qr_point_id')->nullable()->constrained('qr_points')->restrictOnDelete();
            $table->foreignId('guard_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('device_id')->nullable()->constrained('devices')->restrictOnDelete();
            $table->dateTime('client_scanned_at', 6)->nullable();
            $table->dateTime('received_at', 6)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('source', ['online', 'offline_sync'])->default('online');
            $table->enum('validation_status', ['pending', 'validated', 'rejected'])->default('pending');
            $table->string('validation_reason', 255)->nullable();
            $table->dateTime('validated_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['patrol_run_id', 'received_at'], 'idx_scans_run');
            $table->index(['guard_id', 'client_scanned_at'], 'idx_scans_guard');
            $table->index(['validation_status', 'source'], 'idx_scans_validation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_scans');
    }
};