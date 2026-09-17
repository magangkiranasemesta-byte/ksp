<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('asset_number')->unique(); // Nomor aset/inventaris (contoh: DEV-001)
            $table->string('name');                   // Nama perangkat (contoh: Laptop Dell XPS 13)
            $table->string('brand')->nullable();      // Merk/Brand
            $table->string('model')->nullable();      // Tipe/Model
            $table->string('serial_number')->nullable(); // Nomor Seri/SN
            
            // Lokasi & Penanggung Jawab
            $table->string('location')->nullable();   // Lokasi/Ruangan (contoh: Ruang IT Lt. 2)
            $table->enum('status', ['active', 'in_repair', 'damaged', 'retired'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
