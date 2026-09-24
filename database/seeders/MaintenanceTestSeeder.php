<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Equipment;
use App\Models\MaintenanceRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MaintenanceTestSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */

        $admin = User::updateOrCreate(
            [
                'email' => 'admin@maintenance.test',
            ],
            [
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'SUPERADMIN',
                'permissions' => [
                    'dashboard',
                    'tickets',
                    'maintenance',
                    'history',
                    'equipment',
                    'spareparts',
                    'users',
                    'activity_logs',
                ],
            ]
        );

        $engineer = User::updateOrCreate(
            [
                'email' => 'engineer@maintenance.test',
            ],
            [
                'username' => 'engineer',
                'password' => Hash::make('password'),
                'role' => 'ENGINEER',
                'permissions' => [
                    'dashboard',
                    'maintenance',
                    'equipment',
                    'history',
                ],
            ]
        );

        $technician = User::updateOrCreate(
            [
                'email' => 'technician@maintenance.test',
            ],
            [
                'username' => 'technician',
                'password' => Hash::make('password'),
                'role' => 'TECHNICIAN',
                'permissions' => [
                    'dashboard',
                    'maintenance',
                    'equipment',
                    'history',
                ],
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | EQUIPMENT
        |--------------------------------------------------------------------------
        */

        $compressor = Equipment::updateOrCreate(
            [
                'equipment_code' => 'EQ-CMP-001',
            ],
            [
                'name' => 'Air Compressor 01',
                'location' => 'Production Area A',
                'description' => 'Air compressor untuk kebutuhan produksi.',
                'status' => 'ACTIVE',
            ]
        );

        $packaging = Equipment::updateOrCreate(
            [
                'equipment_code' => 'EQ-PKG-001',
            ],
            [
                'name' => 'Packaging Machine 01',
                'location' => 'Production Area B',
                'description' => 'Mesin packaging untuk proses pengemasan produk.',
                'status' => 'ACTIVE',
            ]
        );

        $boiler = Equipment::updateOrCreate(
            [
                'equipment_code' => 'EQ-BLR-001',
            ],
            [
                'name' => 'Boiler 01',
                'location' => 'Utility Area',
                'description' => 'Boiler untuk kebutuhan steam produksi.',
                'status' => 'ACTIVE',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | MAINTENANCE REQUEST
        |--------------------------------------------------------------------------
        */

        MaintenanceRequest::updateOrCreate(
            [
                'equipment_id' => $compressor->id,
                'description' => 'Tekanan compressor menurun saat digunakan.',
            ],
            [
                'engineer_id' => $engineer->id,
                'priority' => 'HIGH',
                'status' => 'APPROVED',
            ]
        );

        MaintenanceRequest::updateOrCreate(
            [
                'equipment_id' => $packaging->id,
                'description' => 'Mesin packaging mengalami getaran saat beroperasi.',
            ],
            [
                'engineer_id' => $engineer->id,
                'priority' => 'MEDIUM',
                'status' => 'APPROVED',
            ]
        );

        MaintenanceRequest::updateOrCreate(
            [
                'equipment_id' => $boiler->id,
                'description' => 'Pemeriksaan rutin kondisi boiler.',
            ],
            [
                'engineer_id' => $engineer->id,
                'priority' => 'LOW',
                'status' => 'APPROVED',
            ]
        );


        $this->command->info('Data testing Maintenance X berhasil dibuat.');
        $this->command->info('Admin     : admin@maintenance.test / password');
        $this->command->info('Engineer  : engineer@maintenance.test / password');
        $this->command->info('Technician: technician@maintenance.test / password');
    }
}