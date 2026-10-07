<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentDowntime;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $equipments = Equipment::orderBy('name')->get();

        return view('downtime.create', compact('equipments'));
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

        $downtime = EquipmentDowntime::create([
            'equipment_id' => $validated['equipment_id'],
            'started_at' => $validated['started_at'],
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'ONGOING',
            'created_by' => Auth::id(),
        ]);

        $equipment = Equipment::find($validated['equipment_id']);

        NotificationService::roles(
            ['SUPERVISOR', 'MANAGER', 'ADMIN'],
            'Equipment downtime',
            ($equipment->name ?? 'Equipment') . ' mengalami downtime: ' . $validated['reason'],
            'warning',
            route('downtime.show', $downtime)
        );

        return redirect()
            ->route('downtime.index')
            ->with(
                'success',
                'Downtime equipment berhasil dibuat. Ajukan Maintenance Request agar perbaikan dapat dijadwalkan.'
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
            'maintenanceRequest.workOrder',
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
     * Menyelesaikan downtime secara manual.
     * (Downtime yang terhubung ke Maintenance Request juga selesai
     * otomatis ketika Work Order-nya COMPLETED.)
     */
    public function complete(EquipmentDowntime $downtime)
    {
        $done = DB::transaction(function () use ($downtime) {
            $fresh = EquipmentDowntime::lockForUpdate()->findOrFail($downtime->id);

            if ($fresh->status === 'COMPLETED') {
                return false;
            }

            $fresh->forceFill([
                'ended_at' => now(),
                'status'   => 'COMPLETED',
            ])->saveQuietly();

            activity('downtime')
                ->performedOn($fresh)
                ->causedBy(Auth::user())
                ->event('complete')
                ->log("Downtime #{$fresh->id} diselesaikan");

            return true;
        });

        if (! $done) {
            return back()->with('error', 'Downtime ini sudah selesai.');
        }

        return back()->with('success', 'Downtime berhasil diselesaikan.');
    }

    /**
     * Menghapus data downtime.
     */
    public function destroy(EquipmentDowntime $downtime)
    {
        if ($downtime->maintenance_request_id) {
            return back()->with(
                'error',
                'Downtime terhubung ke Maintenance Request dan tidak dapat dihapus.'
            );
        }

        $downtime->delete();

        return redirect()
            ->route('downtime.index')
            ->with(
                'success',
                'Downtime equipment berhasil dihapus.'
            );
    }
}