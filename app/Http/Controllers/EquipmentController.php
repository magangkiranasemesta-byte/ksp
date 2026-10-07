<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\SparepartUsage;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EquipmentController extends Controller
{
    /**
     * Menampilkan daftar equipment.
     */
    public function index()
    {
        $equipment = Equipment::latest()->paginate(10);

        return view('equipment.index', compact('equipment'));
    }

    /**
     * Menampilkan detail equipment.
     *
     * Halaman ini menjadi tujuan QR Code.
     */
    public function show(Equipment $equipment)
    {
        $equipment->load([
            'currentWorkOrder.technician',
            'ongoingDowntime',
            'preventiveMaintenances.technician',
        ]);

        $workOrders = WorkOrder::with('technician')
            ->where('equipment_id', $equipment->id)
            ->latest()
            ->limit(10)
            ->get();

        $downtimes = $equipment->downtimes()->latest('started_at')->limit(10)->get();

        $maintenanceRequests = $equipment->maintenanceRequests()
            ->with('workOrder')
            ->latest()
            ->limit(5)
            ->get();

        $sparepartUsages = SparepartUsage::with(['sparepart', 'workOrder'])
            ->whereHas('workOrder', fn ($q) => $q->where('equipment_id', $equipment->id))
            ->latest('used_at')
            ->limit(8)
            ->get();

        $totalDowntimeMinutes = (int) $equipment->downtimes()
            ->get()
            ->sum(fn ($d) => $d->started_at->diffInMinutes($d->ended_at ?? now()));

        return view('equipment.show', compact(
            'equipment',
            'workOrders',
            'downtimes',
            'maintenanceRequests',
            'sparepartUsages',
            'totalDowntimeMinutes'
        ));
    }

    /**
     * Form edit equipment.
     */
    public function edit(Equipment $equipment)
    {
        return view('equipment.edit', [
            'equipment'     => $equipment,
            'hasActiveWork' => $this->hasActiveWork($equipment),
        ]);
    }

    /**
     * Memperbarui equipment.
     *
     * Status tidak boleh bertentangan dengan Work Order yang sedang berjalan:
     * selama ada WO IN_PROGRESS/ON_HOLD, status harus MAINTENANCE.
     */
    public function update(Request $request, Equipment $equipment)
    {
        $data = $request->validate([
            'equipment_code' => ['required', 'string', 'max:100', Rule::unique('equipment', 'equipment_code')->ignore($equipment->id)],
            'name'           => ['required', 'string', 'max:255'],
            'location'       => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'status'         => ['required', Rule::in(['ACTIVE', 'MAINTENANCE', 'INACTIVE'])],
        ]);

        if ($data['status'] !== 'MAINTENANCE' && $this->hasActiveWork($equipment)) {
            return back()
                ->withInput()
                ->withErrors(['status' => 'Equipment sedang dikerjakan melalui Work Order. Status akan kembali otomatis setelah Work Order selesai atau dibatalkan.']);
        }

        $equipment->update($data);

        return redirect()
            ->route('equipment.show', $equipment)
            ->with('success', 'Equipment berhasil diperbarui.');
    }

    private function hasActiveWork(Equipment $equipment): bool
    {
        return WorkOrder::where('equipment_id', $equipment->id)
            ->whereIn('status', ['IN_PROGRESS', 'ON_HOLD'])
            ->exists();
    }

    /**
     * Generate QR Code equipment.
     *
     * QR Code berisi URL menuju halaman
     * detail equipment.
     */
    public function qr(Equipment $equipment)
    {
        $url = route('equipment.show', $equipment);

        return QrCode::format('svg')
            ->size(400)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($url);
    }

    /**
     * Menyimpan equipment baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'equipment_code' => [
                'required',
                'string',
                'max:100',
                'unique:equipment,equipment_code',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'location' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                Rule::in([
                    'ACTIVE',
                    'MAINTENANCE',
                    'INACTIVE',
                ]),
            ],
        ]);

        Equipment::create($data);

        return back()->with(
            'success',
            'Equipment berhasil ditambahkan.'
        );
    }

    /**
     * Menghapus equipment.
     */
    public function destroy(Equipment $equipment)
    {
        if (
            $equipment->maintenanceRequests()->exists()
            || WorkOrder::where('equipment_id', $equipment->id)->exists()
            || $equipment->downtimes()->exists()
            || $equipment->preventiveMaintenances()->exists()
        ) {
            return back()->with(
                'error',
                'Equipment tidak dapat dihapus karena memiliki riwayat maintenance, Work Order, downtime, atau jadwal preventive. Ubah status menjadi INACTIVE.'
            );
        }

        $equipment->delete();

        return back()->with(
            'success',
            'Equipment berhasil dihapus.'
        );
    }
}