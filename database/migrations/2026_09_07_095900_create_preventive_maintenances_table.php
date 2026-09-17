<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preventive_maintenances', function (Blueprint $table) {
            $table->date('next_maintenance')->nullable()->after('frequency');
        });
    }

    public function down(): void
    {
        Schema::table('preventive_maintenances', function (Blueprint $table) {
            $table->dropColumn('next_maintenance');
        });
    }
};