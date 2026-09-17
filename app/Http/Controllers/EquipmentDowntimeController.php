<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentDowntime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentDowntimeController extends Controller
{
    public function index()
    {
        $downtimes = EquipmentDowntime::with([
            'equipment',
            'creator'
        ])
        ->latest()
        ->paginate(10);

        return view('downtime.index', compact('downtimes'));
    }

    public function create()
    {
        $equipment = Equipment::orderBy('name')->get();

        return view('downtime.create', compact('equipment'));
    }

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

        $existingDowntime = EquipmentDowntime::where(
            'equipment_id',
            $validated['equipment_id']
        )
        ->where('status', 'ONGOING')
        ->exists();

        if ($existingDowntime) {
            return back()
                ->withInput()
                ->withErrors([
                    'equipment_id' =>
                        'Equipment tersebut masih memiliki downtime yang sedang berjalan.'
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
            ->with('success', 'Downtime equipment berhasil dibuat.');
    }

    public function complete(EquipmentDowntime $downtime)
    {
        if ($downtime->status === 'COMPLETED') {
            return back()->with('error', 'Downtime ini sudah selesai.');
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
}