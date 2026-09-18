<?php

namespace Database\Seeders;

use App\Models\MaintenanceEvidence;
use App\Models\MaintenanceTicket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MaintenanceEvidenceSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ticket pertama yang tersedia
        $ticket = MaintenanceTicket::first();

        if (!$ticket) {
            $this->command->error('Belum ada Maintenance Ticket.');
            return;
        }

        // Ambil user pertama sebagai uploader
        $user = \App\Models\User::first();

        if (!$user) {
            $this->command->error('Belum ada user.');
            return;
        }

        $disk = Storage::disk('public');

        $folder = 'maintenance-evidence/' . $ticket->id;

        // Pastikan folder tersedia
        $disk->makeDirectory($folder);

        $dummyData = [
            [
                'stage' => 'before',
                'filename' => 'dummy-before.jpg',
                'description' => 'Kondisi mesin sebelum dilakukan maintenance. Terlihat bagian mesin mengalami kerusakan.',
            ],
            [
                'stage' => 'process',
                'filename' => 'dummy-process.jpg',
                'description' => 'Proses pembongkaran dan pemeriksaan komponen mesin oleh engineer.',
            ],
            [
                'stage' => 'after',
                'filename' => 'dummy-after.jpg',
                'description' => 'Kondisi mesin setelah dilakukan perbaikan dan pemeriksaan akhir.',
            ],
        ];

        foreach ($dummyData as $data) {

            $path = $folder . '/' . $data['filename'];

            // Jangan buat data duplikat jika seeder dijalankan lagi
            if ($disk->exists($path)) {
                continue;
            }

            /*
             * Membuat gambar dummy JPEG sederhana.
             * Tidak membutuhkan package tambahan.
             */
            $image = imagecreatetruecolor(1200, 700);

            $background = imagecolorallocate(
                $image,
                235,
                241,
                247
            );

            $textColor = imagecolorallocate(
                $image,
                30,
                41,
                59
            );

            imagefill(
                $image,
                0,
                0,
                $background
            );

            // Border
            $borderColor = imagecolorallocate(
                $image,
                148,
                163,
                184
            );

            imagerectangle(
                $image,
                20,
                20,
                1180,
                680,
                $borderColor
            );

            $title = strtoupper($data['stage']);

            imagestring(
                $image,
                5,
                500,
                280,
                'MAINTENANCE',
                $textColor
            );

            imagestring(
                $image,
                5,
                545,
                330,
                $title,
                $textColor
            );

            imagestring(
                $image,
                3,
                455,
                380,
                'Dummy Evidence Photo',
                $textColor
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

            MaintenanceEvidence::create([
                'ticket_id' => $ticket->id,
                'stage' => $data['stage'],
                'image_path' => $path,
                'description' => $data['description'],
                'uploaded_by' => $user->id,
            ]);
        }

        $this->command->info(
            "Dummy Evidence berhasil dibuat untuk Ticket #{$ticket->id}"
        );
    }
}