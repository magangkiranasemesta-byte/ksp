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
        Schema::create('sparepart_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sparepart_id')
                ->constrained('spareparts')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->unsignedBigInteger('maintenance_ticket_id')
                ->nullable();

            $table->unsignedInteger('quantity');

            $table->text('notes')
                ->nullable();

            $table->timestamp('used_at')
                ->useCurrent();

            $table->timestamps();

            $table->index('maintenance_ticket_id');
            $table->index('used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepart_usages');
    }
};