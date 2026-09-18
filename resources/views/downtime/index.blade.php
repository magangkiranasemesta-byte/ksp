@extends('layouts.app')

@section('title', 'Downtime Equipment')

@section('content')

<div class="downtime-page">

    {{-- HEADER --}}
    <div class="downtime-header">

        <div class="downtime-header-left">

            <a href="{{ url()->previous() }}" class="btn-back">
                ←
                <span>Back</span>
            </a>

            <div>
                <h1>Downtime Equipment</h1>
                <p>
                    Monitor and manage equipment downtime records.
                </p>
            </div>

        </div>

        <a href="{{ route('downtime.create') }}" class="btn-primary">
            + Add Downtime
        </a>

    </div>


    {{-- SUMMARY --}}
    <div class="downtime-summary">

        <div class="downtime-summary-card">
            <div>
                <span>Total Downtime</span>
                <strong>{{ $downtimes->count() }}</strong>
            </div>
        </div>

        <div class="downtime-summary-card">
            <div>
                <span>Affected Equipment</span>
                <strong>
                    {{ $downtimes->unique('equipment_id')->count() }}
                </strong>
            </div>
        </div>

        <div class="downtime-summary-card">
            <div>
                <span>Active Downtime</span>
                <strong>
                    {{ $downtimes->where('status', 'ACTIVE')->count() }}
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

                <input
                    type="text"
                    id="downtimeSearch"
                    class="downtime-search"
                    placeholder="Search equipment..."
                >

                <select
                    id="statusFilter"
                    class="downtime-filter"
                >
                    <option value="">All Status</option>
                    <option value="ACTIVE">Active</option>
                    <option value="COMPLETED">Completed</option>
                </select>

            </div>

        </div>


        <div class="table-wrapper">

            <table class="downtime-table">

                <thead>

                    <tr>

                        <th>No</th>

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

                        <th>Start</th>

                        <th>End</th>

                        <th>Duration</th>

                        <th>Reason</th>

                        <th>Status</th>

                        <th>Action</th>

                    </tr>

                </thead>

                <tbody id="downtimeTableBody">

                    @forelse($downtimes as $downtime)

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

                            <td>
                                {{ $loop->iteration }}
                            </td>

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

                            <td>
                                {{ $downtime->start_time
                                    ? \Carbon\Carbon::parse($downtime->start_time)->format('d M Y H:i')
                                    : '-'
                                }}
                            </td>

                            <td>
                                {{ $downtime->end_time
                                    ? \Carbon\Carbon::parse($downtime->end_time)->format('d M Y H:i')
                                    : '-'
                                }}
                            </td>

                            <td>
                                {{ $downtime->duration ?? '-' }}
                            </td>

                            <td>
                                {{ $downtime->reason ?? '-' }}
                            </td>

                            <td>

                                <span
                                    class="status-badge status-{{ strtolower($downtime->status) }}"
                                >
                                    {{ $downtime->status }}
                                </span>

                            </td>

                            <td>

                                <div class="table-actions">

                                    <a
                                        href="{{ route('downtime.show', $downtime->id) }}"
                                        class="action-btn action-view"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="{{ route('downtime.edit', $downtime->id) }}"
                                        class="action-btn action-edit"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('downtime.destroy', $downtime->id) }}"
                                        method="POST"
                                        class="delete-form"
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

    </div>

</div>

@endsection


@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/downtime.css') }}"
>

@endpush


@push('scripts')

<script src="{{ asset('js/downtime.js') }}"></script>

@endpush