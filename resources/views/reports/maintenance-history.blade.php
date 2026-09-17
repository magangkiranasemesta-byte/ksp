<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Maintenance History Report</title>

    <style>

        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .company {
            font-size: 20px;
            font-weight: bold;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        .subtitle {
            font-size: 10px;
            color: #555;
            margin-top: 4px;
        }

        .information {
            width: 100%;
            margin-bottom: 15px;
        }

        .information td {
            padding: 3px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th {
            background: #222;
            color: white;
            padding: 7px;
            border: 1px solid #999;
            text-align: center;
        }

        table.data td {
            padding: 6px;
            border: 1px solid #999;
            vertical-align: top;
        }

        .center {
            text-align: center;
        }

        .summary {
            margin-top: 15px;
            width: 100%;
            border-collapse: collapse;
        }

        .summary td {
            border: 1px solid #aaa;
            padding: 7px;
        }

        .summary-title {
            font-weight: bold;
            background: #eee;
        }

        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #777;
            text-align: right;
        }

    </style>

</head>

<body>

    <!-- HEADER -->

    <div class="header">

        <div class="company">
            KIRANA SEMESTA PANGAN
        </div>

        <div class="title">
            MAINTENANCE HISTORY REPORT
        </div>

        <div class="subtitle">
            Equipment Maintenance Management System
        </div>

    </div>


    <!-- INFORMATION -->

    <table class="information">

        <tr>

            <td width="15%">
                <strong>Periode</strong>
            </td>

            <td>
                :

                @if($startDate || $endDate)

                    {{ $startDate ?: 'Semua' }}

                    s/d

                    {{ $endDate ?: 'Sekarang' }}

                @else

                    Semua Periode

                @endif

            </td>

        </tr>

        <tr>

            <td>
                <strong>Status</strong>
            </td>

            <td>
                :
                {{ $status && $status !== 'ALL'
                    ? $status
                    : 'Semua Status'
                }}
            </td>

        </tr>

        <tr>

            <td>
                <strong>Generated</strong>
            </td>

            <td>
                :
                {{ now()->format('d-m-Y H:i:s') }}
            </td>

        </tr>

    </table>


    <!-- DATA -->

    <table class="data">

        <thead>

            <tr>

                <th width="5%">
                    ID
                </th>

                <th width="15%">
                    Equipment
                </th>

                <th width="15%">
                    Engineer
                </th>

                <th width="10%">
                    Priority
                </th>

                <th width="12%">
                    Status
                </th>

                <th>
                    Description
                </th>

                <th width="15%">
                    Created
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($history as $item)

                <tr>

                    <td class="center">
                        {{ $item->id }}
                    </td>

                    <td>
                        {{ $item->equipment->name ?? '-' }}
                    </td>

                    <td>
                        {{ $item->engineer->username ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $item->priority ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $item->status ?? '-' }}
                    </td>

                    <td>
                        {{ $item->description ?? '-' }}
                    </td>

                    <td class="center">
                        {{ optional($item->created_at)->format('d-m-Y H:i') }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="7"
                        class="center"
                    >
                        Tidak ada data maintenance history.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <!-- SUMMARY -->

    <table class="summary">

        <tr>

            <td class="summary-title">
                Total Data
            </td>

            <td>
                {{ $history->count() }}
            </td>

        </tr>

        <tr>

            <td class="summary-title">
                Completed
            </td>

            <td>
                {{ $history->where('status', 'COMPLETED')->count() }}
            </td>

        </tr>

        <tr>

            <td class="summary-title">
                In Progress
            </td>

            <td>
                {{ $history->where('status', 'IN_PROGRESS')->count() }}
            </td>

        </tr>

        <tr>

            <td class="summary-title">
                Approved
            </td>

            <td>
                {{ $history->where('status', 'APPROVED')->count() }}
            </td>

        </tr>

        <tr>

            <td class="summary-title">
                Rejected
            </td>

            <td>
                {{ $history->where('status', 'REJECTED')->count() }}
            </td>

        </tr>

    </table>


    <div class="footer">

        Maintenance Management System
        -
        {{ now()->format('Y') }}

    </div>

</body>

</html>