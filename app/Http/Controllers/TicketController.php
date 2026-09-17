<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\MaintenanceLog;
use App\Models\MaintenanceTicket;
use App\Models\TicketStatusHistory;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * Menampilkan daftar semua tiket maintenance.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Query Ticket
        |--------------------------------------------------------------------------
        */
        $query = MaintenanceTicket::with([
            'device',
            'reporter',
            'technician'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")

                    ->orWhereHas('device', function ($device) use ($search) {
                        $device->where('name', 'like', "%{$search}%")
                            ->orWhere(
                                'asset_number',
                                'like',
                                "%{$search}%"
                            );
                    })

                    ->orWhereHas('reporter', function ($reporter) use ($search) {
                        $reporter->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    })

                    ->orWhereHas('technician', function ($technician) use ($search) {
                        $technician->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */
        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Priority
        |--------------------------------------------------------------------------
        */
        if ($request->filled('priority')) {
            $query->where(
                'priority',
                $request->priority
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Statistik Ticket
        |--------------------------------------------------------------------------
        | Statistik menggunakan seluruh data database,
        | bukan hanya data hasil pagination.
        |--------------------------------------------------------------------------
        */

        $totalTickets = MaintenanceTicket::count();

        $openTickets = MaintenanceTicket::where(
            'status',
            'open'
        )->count();

        $inProgressTickets = MaintenanceTicket::whereIn(
            'status',
            [
                'in_progress',
                'pending_sparepart'
            ]
        )->count();

        $completedTickets = MaintenanceTicket::whereIn(
            'status',
            [
                'resolved',
                'closed'
            ]
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $tickets = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('tickets.index', compact(
            'tickets',
            'totalTickets',
            'openTickets',
            'inProgressTickets',
            'completedTickets'
        ));
    }

    /**
     * Menampilkan form untuk membuat tiket baru.
     */
    public function create()
    {
        $devices = Device::where(
            'status',
            '!=',
            'retired'
        )->get();

        return view(
            'tickets.create',
            compact('devices')
        );
    }

    /**
     * Menyimpan tiket baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'image_proof' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request) {

            /*
            |--------------------------------------------------------------------------
            | Upload Foto Bukti
            |--------------------------------------------------------------------------
            */
            $imagePath = null;

            if ($request->hasFile('image_proof')) {
                $imagePath = $request
                    ->file('image_proof')
                    ->store(
                        'tickets/proofs',
                        'public'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Generate Nomor Ticket
            |--------------------------------------------------------------------------
            */
            do {
                $ticketNumber =
                    'TKT-' .
                    date('Ymd') .
                    '-' .
                    strtoupper(Str::random(4));

            } while (
                MaintenanceTicket::where(
                    'ticket_number',
                    $ticketNumber
                )->exists()
            );

            /*
            |--------------------------------------------------------------------------
            | Buat Ticket
            |--------------------------------------------------------------------------
            */
            $ticket = MaintenanceTicket::create([
                'ticket_number' => $ticketNumber,
                'device_id' => $request->device_id,
                'reported_by' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'status' => 'open',
                'image_proof' => $imagePath,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Ubah Status Device
            |--------------------------------------------------------------------------
            */
            Device::where(
                'id',
                $request->device_id
            )->update([
                'status' => 'in_repair'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Simpan History Status
            |--------------------------------------------------------------------------
            */
            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'old_status' => null,
                'new_status' => 'open',
                'notes' => 'Tiket pelaporan kerusakan berhasil dibuat.',
            ]);
        });

        return redirect()
            ->route('tickets.index')
            ->with(
                'success',
                'Tiket kerusakan berhasil dibuat!'
            );
    }

    /**
     * Menampilkan detail tiket beserta riwayat perbaikan & status.
     */
    public function show($id)
    {
        $ticket = MaintenanceTicket::with([
            'device',
            'reporter',
            'technician',
            'logs.technician',
            'statusHistories.user'
        ])->findOrFail($id);

        return view(
            'tickets.show',
            compact('ticket')
        );
    }

    /**
     * Menugaskan teknisi ke tiket tertentu.
     */
    public function assignTechnician(
        Request $request,
        $id
    ) {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $ticket = MaintenanceTicket::findOrFail($id);

        $oldTechnician = $ticket->assigned_to;

        $ticket->update([
            'assigned_to' => $request->assigned_to
        ]);

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */
        if (function_exists('activity')) {
            activity('maintenance_ticket')
                ->performedOn($ticket)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old_assigned_to' => $oldTechnician,
                    'new_assigned_to' => $request->assigned_to,
                ])
                ->log('Teknisi ticket berhasil ditugaskan');
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Teknisi berhasil ditugaskan.'
            );
    }

    /**
     * Mengubah status tiket dan mencatat riwayat perubahan.
     */
    public function updateStatus(
        Request $request,
        $id
    ) {
        $request->validate([
            'status' => 'required|in:open,in_progress,pending_sparepart,resolved,closed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $ticket = MaintenanceTicket::findOrFail($id);

        if ($ticket->status !== $request->status) {

            DB::transaction(function () use (
                $ticket,
                $request
            ) {

                $oldStatus = $ticket->status;

                /*
                |--------------------------------------------------------------------------
                | Update Status Ticket
                |--------------------------------------------------------------------------
                */
                $ticket->status = $request->status;

                if (
                    $request->status === 'resolved'
                ) {
                    $ticket->resolved_at = now();
                }

                if (
                    $request->status === 'closed' &&
                    !$ticket->resolved_at
                ) {
                    $ticket->resolved_at = now();
                }

                $ticket->save();

                /*
                |--------------------------------------------------------------------------
                | Update Status Device
                |--------------------------------------------------------------------------
                */
                if (
                    in_array(
                        $request->status,
                        [
                            'resolved',
                            'closed'
                        ]
                    )
                ) {

                    $ticket
                        ->device()
                        ->update([
                            'status' => 'active'
                        ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Jika Ticket Kembali Aktif
                |--------------------------------------------------------------------------
                */
                elseif (
                    in_array(
                        $request->status,
                        [
                            'open',
                            'in_progress',
                            'pending_sparepart'
                        ]
                    )
                ) {

                    $ticket
                        ->device()
                        ->update([
                            'status' => 'in_repair'
                        ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Catat History
                |--------------------------------------------------------------------------
                */
                TicketStatusHistory::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => Auth::id(),
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'notes' => $request->notes,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Activity Log
                |--------------------------------------------------------------------------
                */
                if (function_exists('activity')) {

                    activity('maintenance_ticket')
                        ->performedOn($ticket)
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'old_status' => $oldStatus,
                            'new_status' => $request->status,
                            'notes' => $request->notes,
                        ])
                        ->log(
                            'Status ticket diubah dari ' .
                            $oldStatus .
                            ' menjadi ' .
                            $request->status
                        );
                }
            });
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Status tiket berhasil diperbarui.'
            );
    }

    /**
     * Menambahkan catatan tindakan/riwayat perbaikan oleh teknisi.
     */
    public function addLog(
        Request $request,
        $id
    ) {
        $request->validate([
            'action_taken' => 'required|string',
            'maintenance_type' => 'required|in:corrective,preventive',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $ticket = MaintenanceTicket::findOrFail($id);

        $log = MaintenanceLog::create([
            'ticket_id' => $ticket->id,
            'technician_id' => Auth::id(),
            'action_taken' => $request->action_taken,
            'maintenance_type' => $request->maintenance_type,
            'cost' => $request->cost ?? 0,
            'completed_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */
        if (function_exists('activity')) {

            activity('maintenance_ticket')
                ->performedOn($ticket)
                ->causedBy(Auth::user())
                ->withProperties([
                    'maintenance_log_id' => $log->id,
                    'maintenance_type' => $request->maintenance_type,
                    'cost' => $request->cost ?? 0,
                ])
                ->log(
                    'Log tindakan maintenance ditambahkan'
                );
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Log tindakan perbaikan berhasil ditambahkan.'
            );
    }

    /**
     * Menambahkan sparepart ke ticket.
     */
    public function addSparepart(
        Request $request,
        $ticketId
    ) {
        $request->validate([
            'sparepart_id' => 'required|exists:spareparts,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $ticket = MaintenanceTicket::findOrFail(
            $ticketId
        );

        $sparepart = Sparepart::findOrFail(
            $request->sparepart_id
        );

        if (
            $sparepart->stock <
            $request->quantity
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Stok tidak mencukupi!'
                );
        }

        DB::transaction(function () use (
            $ticket,
            $sparepart,
            $request
        ) {

            $qty = $request->quantity;

            $totalPrice =
                $sparepart->price * $qty;

            $ticket->spareparts()->attach(
                $sparepart->id,
                [
                    'quantity' => $qty,
                    'unit_price' => $sparepart->price,
                    'total_price' => $totalPrice,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Potong Stok Otomatis
            |--------------------------------------------------------------------------
            */
            $sparepart->decrement(
                'stock',
                $qty
            );

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */
            if (function_exists('activity')) {

                activity('maintenance_ticket')
                    ->performedOn($ticket)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'sparepart_id' => $sparepart->id,
                        'quantity' => $qty,
                        'unit_price' => $sparepart->price,
                        'total_price' => $totalPrice,
                    ])
                    ->log(
                        'Sparepart digunakan pada ticket'
                    );
            }
        });

        return redirect()
            ->back()
            ->with(
                'success',
                'Sparepart berhasil digunakan!'
            );
    }

    /**
     * Menghapus sparepart dari ticket
     * dan mengembalikan stok.
     */
    public function removeSparepart(
        $ticketId,
        $sparepartId
    ) {
        $ticket = MaintenanceTicket::findOrFail(
            $ticketId
        );

        $sparepart = Sparepart::findOrFail(
            $sparepartId
        );

        $pivotData = $ticket
            ->spareparts()
            ->where(
                'sparepart_id',
                $sparepartId
            )
            ->first();

        if ($pivotData) {

            DB::transaction(function () use (
                $ticket,
                $sparepart,
                $pivotData
            ) {

                $quantity =
                    $pivotData->pivot->quantity;

                /*
                |--------------------------------------------------------------------------
                | Kembalikan Stok
                |--------------------------------------------------------------------------
                */
                $sparepart->increment(
                    'stock',
                    $quantity
                );

                /*
                |--------------------------------------------------------------------------
                | Hapus Relasi
                |--------------------------------------------------------------------------
                */
                $ticket
                    ->spareparts()
                    ->detach(
                        $sparepart->id
                    );

                /*
                |--------------------------------------------------------------------------
                | Activity Log
                |--------------------------------------------------------------------------
                */
                if (function_exists('activity')) {

                    activity('maintenance_ticket')
                        ->performedOn($ticket)
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'sparepart_id' => $sparepart->id,
                            'quantity' => $quantity,
                        ])
                        ->log(
                            'Sparepart dihapus dari ticket dan stok dikembalikan'
                        );
                }
            });
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Sparepart dihapus dan stok dikembalikan!'
            );
    }
}