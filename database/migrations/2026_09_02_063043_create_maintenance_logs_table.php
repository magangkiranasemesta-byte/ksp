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
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            // Foreign Keys
            $table->foreignId('ticket_id')->constrained('maintenance_tickets')->onDelete('cascade');
            $table->foreignId('technician_id')->constrained('users')->onDelete('cascade');
            
            // Detail Tindakan Perbaikan
            $table->text('action_taken'); // Penjelasan tindakan yang dilakukan
            $table->enum('maintenance_type', ['corrective', 'preventive'])->default('corrective');
            $table->decimal('cost', 12, 2)->default(0); // Biaya perbaikan jika ada
            
            // Waktu Pengerjaan
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
