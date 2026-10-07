<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menghubungkan modul-modul ke alur Work Order (semua kolom nullable,
 * data lama tidak terpengaruh):
 *
 * - work_orders.preventive_maintenance_id      : WO yang dibuat dari jadwal PM
 * - equipment_downtimes.maintenance_request_id : downtime -> request -> WO
 * - sparepart_usages.work_order_id             : pemakaian sparepart pada WO
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('work_orders', 'preventive_maintenance_id')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->foreignId('preventive_maintenance_id')
                    ->nullable()
                    ->after('maintenance_request_id')
                    ->constrained('preventive_maintenances')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('equipment_downtimes', 'maintenance_request_id')) {
            Schema::table('equipment_downtimes', function (Blueprint $table) {
                $table->foreignId('maintenance_request_id')
                    ->nullable()
                    ->after('equipment_id')
                    ->constrained('maintenance_requests')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('sparepart_usages', 'work_order_id')) {
            Schema::table('sparepart_usages', function (Blueprint $table) {
                $table->foreignId('work_order_id')
                    ->nullable()
                    ->after('maintenance_ticket_id')
                    ->constrained('work_orders')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sparepart_usages', 'work_order_id')) {
            Schema::table('sparepart_usages', function (Blueprint $table) {
                $table->dropConstrainedForeignId('work_order_id');
            });
        }

        if (Schema::hasColumn('equipment_downtimes', 'maintenance_request_id')) {
            Schema::table('equipment_downtimes', function (Blueprint $table) {
                $table->dropConstrainedForeignId('maintenance_request_id');
            });
        }

        if (Schema::hasColumn('work_orders', 'preventive_maintenance_id')) {
            Schema::table('work_orders', function (Blueprint $table) {
                $table->dropConstrainedForeignId('preventive_maintenance_id');
            });
        }
    }
};
