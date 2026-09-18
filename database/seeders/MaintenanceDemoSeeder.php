<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\MaintenanceEvidence;
use App\Models\MaintenanceTicket;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MaintenanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Ambil User
        |--------------------------------------------------------------------------
        */

        $user = User::first();

        if (!$user) {
            $this->command->error(
                'Tidak ada User di database. Buat user terlebih dahulu.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Cari atau buat Device Demo
        |--------------------------------------------------------------------------
        */

        $device = Device::where(
            'asset_number',
            'DEV-DEMO-001'
        )->first();

        if (!$device) {
            $device = Device::create([
                'asset_number' => 'DEV-DEMO-001',
                'name' => 'Mesin Produksi Demo',
                'brand' => 'Demo Equipment',
                'model' => 'DEMO-MACHINE-01',
                'serial_number' => 'SN-DEMO-001',
                'location' => 'Area Produksi',
                'status' => 'in_repair',
            ]);

            $this->command->info(
                'Device Demo berhasil dibuat.'
            );
        } else {
            $this->command->info(
                'Device Demo sudah ada, menggunakan data yang sudah tersedia.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Buat nomor ticket unik
        |--------------------------------------------------------------------------
        */

        do {
            $ticketNumber =
                'TKT-' .
                now()->format('Ymd') .
                '-DEMO-' .
                strtoupper(Str::random(4));

        } while (
            MaintenanceTicket::where(
                'ticket_number',
                $ticketNumber
            )->exists()
        );

        /*
        |--------------------------------------------------------------------------
        | 4. Buat Ticket Demo
        |--------------------------------------------------------------------------
        */

        $ticket = MaintenanceTicket::create([
            'ticket_number' => $ticketNumber,
            'device_id' => $device->id,
            'reported_by' => $user->id,
            'assigned_to' => null,
            'title' => 'Demo Maintenance - Pemeriksaan Mesin',
            'description' =>
                'Ticket dummy untuk pengujian fitur Evidence Gallery Maintenance. '
                . 'Dokumentasi terdiri dari kondisi Before, Process, dan After.',
            'priority' => 'high',
            'status' => 'in_progress',
            'image_proof' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 5. Status History
        |--------------------------------------------------------------------------
        */

        TicketStatusHistory::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'old_status' => null,
            'new_status' => 'open',
            'notes' => 'Ticket demo dibuat untuk pengujian Evidence Gallery.',
        ]);

        TicketStatusHistory::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'old_status' => 'open',
            'new_status' => 'in_progress',
            'notes' => 'Ticket demo masuk ke tahap proses maintenance.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 6. Folder Evidence
        |--------------------------------------------------------------------------
        */

        $disk = Storage::disk('public');

        $folder = 'maintenance-evidence/' . $ticket->id;

        $disk->makeDirectory($folder);

        /*
        |--------------------------------------------------------------------------
        | 7. Data Evidence
        |--------------------------------------------------------------------------
        */

        $evidences = [
            [
                'stage' => 'before',
                'filename' => 'demo-before.jpg',
                'description' =>
                    'Kondisi mesin sebelum dilakukan maintenance. '
                    . 'Ditemukan indikasi kerusakan pada komponen mesin.',
            ],
            [
                'stage' => 'process',
                'filename' => 'demo-process.jpg',
                'description' =>
                    'Engineer melakukan pemeriksaan dan proses perbaikan '
                    . 'pada komponen mesin.',
            ],
            [
                'stage' => 'after',
                'filename' => 'demo-after.jpg',
                'description' =>
                    'Kondisi mesin setelah dilakukan maintenance dan '
                    . 'pemeriksaan akhir.',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | 8. Generate 3 Foto Dummy
        |--------------------------------------------------------------------------
        */

        foreach ($evidences as $data) {
            $path = $folder . '/' . $data['filename'];

            if ($disk->exists($path)) {
                $disk->delete($path);
            }

            /*
            |--------------------------------------------------------------------------
            | Buat gambar menggunakan GD
            |--------------------------------------------------------------------------
            */

            $image = imagecreatetruecolor(
                1200,
                700
            );

            $background = imagecolorallocate(
                $image,
                241,
                245,
                249
            );

            $dark = imagecolorallocate(
                $image,
                15,
                23,
                42
            );

            $gray = imagecolorallocate(
                $image,
                71,
                85,
                105
            );

            $border = imagecolorallocate(
                $image,
                148,
                163,
                184
            );

            imagefill(
                $image,
                0,
                0,
                $background
            );

            imagerectangle(
                $image,
                20,
                20,
                1180,
                680,
                $border
            );

            $stageTitle = strtoupper(
                $data['stage']
            );

            imagestring(
                $image,
                5,
                500,
                250,
                'MAINTENANCE',
                $dark
            );

            imagestring(
                $image,
                5,
                550,
                310,
                $stageTitle,
                $dark
            );

            imagestring(
                $image,
                4,
                470,
                370,
                'EVIDENCE GALLERY',
                $gray
            );

            imagestring(
                $image,
                3,
                490,
                410,
                'Demo Maintenance Equipment',
                $gray
            );

            ob_start();

            imagejpeg(
                $image,
                null,
                90
            );

            $imageContent = ob_get_clean();

            imagedestroy($image);

            $disk->put(
                $path,
                $imageContent
            );

            /*
            |--------------------------------------------------------------------------
            | Simpan Evidence ke Database
            |--------------------------------------------------------------------------
            */

            MaintenanceEvidence::create([
                'ticket_id' => $ticket->id,
                'stage' => $data['stage'],
                'image_path' => $path,
                'description' => $data['description'],
                'uploaded_by' => $user->id,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 9. Selesai
        |--------------------------------------------------------------------------
        */

        $this->command->newLine();

        $this->command->info(
            '=============================================='
        );

        $this->command->info(
            'DEMO MAINTENANCE BERHASIL DIBUAT'
        );

        $this->command->info(
            '=============================================='
        );

        $this->command->info(
            'Device        : ' . $device->name
        );

        $this->command->info(
            'Asset Number  : ' . $device->asset_number
        );

        $this->command->info(
            'Ticket ID     : ' . $ticket->id
        );

        $this->command->info(
            'Ticket Number : ' . $ticket->ticket_number
        );

        $this->command->info(
            'Evidence      : 3 foto'
        );

        $this->command->info(
            'Stage         : Before, Process, After'
        );

        $this->command->info(
            'Status        : In Progress'
        );

        $this->command->info(
            '=============================================='
        );

        $this->command->newLine();
    }
}