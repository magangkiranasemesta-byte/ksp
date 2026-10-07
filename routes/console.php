<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduler
|--------------------------------------------------------------------------
|
| Pengingat Preventive Maintenance (due soon / overdue) dikirim sekali
| per status per jadwal. Pastikan scheduler berjalan:
|
|   Produksi : * * * * * php /path/to/artisan schedule:run
|   Lokal    : php artisan schedule:work
|
*/

Schedule::command('maintenance:notify-preventive')
    ->dailyAt('07:00')
    ->withoutOverlapping();
