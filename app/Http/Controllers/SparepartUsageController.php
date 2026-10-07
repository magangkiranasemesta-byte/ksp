<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\SparepartUsage;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Gate;
use App\Models\MaintenanceTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SparepartUsageController extends Controller
{
    /**
     * Menampilkan riwayat pemakaian sparepart.
     */
    public function index(Request $request)
    {
        $query = SparepartUsage::with([
            'sparepart',
            'user',
            'workOrder',
        ])->latest('used_at');

        /*
        |--------------------------------------------------------------------------
        | Filter Sparepart
        |--------------------------------------------------------------------------
        */

        if ($request->filled('sparepart_id')) {
            $query->where('sparepart_id', $request->sparepart_id);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Tanggal
        |--------------------------------------------------------------------------
        */

        if ($request->filled('start_date')) {
            $query->whereDate(
                'used_at',
                '>=',
                $request->start_date
            );
        }

        if ($request->filled('end_date')) {
            $query->whereDate(
                'used_at',
                '<=',
                $request->end_date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('notes', 'like', "%{$search}%")

                    ->orWhereHas('sparepart', function ($sparepartQuery) use ($search) {
                        $sparepartQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })

                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('username', 'like', "%{$search}%");
                    })

                    ->orWhereHas('workOrder', function ($woQuery) use ($search) {
                        $woQuery->where('wo_number', 'like', "%{$search}%");
                    });
            });
        }

        $usages = $query
            ->paginate(15)
            ->withQueryString();

        $spareparts = Sparepart::orderBy('name')->get();

        return view(
            'spareparts.usages.index',
            compact(
                'usages',
                'spareparts'
            )
        );
    }

    /**
     * Form pencatatan pemakaian sparepart.
     */
    public function create()
    {
        $spareparts = Sparepart::orderBy('name')->get();

        /*
        |--------------------------------------------------------------------------
        | Ambil ticket yang tersedia
        |--------------------------------------------------------------------------
        |
        | Kita ambil model MaintenanceTicket sesuai relasi Sparepart
        | yang sudah digunakan di project.
        |
        */

        $tickets = MaintenanceTicket::latest()->get();

        // Work Order yang sedang dikerjakan dan boleh dicatat pemakaian sparepart-nya oleh user ini.
        $workOrders = WorkOrder::with('equipment')
            ->whereIn('status', ['IN_PROGRESS', 'ON_HOLD'])
            ->latest()
            ->get()
            ->filter(fn ($wo) => Gate::allows('recordSparepart', $wo))
            ->values();

        $selectedWorkOrderId = request()->integer('work_order_id') ?: null;

        return view(
            'spareparts.usages.create',
            compact(
                'spareparts',
                'tickets',
                'workOrders',
                'selectedWorkOrderId'
            )
        );
    }

    /**
     * Menyimpan pemakaian sparepart.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'sparepart_id' => [
                'required',
                'exists:spareparts,id',
            ],

            'maintenance_ticket_id' => [
                'nullable',
                'integer',
                'prohibits:work_order_id',
            ],

            'work_order_id' => [
                'nullable',
                'integer',
                'exists:work_orders,id',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'used_at' => [
                'nullable',
                'date',
            ],
        ], [
            'sparepart_id.required' =>
                'Sparepart wajib dipilih.',

            'sparepart_id.exists' =>
                'Sparepart yang dipilih tidak ditemukan.',

            'maintenance_ticket_id.prohibits' =>
                'Pilih salah satu: Ticket atau Work Order.',

            'quantity.required' =>
                'Jumlah pemakaian wajib diisi.',

            'quantity.integer' =>
                'Jumlah pemakaian harus berupa angka.',

            'quantity.min' =>
                'Jumlah pemakaian minimal 1.',

            'notes.max' =>
                'Catatan terlalu panjang.',

            'used_at.date' =>
                'Tanggal pemakaian tidak valid.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Lock Sparepart
            |--------------------------------------------------------------------------
            |
            | lockForUpdate() mencegah dua transaksi memakai stok
            | yang sama secara bersamaan.
            |
            */

            $sparepart = Sparepart::where(
                'id',
                $validated['sparepart_id']
            )
                ->lockForUpdate()
                ->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | Cek Stok
            |--------------------------------------------------------------------------
            */

            $quantity = (int) $validated['quantity'];

            if ($quantity > $sparepart->stock) {

                DB::rollBack();

                return back()
                    ->withInput()
                    ->withErrors([
                        'quantity' =>
                            "Stok {$sparepart->name} tidak mencukupi. " .
                            "Stok tersedia: {$sparepart->stock} {$sparepart->unit}.",
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi Ticket
            |--------------------------------------------------------------------------
            |
            | Ticket bersifat optional.
            |
            */

            $ticketId = $validated['maintenance_ticket_id'] ?? null;

            if ($ticketId !== null) {

                $ticketExists = MaintenanceTicket::where(
                    'id',
                    $ticketId
                )->exists();

                if (!$ticketExists) {

                    DB::rollBack();

                    return back()
                        ->withInput()
                        ->withErrors([
                            'maintenance_ticket_id' =>
                                'Maintenance ticket tidak ditemukan.',
                        ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi Work Order
            |--------------------------------------------------------------------------
            |
            | Sparepart hanya boleh dipakai pada WO yang sedang dikerjakan
            | (IN_PROGRESS / ON_HOLD) dan oleh pihak yang berwenang.
            |
            */

            $workOrderId = $validated['work_order_id'] ?? null;

            if ($workOrderId !== null) {

                $workOrder = WorkOrder::lockForUpdate()->find($workOrderId);

                if (
                    ! $workOrder
                    || ! in_array($workOrder->status, ['IN_PROGRESS', 'ON_HOLD'], true)
                ) {
                    DB::rollBack();

                    return back()
                        ->withInput()
                        ->withErrors([
                            'work_order_id' =>
                                'Sparepart hanya dapat dicatat pada Work Order yang sedang dikerjakan.',
                        ]);
                }

                if (! Gate::allows('recordSparepart', $workOrder)) {
                    DB::rollBack();

                    abort(403);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Kurangi Stok
            |--------------------------------------------------------------------------
            */

            $sparepart->stock = $sparepart->stock - $quantity;

            $sparepart->save();

            /*
            |--------------------------------------------------------------------------
            | Simpan Riwayat Pemakaian
            |--------------------------------------------------------------------------
            */

            $usage = SparepartUsage::create([
                'work_order_id' =>
                    $workOrderId,

                'sparepart_id' =>
                    $sparepart->id,

                'user_id' =>
                    auth()->id(),

                'maintenance_ticket_id' =>
                    $ticketId,

                'quantity' =>
                    $quantity,

                'notes' =>
                    $validated['notes'] ?? null,

                'used_at' =>
                    $validated['used_at'] ?? now(),
            ]);

            activity('sparepart')
                ->performedOn($sparepart)
                ->causedBy(auth()->user())
                ->event('use')
                ->withProperties([
                    'quantity'      => $quantity,
                    'stock_after'   => $sparepart->stock,
                    'work_order_id' => $workOrderId,
                    'ticket_id'     => $ticketId,
                ])
                ->log("Pemakaian {$quantity} {$sparepart->unit} {$sparepart->name}");

            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
            */

            DB::commit();

            if ($workOrderId !== null) {
                return redirect()
                    ->route('work-orders.show', $workOrderId)
                    ->with(
                        'success',
                        "Pemakaian {$quantity} {$sparepart->unit} {$sparepart->name} dicatat. Stok tersisa: {$sparepart->stock} {$sparepart->unit}."
                    );
            }

            return redirect()
                ->route('sparepart-usages.index')
                ->with(
                    'success',
                    "Pemakaian {$quantity} {$sparepart->unit} {$sparepart->name} berhasil dicatat. Stok tersisa: {$sparepart->stock} {$sparepart->unit}."
                );

        } catch (\Illuminate\Auth\Access\AuthorizationException | \Symfony\Component\HttpKernel\Exception\HttpException $e) {

            DB::rollBack();

            throw $e;

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors([
                    'error' =>
                        'Gagal mencatat pemakaian sparepart: ' .
                        $e->getMessage(),
                ]);
        }
    }
}