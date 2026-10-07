<?php

namespace App\Console\Commands;

use App\Models\PreventiveMaintenance;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Mengirim notifikasi Preventive Maintenance yang due soon / overdue.
 *
 * Setiap kombinasi (jadwal, tanggal jatuh tempo, kondisi) hanya dikirim sekali,
 * sehingga perintah ini aman dijalankan setiap hari.
 */
class NotifyPreventiveMaintenance extends Command
{
    protected $signature = 'maintenance:notify-preventive';

    protected $description = 'Kirim notifikasi Preventive Maintenance yang akan jatuh tempo atau overdue';

    public function handle(): int
    {
        $sent = 0;

        PreventiveMaintenance::with(['equipment', 'technician'])
            ->where('status', '<>', 'in_progress')
            ->where(function ($q) {
                $q->overdue()->orWhere(fn ($q2) => $q2->dueSoon());
            })
            ->orderBy('next_maintenance_date')
            ->each(function (PreventiveMaintenance $pm) use (&$sent) {
                $state = $pm->due_state; // overdue | due_soon

                $key = sprintf('pm-notified:%d:%s:%s', $pm->id, $pm->next_maintenance_date->toDateString(), $state);

                // Cache::add hanya berhasil bila kunci belum ada -> kirim satu kali.
                if (! Cache::add($key, true, now()->addDays(60))) {
                    return;
                }

                $equipment = $pm->equipment->name ?? 'Equipment';
                $date      = $pm->next_maintenance_date->format('d/m/Y');
                $url       = route('maintenance.preventive.show', $pm);

                if ($state === 'overdue') {
                    $title   = 'Preventive Maintenance overdue';
                    $message = "\"{$pm->title}\" ({$equipment}) terlambat sejak {$date}.";
                    $type    = 'warning';
                } else {
                    $title   = 'Preventive Maintenance segera jatuh tempo';
                    $message = "\"{$pm->title}\" ({$equipment}) jatuh tempo {$date}.";
                    $type    = 'maintenance';
                }

                if ($pm->technician) {
                    NotificationService::user($pm->technician, $title, $message, $type, $url);
                }

                NotificationService::roles(['SUPERVISOR', 'ADMIN'], $title, $message, $type, $url);

                $sent++;
            });

        $this->info("Notifikasi Preventive Maintenance terkirim: {$sent}");

        return self::SUCCESS;
    }
}
