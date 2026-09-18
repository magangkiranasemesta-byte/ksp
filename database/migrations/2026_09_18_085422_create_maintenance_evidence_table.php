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
        Schema::create('maintenance_evidence', function (Blueprint $table) {
            $table->id();

            // Ticket yang memiliki evidence
            $table->foreignId('ticket_id')
                ->constrained('maintenance_tickets')
                ->cascadeOnDelete();

            // Tahap evidence: before, process, after
            $table->string('stage', 20);

            // Lokasi file gambar
            $table->string('image_path');

            // Keterangan foto
            $table->text('description')->nullable();

            // User yang mengupload
            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Mempermudah pencarian evidence berdasarkan ticket dan tahap
            $table->index(['ticket_id', 'stage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_evidence');
    }
};