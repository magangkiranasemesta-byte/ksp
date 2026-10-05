<?php

namespace App\Http\Controllers;

use App\Models\PreventiveMaintenance;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PreventiveMaintenanceController extends Controller
{
    /**
     * Menampilkan daftar Preventive Maintenance.
     */
    public function index()
    {
        $preventives = PreventiveMaintenance::with([
                'equipment',
                'technician'
            ])
            ->orderBy('next_maintenance_date', 'asc')
            ->paginate(10);

        $equipments = Equipment::orderBy('name')->get();

        // Jika sementara semua user boleh dipilih sebagai teknisi.
        // Nanti bisa dibatasi berdasarkan role jika diperlukan.
        $engineers = User::orderBy('username')->get();

        return view(
            'maintenance.preventive',
            compact(
                'preventives',
                'equipments',
                'engineers'
            )
        );
    }


    /**
     * Menyimpan jadwal Preventive Maintenance baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id'          => 'required|exists:equipment,id',
            'title'                 => 'required|string|max:255',
            'frequency'             => 'required|in:daily,weekly,monthly,yearly',
            'next_maintenance_date' => 'required|date',
            'assigned_to'           => 'nullable|exists:users,id',
            'notes'                 => 'nullable|string',
        ]);

        PreventiveMaintenance::create([
            'equipment_id'          => $validated['equipment_id'],
            'title'                 => $validated['title'],
            'frequency'             => $validated['frequency'],
            'next_maintenance_date' => $validated['next_maintenance_date'],
            'assigned_to'           => $validated['assigned_to'] ?? null,
            'notes'                 => $validated['notes'] ?? null,
            'status'                => 'scheduled',
        ]);

        return redirect()
            ->route('maintenance.preventive.index')
            ->with(
                'success',
                'Jadwal Preventive Maintenance berhasil ditambahkan.'
            );
    }


    /**
     * Menandai Preventive Maintenance selesai
     * dan menghitung jadwal berikutnya.
     */
    public function complete($id)
    {
        $preventive = PreventiveMaintenance::findOrFail($id);

        $currentDate = $preventive->next_maintenance_date
            ? Carbon::parse($preventive->next_maintenance_date)
            : now();

        $nextDate = match ($preventive->frequency) {

            'daily' => $currentDate->copy()->addDay(),

            'weekly' => $currentDate->copy()->addWeek(),

            'monthly' => $currentDate->copy()->addMonth(),

            'yearly' => $currentDate->copy()->addYear(),

            default => $currentDate->copy()->addMonth(),
        };

        $preventive->update([
            'next_maintenance_date' => $nextDate->format('Y-m-d'),
        ]);

        return redirect()
            ->route('maintenance.preventive.index')
            ->with(
                'success',
                'Perawatan rutin telah diselesaikan, jadwal berikutnya diperbarui.'
            );
    }
}