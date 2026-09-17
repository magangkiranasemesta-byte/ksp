<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = Equipment::latest()->paginate(10);
        return view('equipment.index', compact('equipment'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'equipment_code' => ['required','string','max:100','unique:equipment,equipment_code'],
            'name' => ['required','string','max:255'],
            'location' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'status' => ['required', Rule::in(['ACTIVE','MAINTENANCE','INACTIVE'])],
        ]);
        Equipment::create($data);
        return back()->with('success','Equipment berhasil ditambahkan.');
    }

    public function destroy(Equipment $equipment)
    {
        if ($equipment->maintenanceRequests()->exists()) return back()->with('error','Equipment tidak dapat dihapus karena memiliki riwayat maintenance.');
        $equipment->delete();
        return back()->with('success','Equipment berhasil dihapus.');
    }
}
