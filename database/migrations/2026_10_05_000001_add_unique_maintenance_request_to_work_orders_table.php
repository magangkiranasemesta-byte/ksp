<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Satu Maintenance Request hanya boleh punya satu Work Order.
 * Controller sudah menjaga aturan ini; migration ini menjadikannya
 * jaminan di level database. (NULL tetap boleh berulang.)
 */
return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('work_orders')
            ->select('maintenance_request_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('maintenance_request_id')
            ->groupBy('maintenance_request_id')
            ->having('total', '>', 1)
            ->pluck('maintenance_request_id');

        if ($duplicates->isNotEmpty()) {
            throw new RuntimeException(
                'Tidak dapat menambah unique index: Maintenance Request ID '
                . $duplicates->implode(', ')
                . ' memiliki lebih dari satu Work Order. Batalkan/gabungkan duplikat tersebut lalu jalankan migrate lagi.'
            );
        }

        Schema::table('work_orders', function (Blueprint $table) {
            $table->unique('maintenance_request_id', 'work_orders_maintenance_request_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropUnique('work_orders_maintenance_request_id_unique');
        });
    }
};
