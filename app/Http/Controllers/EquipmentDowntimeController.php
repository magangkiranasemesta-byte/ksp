<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentDowntime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentDowntimeController extends Controller
{
    /**
     * Menampilkan daftar downtime equipment.
     */
    public function index()
    {
        $downtimes = EquipmentDowntime::with([
            'equipment',
            'creator',
        ])
            ->latest()
            ->paginate(10);

        $totalDowntime = EquipmentDowntime::count();

        $ongoingDowntime = EquipmentDowntime::where(
            'status',
            'ONGOING'
        )->count();

        $completedDowntime = EquipmentDowntime::where(
            'status',
            'COMPLETED'
        )->count();

        return view('downtime.index', [
            'downtimes' => $downtimes,
            'totalDowntime' => $totalDowntime,
            'ongoingDowntime' => $ongoingDowntime,
            'completedDowntime' => $completedDowntime,
        ]);
    }

    /**
     * Menampilkan form tambah downtime.
     */
    public function create()
    {
        $equipment = Equipment::orderBy('name')->get();

        return view('downtime.create', [
            'equipment' => $equipment,
        ]);
    }

    /**
     * Menyimpan downtime baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => [
                'required',
                'exists:equipment,id',
            ],

            'started_at' => [
                'required',
                'date',
            ],

            'reason' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        /*
         * Cek apakah equipment masih memiliki
         * downtime yang sedang berjalan.
         */
        $hasOngoingDowntime = EquipmentDowntime::where(
            'equipment_id',
            $validated['equipment_id']
        )
            ->where('status', 'ONGOING')
            ->exists();

        if ($hasOngoingDowntime) {
            return back()
                ->withInput()
                ->withErrors([
                    'equipment_id' =>
                        'Equipment tersebut masih memiliki downtime yang sedang berjalan.',
                ]);
        }

        EquipmentDowntime::create([
            'equipment_id' => $validated['equipment_id'],
            'started_at' => $validated['started_at'],
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'ONGOING',
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('downtime.index')
            ->with(
                'success',
                'Downtime equipment berhasil dibuat.'
            );
    }

    /**
     * Menampilkan detail downtime.
     */
    public function show(EquipmentDowntime $downtime)
    {
        $downtime->load([
            'equipment',
            'creator',
        ]);

        return view('downtime.show', [
            'downtime' => $downtime,
        ]);
    }

    /**
     * Menampilkan form edit downtime.
     */
    public function edit(EquipmentDowntime $downtime)
    {
        $equipment = Equipment::orderBy('name')->get();

        $downtime->load([
            'equipment',
            'creator',
        ]);

        return view('downtime.edit', [
            'downtime' => $downtime,
            'equipment' => $equipment,
        ]);
    }

    /**
     * Memperbarui data downtime.
     */
    public function update(
        Request $request,
        EquipmentDowntime $downtime
    ) {
        $validated = $request->validate([
            'equipment_id' => [
                'required',
                'exists:equipment,id',
            ],

            'started_at' => [
                'required',
                'date',
            ],

            'reason' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        /*
         * Jika equipment diganti, cek apakah
         * equipment baru sudah memiliki downtime aktif.
         */
        if (
            $validated['equipment_id']
            != $downtime->equipment_id
        ) {
            $hasOngoingDowntime = EquipmentDowntime::where(
                'equipment_id',
                $validated['equipment_id']
            )
                ->where('status', 'ONGOING')
                ->where('id', '!=', $downtime->id)
                ->exists();

            if ($hasOngoingDowntime) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'equipment_id' =>
                            'Equipment tersebut masih memiliki downtime yang sedang berjalan.',
                    ]);
            }
        }

        $downtime->update([
            'equipment_id' => $validated['equipment_id'],
            'started_at' => $validated['started_at'],
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('downtime.show', $downtime)
            ->with(
                'success',
                'Downtime equipment berhasil diperbarui.'
            );
    }

    /**
     * Menyelesaikan downtime.
     */
    public function complete(EquipmentDowntime $downtime)
    {
        if ($downtime->status === 'COMPLETED') {
            return back()->with(
                'error',
                'Downtime ini sudah selesai.'
            );
        }

        $downtime->update([
            'ended_at' => now(),
            'status' => 'COMPLETED',
        ]);

        return back()->with(
            'success',
            'Downtime berhasil diselesaikan.'
        );
    }

    /**
     * Menghapus data downtime.
     */
    public function destroy(EquipmentDowntime $downtime)
    {
        $downtime->delete();

        return redirect()
            ->route('downtime.index')
            ->with(
                'success',
                'Downtime equipment berhasil dihapus.'
            );
    }
}