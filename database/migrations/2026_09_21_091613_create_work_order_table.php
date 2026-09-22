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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Reference
            |--------------------------------------------------------------------------
            */

            // Maintenance Request asal Work Order
            $table->foreignId('maintenance_request_id')
                ->nullable()
                ->constrained('maintenance_requests')
                ->nullOnDelete();

            // Equipment yang dikerjakan
            $table->foreignId('equipment_id')
                ->constrained('equipment')
                ->restrictOnDelete();

            // Technician / Engineer yang ditugaskan
            $table->foreignId('technician_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Work Order Information
            |--------------------------------------------------------------------------
            */

            // Contoh: WO-20260921-0001
            $table->string('wo_number')->unique();

            // CORRECTIVE / PREVENTIVE / INSPECTION
            $table->string('maintenance_type')->default('CORRECTIVE');

            // LOW / MEDIUM / HIGH / CRITICAL
            $table->string('priority')->default('MEDIUM');


            /*
            |--------------------------------------------------------------------------
            | Maintenance Description
            |--------------------------------------------------------------------------
            */

            $table->text('problem_description')->nullable();

            $table->text('root_cause')->nullable();

            $table->text('corrective_action')->nullable();


            /*
            |--------------------------------------------------------------------------
            | Schedule
            |--------------------------------------------------------------------------
            */

            $table->dateTime('planned_start')->nullable();

            $table->dateTime('planned_end')->nullable();

            $table->dateTime('actual_start')->nullable();

            $table->dateTime('actual_end')->nullable();


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            // OPEN / ASSIGNED / IN_PROGRESS / ON_HOLD / COMPLETED / CANCELLED
            $table->string('status')->default('OPEN');


            /*
            |--------------------------------------------------------------------------
            | Completion
            |--------------------------------------------------------------------------
            */

            $table->text('completion_notes')->nullable();


            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index('status');
            $table->index('priority');
            $table->index('maintenance_type');
            $table->index('planned_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};