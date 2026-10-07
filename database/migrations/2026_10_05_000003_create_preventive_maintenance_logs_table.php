<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Riwayat pelaksanaan Preventive Maintenance.
 * Satu baris = satu siklus yang diselesaikan.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('preventive_maintenance_logs')) {
            return;
        }

        Schema::create('preventive_maintenance_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('preventive_maintenance_id')
                ->constrained('preventive_maintenances')
                ->cascadeOnDelete();

            $table->foreignId('work_order_id')
                ->nullable()
                ->constrained('work_orders')
                ->nullOnDelete();

            $table->foreignId('performed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('due_date')->nullable();          // jadwal yang seharusnya
            $table->dateTime('performed_at');              // waktu dilaksanakan
            $table->date('next_maintenance_date')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('performed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preventive_maintenance_logs');
    }
};
