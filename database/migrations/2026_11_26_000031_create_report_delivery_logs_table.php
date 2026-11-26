<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_export_id')->constrained('report_exports')->restrictOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->restrictOnDelete();
            $table->string('delivery_channel', 40);
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();
            $table->index(['recipient_id', 'status'], 'idx_delivery_recipient');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_delivery_logs');
    }
};