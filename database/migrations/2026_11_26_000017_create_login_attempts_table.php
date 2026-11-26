<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email', 190)->nullable();
            $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('authentication_method', 40);
            $table->boolean('successful')->default(false);
            $table->string('failure_reason', 255)->nullable();
            $table->timestamp('attempted_at');
            $table->index(['email', 'attempted_at'], 'idx_login_email');
            $table->index(['ip_address', 'attempted_at'], 'idx_login_ip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};