<?php

namespace App\Exports;

use App\Models\MaintenanceRequest;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaintenanceHistoryExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct(
        $startDate = null,
        $endDate = null,
        $status = null
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    /**
     * Ambil data history
     */
    public function collection(): Enumerable
    {
        $query = MaintenanceRequest::with([
            'equipment',
            'engineer'
        ])
        ->whereIn('status', [
            'REJECTED',
            'APPROVED',
            'IN_PROGRESS',
            'COMPLETED'
        ]);

        // Filter tanggal mulai
        if ($this->startDate) {
            $query->whereDate(
                'created_at',
                '>=',
                $this->startDate
            );
        }

        // Filter tanggal akhir
        if ($this->endDate) {
            $query->whereDate(
                'created_at',
                '<=',
                $this->endDate
            );
        }

        // Filter status
        if (
            $this->status &&
            strtoupper($this->status) !== 'ALL'
        ) {
            $query->where(
                'status',
                strtoupper($this->status)
            );
        }

        return $query
            ->latest()
            ->get();
    }

    /**
     * Header Excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Equipment',
            'Engineer',
            'Priority',
            'Status',
            'Description',
            'Created At',
        ];
    }

    /**
     * Mapping data
     */
    public function map($maintenance): array
    {
        return [
            $maintenance->id,

            $maintenance->equipment
                ? $maintenance->equipment->name
                : '-',

            $maintenance->engineer
                ? $maintenance->engineer->username
                : '-',

            $maintenance->priority ?? '-',

            $maintenance->status ?? '-',

            $maintenance->description ?? '-',

            $maintenance->created_at
                ? $maintenance->created_at->format('Y-m-d H:i:s')
                : '-',
        ];
    }

    /**
     * Styling Excel
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }
}