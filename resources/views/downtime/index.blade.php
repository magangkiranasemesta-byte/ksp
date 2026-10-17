
@extends('layouts.app')

@section('title', 'Downtime Equipment')

@section('content')

<div class="downtime-list-page">

    {{-- HEADER --}}
    <header class="dt-list-header">
        <div class="dt-list-heading">
            <div class="dt-list-heading-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 9v4m0 4h.01M10.3 3.9 2 18a2 2 0 0 0 1.7 3h16.6a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"
                          stroke-width="1.8"
                          stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </div>

            <div>
                <h1>Downtime Equipment</h1>
                <p>Monitor and manage equipment downtime records.</p>
            </div>
        </div>

        <a href="{{ route('downtime.create') }}" class="dt-add-button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M12 5v14m-7-7h14"
                      stroke-width="2"
                      stroke-linecap="round"/>
            </svg>
            <span>Add Downtime</span>
        </a>
    </header>

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <div class="dt-alert dt-alert-success" role="status">
            <span class="dt-alert-icon">✓</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="dt-alert dt-alert-danger" role="alert">
            <span class="dt-alert-icon">!</span>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="dt-alert dt-alert-danger" role="alert">
            <span class="dt-alert-icon">!</span>
            <div>
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- SUMMARY --}}
    <section class="dt-summary-grid">

        <article class="dt-summary-card">
            <div class="dt-summary-top">
                <span>Total Downtime</span>
                <span class="dt-summary-icon dt-icon-blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M4 5h16v14H4zM8 9h8m-8 4h5"
                              stroke-width="1.8"
                              stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
            <strong>{{ $totalDowntime ?? $downtimes->total() }}</strong>
            <p>All recorded downtime entries</p>
        </article>

        <article class="dt-summary-card">
            <div class="dt-summary-top">
                <span>Affected Equipment</span>
                <span class="dt-summary-icon dt-icon-violet">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="4" y="4" width="16" height="16" rx="2"
                              stroke-width="1.8"/>
                        <path d="M9 9h6v6H9z"
                              stroke-width="1.8"
                              stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
            <strong>{{ $affectedEquipment ?? $downtimes->pluck('equipment_id')->unique()->count() }}</strong>
            <p>Distinct equipment in this result</p>
        </article>

        <article class="dt-summary-card">
            <div class="dt-summary-top">
                <span>Active Downtime</span>
                <span class="dt-summary-icon dt-icon-amber">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                        <path d="M12 7v5l3 2"
                              stroke-width="1.8"
                              stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
            <strong>{{ $ongoingDowntime ?? 0 }}</strong>
            <p>Equipment downtime still in progress</p>
        </article>

    </section>

    {{-- TABLE CARD --}}
    <section class="dt-record-card">

        <div class="dt-record-header">
            <div class="dt-record-title">
                <h2>Downtime Records</h2>
                <p>Review equipment interruptions and their duration.</p>
            </div>

            <div class="dt-record-tools">
                <label class="dt-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                        <path d="m16 16 4 4"
                              stroke-width="1.8"
                              stroke-linecap="round"/>
                    </svg>
                    <input
                        type="search"
                        id="downtimeSearch"
                        placeholder="Search equipment..."
                        aria-label="Search downtime records"
                    >
                </label>

                <select id="statusFilter"
                        class="dt-status-filter"
                        aria-label="Filter by downtime status">
                    <option value="">All Status</option>
                    <option value="ACTIVE">Active</option>
                    <option value="ONGOING">Ongoing</option>
                    <option value="COMPLETED">Completed</option>
                </select>
            </div>
        </div>

        {{-- TABLE SCROLL CONTAINER --}}
        <div class="dt-table-scroll">
            <table class="dt-data-table">
                <thead>
                    <tr>
                        <th class="dt-col-no">No</th>
                        <th>
                            <button type="button"
                                    class="dt-sort-button"
                                    data-sort="equipment">
                                Equipment <span>↕</span>
                            </button>
                        </th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Duration</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th class="dt-col-action">Actions</th>
                    </tr>
                </thead>

                <tbody id="downtimeTableBody">
                    @forelse ($downtimes as $downtime)

                        @php
                            $status = strtoupper((string) $downtime->status);
                            $start = $downtime->started_at;
                            $end = $downtime->ended_at;

                            $equipmentCode = $downtime->equipment?->equipment_code ?? '-';
                            $equipmentName = $downtime->equipment?->name ?? '-';
                            $reason = $downtime->reason ?? '-';

                            $duration = $start
                                ? \Carbon\Carbon::parse($start)->diffForHumans(
                                    $end ? \Carbon\Carbon::parse($end) : now(),
                                    true
                                )
                                : '-';
                        @endphp

                        <tr
                            data-status="{{ $status }}"
                            data-search="{{ strtolower($equipmentCode . ' ' . $equipmentName . ' ' . $reason . ' ' . ($downtime->description ?? '')) }}"
                            data-equipment="{{ strtolower($equipmentCode . ' ' . $equipmentName) }}"
                        >
                            <td class="dt-number-cell">
                                {{ $downtimes->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <div class="dt-equipment-info">
                                    <span class="dt-equipment-avatar">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <rect x="4" y="4" width="16" height="16" rx="2"
                                                  stroke-width="1.7"/>
                                            <path d="M9 9h6v6H9z"
                                                  stroke-width="1.7"
                                                  stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <div>
                                        <strong>{{ $equipmentCode }}</strong>
                                        <span>{{ $equipmentName }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="dt-date-cell">
                                @if ($start)
                                    <span>{{ \Carbon\Carbon::parse($start)->format('d M Y') }}</span>
                                    <small>{{ \Carbon\Carbon::parse($start)->format('H:i') }}</small>
                                @else
                                    —
                                @endif
                            </td>

                            <td class="dt-date-cell">
                                @if ($end)
                                    <span>{{ \Carbon\Carbon::parse($end)->format('d M Y') }}</span>
                                    <small>{{ \Carbon\Carbon::parse($end)->format('H:i') }}</small>
                                @else
                                    <span class="dt-still-down">Still Down</span>
                                @endif
                            </td>

                            <td>
                                <span class="dt-duration-value">{{ $duration }}</span>
                            </td>

                            <td>
                                <span class="dt-reason-text"
                                      title="{{ $reason }}">
                                    {{ $reason }}
                                </span>
                            </td>

                            <td>
                                <span class="dt-status-badge dt-status-{{ strtolower($status) }}">
                                    <span class="dt-status-dot"></span>
                                    {{ $status }}
                                </span>
                            </td>

                            <td>
                                <div class="dt-row-actions">
                                    <a
                                        href="{{ route('downtime.show', ['downtime' => $downtime->id]) }}"
                                        class="dt-action-button dt-action-view"
                                    >View</a>

                                    <a
                                        href="{{ route('downtime.edit', ['downtime' => $downtime->id]) }}"
                                        class="dt-action-button dt-action-edit"
                                    >Edit</a>

                                    <form
                                        action="{{ route('downtime.destroy', ['downtime' => $downtime->id]) }}"
                                        method="POST"
                                        class="dt-delete-form"
                                        onsubmit="return confirm('Apakah kamu yakin ingin menghapus downtime ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="dt-action-button dt-action-delete">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="8" class="dt-empty-cell">
                                <div class="dt-empty-state">
                                    <span class="dt-empty-icon">—</span>
                                    <strong>No downtime records found</strong>
                                    <p>Belum ada data downtime yang tersedia.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                    <tr id="downtimeNoResults" hidden>
                        <td colspan="8" class="dt-empty-cell">
                            <div class="dt-empty-state">
                                <strong>No matching records</strong>
                                <p>Coba gunakan kata kunci atau filter status yang berbeda.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- TABLE FOOTER --}}
        <footer class="dt-table-footer">
            <p class="dt-results-info" id="downtimeResultsInfo">
                @if ($downtimes->total() > 0)
                    Showing {{ $downtimes->firstItem() }}–{{ $downtimes->lastItem() }}
                    of {{ $downtimes->total() }} records
                @else
                    No records available
                @endif
            </p>

            @if ($downtimes->hasPages())
                <div class="dt-pagination">
                    {{ $downtimes->links() }}
                </div>
            @endif
        </footer>

    </section>

</div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/downtime.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/downtime.js') }}" defer></script>
@endpush
