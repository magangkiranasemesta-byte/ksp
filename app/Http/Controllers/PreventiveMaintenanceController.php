<?php

namespace App\Http\Controllers;

use App\Models\PreventiveMaintenance;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PreventiveMaintenanceController extends Controller
{
    // Menampilkan halaman daftar preventive maintenance
    public function index()
    {
        $preventives = PreventiveMaintenance::with(['equipment', 'technician'])
            ->orderBy('next_maintenance_date', 'asc')
            ->get();
            
        $equipments = Equipment::all();
        
        // Ambil user yang bertindak sebagai teknisi/engineer (sesuaikan role/permission di aplikasi Anda)
        $engineers = User::all(); 

        return view('maintenance.preventive', compact('preventives', 'equipments', 'engineers'));
    }

    // Menyimpan jadwal rutin baru
    public function store(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'title' => 'required|string|max:255',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'next_maintenance_date' => 'required|date',
        ]);

        PreventiveMaintenance::create($request->all());

        return redirect()->route('maintenance.preventive.index')
            ->with('success', 'Jadwal Preventive Maintenance berhasil ditambahkan.');
    }

    // Menandai selesai dan otomatis menjadwalkan periode berikutnya
    public function complete($id)
    {
        $preventive = PreventiveMaintenance::findOrFail($id);

        // Hitung jadwal berikutnya berdasarkan frekuensi
        $currentDate = Carbon::parse($preventive->next_maintenance_date);
        $nextDate = match ($preventive->frequency) {
            'daily'   => $currentDate->addDay(),
            'weekly'  => $preventive->next_maintenance_date ? Carbon::parse($preventive->next_maintenance_date)->addWeek() : now()->addWeek(),
            'monthly' => $preventive->next_maintenance_date ? Carbon::parse($preventive->next_maintenance_date)->addMonth() : now()->addMonth(),
            'yearly'  => $preventive->next_maintenance_date ? Carbon::parse($preventive->next_maintenance_date)->addYear() : now()->addYear(),
            default   => now()->addMonth(),
        };

        // Update tanggal ke periode berikutnya agar siklus berjalan terus
        $preventive->update([
            'next_maintenance_date' => $nextDate->format('Y-m-d'),
        ]);

        return redirect()->route('maintenance.preventive.index')
            ->with('success', 'Perawatan rutin telah diselesaikan, jadwal berikutnya diperbarui.');
    }
}
