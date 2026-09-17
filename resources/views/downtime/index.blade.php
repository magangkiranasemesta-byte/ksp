<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Equipment Downtime - Maintenance X</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        .container {
            width: 95%;
            max-width: 1400px;
            margin: 30px auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .title h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .title p {
            margin: 0;
            color: #6b7280;
        }

        .btn {
            display: inline-block;
            padding: 11px 18px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-success:hover {
            background: #15803d;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f9fafb;
            padding: 15px;
            text-align: left;
            font-size: 13px;
            color: #4b5563;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background: #fafafa;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-ongoing {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-completed {
            background: #dcfce7;
            color: #166534;
        }

        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #6b7280;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .error-list {
            margin: 0;
            padding-left: 20px;
        }

        .action-form {
            display: inline;
        }

        .action-form button {
            border: none;
            cursor: pointer;
        }

        .pagination {
            padding: 20px;
        }

        .pagination nav {
            display: flex;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .title h1 {
                font-size: 23px;
            }

            .btn-primary {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

<div class="container">

    {{-- HEADER --}}
    <div class="header">

        <div class="title">
            <h1>Equipment Downtime</h1>

            <p>
                Catat dan pantau waktu equipment tidak dapat digunakan.
            </p>
        </div>

        <a href="{{ route('downtime.create') }}"
           class="btn btn-primary">
            + Mulai Downtime
        </a>

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- ERROR MESSAGE --}}
    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif


    {{-- VALIDATION ERROR --}}
    @if($errors->any())
        <div class="alert alert-error">

            <ul class="error-list">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>
    @endif


    {{-- TABLE --}}
    <div class="card">

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>No</th>

                        <th>Equipment</th>

                        <th>Mulai Downtime</th>

                        <th>Selesai</th>

                        <th>Durasi</th>

                        <th>Alasan</th>

                        <th>Status</th>

                        <th>Dibuat Oleh</th>

                        <th>Aksi</th>

                    </tr>

                </thead>

                <tbody>

                @forelse($downtimes as $downtime)

                    <tr>

                        {{-- NOMOR --}}
                        <td>
                            {{ $downtimes->firstItem() + $loop->index }}
                        </td>


                        {{-- EQUIPMENT --}}
                        <td>

                            <strong>
                                {{ $downtime->equipment->name ?? 'Equipment #' . $downtime->equipment_id }}
                            </strong>

                        </td>


                        {{-- START --}}
                        <td>

                            {{ $downtime->started_at
                                ? $downtime->started_at->format('d/m/Y H:i')
                                : '-' }}

                        </td>


                        {{-- END --}}
                        <td>

                            @if($downtime->ended_at)

                                {{ $downtime->ended_at->format('d/m/Y H:i') }}

                            @else

                                -

                            @endif

                        </td>


                        {{-- DURATION --}}
                        <td>

                            <strong>
                                {{ $downtime->duration }}
                            </strong>

                        </td>


                        {{-- REASON --}}
                        <td>

                            {{ $downtime->reason }}

                            @if($downtime->description)

                                <br>

                                <small style="color:#6b7280;">
                                    {{ $downtime->description }}
                                </small>

                            @endif

                        </td>


                        {{-- STATUS --}}
                        <td>

                            @if($downtime->status === 'ONGOING')

                                <span class="badge badge-ongoing">
                                    ONGOING
                                </span>

                            @else

                                <span class="badge badge-completed">
                                    COMPLETED
                                </span>

                            @endif

                        </td>


                        {{-- CREATED BY --}}
                        <td>

                            {{ $downtime->creator->username ?? '-' }}

                        </td>


                        {{-- ACTION --}}
                        <td>

                            @if($downtime->status === 'ONGOING')

                                <form
                                    action="{{ route('downtime.complete', $downtime->id) }}"
                                    method="POST"
                                    class="action-form"
                                    onsubmit="return confirm('Apakah downtime ini sudah selesai?')"
                                >

                                    @csrf

                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn btn-success"
                                    >
                                        Selesaikan
                                    </button>

                                </form>

                            @else

                                <span style="color:#9ca3af;">
                                    Selesai
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="9">

                            <div class="empty">

                                <h3>Belum ada data downtime</h3>

                                <p>
                                    Belum ada equipment yang tercatat mengalami downtime.
                                </p>

                                <a href="{{ route('downtime.create') }}"
                                   class="btn btn-primary">

                                    + Mulai Downtime

                                </a>

                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($downtimes->hasPages())

            <div class="pagination">

                {{ $downtimes->links() }}

            </div>

        @endif

    </div>

</div>

</body>
</html>