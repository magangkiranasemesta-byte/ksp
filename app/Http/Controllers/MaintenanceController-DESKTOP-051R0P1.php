<?php

namespace App\Http\Controllers;

use App\Exports\MaintenanceHistoryExport;
use App\Models\MaintenanceRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class MaintenanceController extends Controller
{
    /**
     * Batas jumlah baris per permintaan ekspor.
     */
    public const MAX_EXPORT_ROWS = 500;

    /**
     * Pilihan jumlah data per halaman (default pertama).
     */
    public const PER_PAGE_OPTIONS = [10, 25, 50];

    /**
     * =========================================================================
     * HISTORY
     * =========================================================================
     */

    public function history(Request $request)
    {
        // ?search[]=x dst. (array) dibuang agar tidak merusak view/pagination.
        $this->dropArrayInput($request, ['start_date', 'end_date', 'status', 'search', 'per_page']);

        // Nilai per_page dibatasi pada pilihan yang diizinkan (selain itu: default).
        $perPageOptions = self::PER_PAGE_OPTIONS;
        $perPage = (int) $request->input('per_page');
        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = $perPageOptions[0];
        }

        $history = MaintenanceRequest::with('equipment', 'engineer')
            ->forHistory()
            ->filterHistory(
                $this->stringInput($request, 'start_date'),
                $this->stringInput($request, 'end_date'),
                $this->stringInput($request, 'status'),
                $this->stringInput($request, 'search')
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('history.index', compact('history', 'perPage', 'perPageOptions'));
    }

    /**
     * =========================================================================
     * EXPORT PDF (hanya data terpilih)
     * =========================================================================
     */

    public function exportPdf(Request $request)
    {
        $result = $this->resolveSelectedExport($request);

        if ($result instanceof \Illuminate\Http\RedirectResponse) {
            return $result;
        }

        ['filters' => $filters, 'rows' => $history] = $result;

        try {
            $search        = $filters['search'];
            $startDate     = $filters['start_date'];
            $endDate       = $filters['end_date'];
            $status        = $filters['status'] ?? 'ALL';
            $selectedCount = $history->count();

            $pdf = Pdf::loadView(
                'reports.maintenance-history',
                compact('history', 'startDate', 'endDate', 'status', 'search', 'selectedCount')
            );

            // Landscape A4
            $pdf->setPaper('a4', 'landscape');

            return $pdf->download(
                'maintenance-history-selected-' . now()->format('Y-m-d-His') . '.pdf'
            );
        } catch (Throwable $e) {
            Log::error('Export PDF maintenance history gagal', [
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);

            return $this->backToHistory($request, 'Gagal membuat file PDF. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * =========================================================================
     * EXPORT EXCEL (hanya data terpilih)
     * =========================================================================
     */

    public function exportExcel(Request $request)
    {
        $result = $this->resolveSelectedExport($request);

        if ($result instanceof \Illuminate\Http\RedirectResponse) {
            return $result;
        }

        ['filters' => $filters, 'ids' => $ids] = $result;

        try {
            return Excel::download(
                new MaintenanceHistoryExport(
                    $filters['start_date'],
                    $filters['end_date'],
                    $filters['status'],
                    $ids,
                    $filters['search']
                ),
                'maintenance-history-selected-' . now()->format('Y-m-d-His') . '.xlsx'
            );
        } catch (Throwable $e) {
            Log::error('Export Excel maintenance history gagal', [
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);

            return $this->backToHistory($request, 'Gagal membuat file Excel. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * =========================================================================
     * HELPER
     * =========================================================================
     */

    /**
     * Validasi permintaan ekspor lalu ambil data terpilih.
     *
     * Semua pemeriksaan dilakukan di server: checkbox/JavaScript hanya
     * kemudahan UI, bukan pengaman.
     *
     * @return array{filters: array, ids: array<int,int>, rows: \Illuminate\Support\Collection}
     *         |\Illuminate\Http\RedirectResponse
     */
    private function resolveSelectedExport(Request $request)
    {
        // Status dinormalkan ke huruf besar (sama seperti filter di halaman list).
        if (is_string($request->input('status'))) {
            $request->merge(['status' => strtoupper($request->input('status'))]);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'selected_ids'   => ['required', 'array', 'min:1', 'max:' . self::MAX_EXPORT_ROWS],
                'selected_ids.*' => ['required', 'integer', 'min:1'],
                'start_date'     => ['nullable', 'date'],
                'end_date'       => ['nullable', 'date', 'after_or_equal:start_date'],
                'search'         => ['nullable', 'string', 'max:100'],
                'status'         => ['nullable', Rule::in(array_merge(['ALL'], MaintenanceRequest::HISTORY_STATUSES))],
            ],
            [
                'selected_ids.required'  => 'Pilih minimal satu data riwayat sebelum melakukan ekspor.',
                'selected_ids.array'     => 'Format data yang dipilih tidak valid.',
                'selected_ids.min'       => 'Pilih minimal satu data riwayat sebelum melakukan ekspor.',
                'selected_ids.max'       => 'Maksimal ' . self::MAX_EXPORT_ROWS . ' data dapat diekspor dalam satu kali permintaan.',
                'selected_ids.*.integer' => 'Terdapat ID data yang tidak valid.',
                'selected_ids.*.min'     => 'Terdapat ID data yang tidak valid.',
                'selected_ids.*.required' => 'Terdapat ID data yang tidak valid.',
                'start_date.date'        => 'Format tanggal mulai tidak valid.',
                'end_date.date'          => 'Format tanggal akhir tidak valid.',
                'end_date.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.',
                'search.string'          => 'Kata kunci pencarian tidak valid.',
                'search.max'             => 'Kata kunci pencarian maksimal 100 karakter.',
                'status.in'              => 'Status filter tidak valid.',
            ]
        );

        if ($validator->fails()) {
            return $this->backToHistory($request, $validator->errors()->first());
        }

        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date'   => $request->input('end_date'),
            'status'     => $request->input('status'),
            'search'     => $request->input('search'),
        ];

        // ID duplikat dibuang diam-diam (aman, tidak perlu menolak permintaan).
        $requestedIds = array_values(array_unique(array_map('intval', $request->input('selected_ids'))));

        // Query yang sama dengan halaman list (forHistory + filter), dibatasi ID terpilih.
        // ID yang tidak ada / di luar filter / di luar status history otomatis tidak ikut.
        $rows = MaintenanceRequest::with('equipment', 'engineer')
            ->forHistory()
            ->filterHistory($filters['start_date'], $filters['end_date'], $filters['status'], $filters['search'])
            ->whereIn('id', $requestedIds)
            ->latest()
            ->orderByDesc('id')
            ->get();

        if ($rows->isEmpty()) {
            return $this->backToHistory(
                $request,
                'Data yang dipilih tidak ditemukan atau tidak sesuai dengan filter yang aktif. Silakan muat ulang halaman dan pilih kembali.'
            );
        }

        return [
            'filters' => $filters,
            'ids'     => $rows->pluck('id')->all(),
            'rows'    => $rows,
        ];
    }

    /**
     * Kembali ke halaman history dengan filter yang sama + pesan error.
     */
    private function backToHistory(Request $request, string $message)
    {
        return redirect()
            ->route('history.index', array_filter(
                $request->only(['start_date', 'end_date', 'status', 'search', 'per_page']),
                fn ($v) => is_string($v) && $v !== ''
            ))
            ->with('error', $message);
    }

    /**
     * Ambil input sebagai string saja (array/objek dari query string diabaikan).
     */
    private function stringInput(Request $request, string $key): ?string
    {
        $value = $request->input($key);

        return is_string($value) ? $value : null;
    }

    /**
     * Hapus parameter yang dikirim sebagai array dari query string / body.
     */
    private function dropArrayInput(Request $request, array $keys): void
    {
        foreach ($keys as $key) {
            if (is_array($request->input($key))) {
                $request->query->remove($key);
                $request->request->remove($key);
            }
        }
    }
}
