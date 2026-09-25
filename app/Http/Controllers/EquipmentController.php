<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
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
            'downtimes',
            'maintenanceRequests',
        ]);

        return view('equipment.show', compact('equipment'));
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
        if ($equipment->maintenanceRequests()->exists()) {
            return back()->with(
                'error',
                'Equipment tidak dapat dihapus karena memiliki riwayat maintenance.'
            );
        }

        $equipment->delete();

        return back()->with(
            'success',
            'Equipment berhasil dihapus.'
        );
    }
}