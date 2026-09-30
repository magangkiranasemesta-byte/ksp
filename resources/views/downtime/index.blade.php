@extends('layouts.app')

@section('title', 'Downtime Equipment')

@section('content')

<div class="downtime-page">

    {{-- HEADER --}}
    <div class="downtime-header">

        <div class="downtime-header-left">

            <a
                href="{{ route('downtime.index') }}"
                class="btn-back"
            >
                <span class="back-icon">←</span>
                <span>Back</span>
            </a>

            <div>
                <h1>Downtime Equipment</h1>

                <p>
                    Monitor and manage equipment downtime records.
                </p>
            </div>

        </div>

        <a
            href="{{ route('downtime.create') }}"
            class="btn-primary"
        >
            + Add Downtime
        </a>

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ERROR MESSAGE --}}
    @if (session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif


    {{-- VALIDATION ERROR --}}
    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>Terjadi kesalahan:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    {{-- SUMMARY --}}
    <div class="downtime-summary">

        {{-- TOTAL --}}
        <div class="downtime-summary-card">

            <div>

                <span>Total Downtime</span>

                <strong>
                    {{ $totalDowntime ?? $downtimes->total() }}
                </strong>

            </div>

        </div>


        {{-- AFFECTED EQUIPMENT --}}
        <div class="downtime-summary-card">

            <div>

                <span>Affected Equipment</span>

                <strong>
                    {{ $downtimes->pluck('equipment_id')->unique()->count() }}
                </strong>

            </div>

        </div>


        {{-- ONGOING --}}
        <div class="downtime-summary-card">

            <div>

                <span>Active Downtime</span>

                <strong>
                    {{ $ongoingDowntime ?? 0 }}
                </strong>

            </div>

        </div>

    </div>


    {{-- TABLE CARD --}}
    <div class="downtime-card">

        <div class="downtime-card-header">

            <div>

                <h2>Downtime Records</h2>

                <p>
                    List of equipment downtime records.
                </p>

            </div>


            <div class="downtime-tools">

                {{-- SEARCH --}}
                <input
                    type="text"
                    id="downtimeSearch"
                    class="downtime-search"
                    placeholder="Search equipment..."
                >


                {{-- STATUS FILTER --}}
                <select
                    id="statusFilter"
                    class="downtime-filter"
                >

                    <option value="">
                        All Status
                    </option>

                    <option value="ONGOING">
                        Ongoing
                    </option>

                    <option value="COMPLETED">
                        Completed
                    </option>

                </select>

            </div>

        </div>


        {{-- TABLE --}}
        <div class="table-wrapper">

            <table class="downtime-table">

                <thead>

                    <tr>

                        <th>
                            No
                        </th>


                        <th>

                            Equipment

                            <button
                                type="button"
                                class="sort-btn"
                                data-sort="equipment"
                            >
                                ↕
                            </button>

                        </th>


                        <th>
                            Start
                        </th>


                        <th>
                            End
                        </th>


                        <th>
                            Duration
                        </th>


                        <th>
                            Reason
                        </th>


                        <th>
                            Status
                        </th>


                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody id="downtimeTableBody">

                    @forelse ($downtimes as $downtime)

                        <tr
                            data-status="{{ $downtime->status }}"
                            data-search="{{ strtolower(
                                ($downtime->equipment->equipment_code ?? '') .
                                ' ' .
                                ($downtime->equipment->name ?? '') .
                                ' ' .
                                ($downtime->reason ?? '')
                            ) }}"
                        >

                            {{-- NO --}}
                            <td>

                                {{ $downtimes->firstItem() + $loop->index }}

                            </td>


                            {{-- EQUIPMENT --}}
                            <td>

                                <div class="equipment-info">

                                    <strong>
                                        {{ $downtime->equipment->equipment_code ?? '-' }}
                                    </strong>

                                    <span>
                                        {{ $downtime->equipment->name ?? '-' }}
                                    </span>

                                </div>

                            </td>


                            {{-- START --}}
                            <td>

                                @if ($downtime->started_at)

                                    {{ \Carbon\Carbon::parse(
                                        $downtime->started_at
                                    )->format('d M Y H:i') }}

                                @else

                                    -

                                @endif

                            </td>


                            {{-- END --}}
                            <td>

                                @if ($downtime->ended_at)

                                    {{ \Carbon\Carbon::parse(
                                        $downtime->ended_at
                                    )->format('d M Y H:i') }}

                                @else

                                    <span>
                                        Still Down
                                    </span>

                                @endif

                            </td>


                            {{-- DURATION --}}
                            <td>

                                @if ($downtime->started_at)

                                    @if ($downtime->ended_at)

                                        {{ \Carbon\Carbon::parse(
                                            $downtime->started_at
                                        )->diffForHumans(
                                            \Carbon\Carbon::parse(
                                                $downtime->ended_at
                                            ),
                                            true
                                        ) }}

                                    @else

                                        {{ \Carbon\Carbon::parse(
                                            $downtime->started_at
                                        )->diffForHumans(now(), true) }}

                                    @endif

                                @else

                                    -

                                @endif

                            </td>


                            {{-- REASON --}}
                            <td>

                                {{ $downtime->reason ?? '-' }}

                            </td>


                            {{-- STATUS --}}
                            <td>

                                <span
                                    class="status-badge status-{{ strtolower($downtime->status) }}"
                                >
                                    {{ $downtime->status }}
                                </span>

                            </td>


                            {{-- ACTION --}}
                            <td>

                                <div class="table-actions">

                                    {{-- VIEW --}}
                                    <a
                                        href="{{ route('downtime.show', ['downtime' => $downtime->id]) }}"
                                        class="action-btn action-view"
                                    >
                                        View
                                    </a>


                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route('downtime.edit', ['downtime' => $downtime->id]) }}"
                                        class="action-btn action-edit"
                                    >
                                        Edit
                                    </a>


                                    {{-- DELETE --}}
                                    <form
                                        action="{{ route('downtime.destroy', ['downtime' => $downtime->id]) }}"
                                        method="POST"
                                        class="delete-form"
                                        onsubmit="return confirm('Apakah kamu yakin ingin menghapus downtime ini?')"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="action-btn action-delete"
                                        >
                                            Delete
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="empty-state"
                            >
                                No downtime records found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if ($downtimes->hasPages())

            <div class="downtime-pagination">

                {{ $downtimes->links() }}

            </div>

        @endif

    </div>

</div>

@endsection


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/downtime.css') }}"
>

<style>

    .alert {
        margin-bottom: 20px;
        padding: 14px 16px;
        border-radius: 10px;
    }

    .alert-success {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .alert-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .alert ul {
        margin: 8px 0 0 20px;
    }

    .table-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .delete-form {
        margin: 0;
    }

    .downtime-pagination {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

</style>

@endpush


@push('scripts')

<script src="{{ asset('js/downtime.js') }}"></script>

@endpush