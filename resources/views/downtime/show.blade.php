@extends('layouts.app')

@section('title', 'Downtime Details')

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
                <h1>Downtime Details</h1>

                <p>
                    View detailed information about this downtime record.
                </p>
            </div>

        </div>

        <a
            href="{{ route('downtime.edit', $downtime->id) }}"
            class="btn-primary"
        >
            Edit Downtime
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


    {{-- DETAIL CARD --}}
    <div class="downtime-detail-card">

        <div class="downtime-detail-header">

            <div>
                <h2>Downtime Information</h2>

                <p>
                    Detailed equipment downtime information.
                </p>
            </div>

            <span
                class="status-badge status-{{ strtolower($downtime->status) }}"
            >
                {{ $downtime->status }}
            </span>

        </div>


        <div class="downtime-detail-body">

            {{-- EQUIPMENT --}}
            <div class="detail-section">

                <h3>Equipment Information</h3>

                <div class="detail-grid">

                    <div class="detail-item">

                        <span class="detail-label">
                            Equipment Code
                        </span>

                        <strong class="detail-value">
                            {{ $downtime->equipment->equipment_code ?? '-' }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Equipment Name
                        </span>

                        <strong class="detail-value">
                            {{ $downtime->equipment->name ?? '-' }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Location
                        </span>

                        <strong class="detail-value">
                            {{ $downtime->equipment->location ?? '-' }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Equipment Status
                        </span>

                        <strong class="detail-value">
                            {{ $downtime->equipment->status ?? '-' }}
                        </strong>

                    </div>

                </div>

            </div>


            {{-- DOWNTIME --}}
            <div class="detail-section">

                <h3>Downtime Information</h3>

                <div class="detail-grid">

                    {{-- STARTED AT --}}
                    <div class="detail-item">

                        <span class="detail-label">
                            Start Downtime
                        </span>

                        <strong class="detail-value">

                            @if ($downtime->started_at)

                                {{ \Carbon\Carbon::parse(
                                    $downtime->started_at
                                )->format('d M Y, H:i') }}

                            @else

                                -

                            @endif

                        </strong>

                    </div>


                    {{-- ENDED AT --}}
                    <div class="detail-item">

                        <span class="detail-label">
                            End Downtime
                        </span>

                        <strong class="detail-value">

                            @if ($downtime->ended_at)

                                {{ \Carbon\Carbon::parse(
                                    $downtime->ended_at
                                )->format('d M Y, H:i') }}

                            @else

                                Still Down

                            @endif

                        </strong>

                    </div>


                    {{-- DURATION --}}
                    <div class="detail-item">

                        <span class="detail-label">
                            Duration
                        </span>

                        <strong class="detail-value">

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

                        </strong>

                    </div>


                    {{-- REASON --}}
                    <div class="detail-item">

                        <span class="detail-label">
                            Reason
                        </span>

                        <strong class="detail-value">
                            {{ $downtime->reason ?? '-' }}
                        </strong>

                    </div>

                </div>

            </div>


            {{-- DESCRIPTION --}}
            <div class="detail-section">

                <h3>Description</h3>

                <div class="description-box">

                    {{ $downtime->description ?: 'No description provided.' }}

                </div>

            </div>


            {{-- CREATED INFORMATION --}}
            <div class="detail-section">

                <h3>Record Information</h3>

                <div class="detail-grid">

                    <div class="detail-item">

                        <span class="detail-label">
                            Created By
                        </span>

                        <strong class="detail-value">
                            {{ $downtime->creator->username ?? '-' }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Created At
                        </span>

                        <strong class="detail-value">

                            @if ($downtime->created_at)

                                {{ $downtime->created_at->format(
                                    'd M Y, H:i'
                                ) }}

                            @else

                                -

                            @endif

                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Updated At
                        </span>

                        <strong class="detail-value">

                            @if ($downtime->updated_at)

                                {{ $downtime->updated_at->format(
                                    'd M Y, H:i'
                                ) }}

                            @else

                                -

                            @endif

                        </strong>

                    </div>

                </div>

            </div>


            {{-- ACTION --}}
            <div class="detail-actions">

                <a
                    href="{{ route('downtime.index') }}"
                    class="btn-secondary"
                >
                    Back to Downtime
                </a>


                {{-- EDIT --}}
                <a
                    href="{{ route('downtime.edit', $downtime) }}"
                    class="btn-primary"
                >
                    Edit Downtime
                </a>


                {{-- COMPLETE --}}
                @if ($downtime->status === 'ONGOING')

                    <form
                        action="{{ route('downtime.complete', $downtime) }}"
                        method="POST"
                        style="display: inline;"
                    >

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="btn-success"
                            onclick="return confirm('Apakah kamu yakin ingin menyelesaikan downtime ini?')"
                        >
                            Complete Downtime
                        </button>

                    </form>

                @endif


                {{-- DELETE --}}
                <form
                    action="{{ route('downtime.destroy', $downtime) }}"
                    method="POST"
                    style="display: inline;"
                >

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn-danger"
                        onclick="return confirm('Apakah kamu yakin ingin menghapus downtime ini?')"
                    >
                        Delete
                    </button>

                </form>

            </div>

        </div>

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

    .btn-success,
    .btn-danger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        border-radius: 10px;
        padding: 11px 18px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-success {
        background: #16a34a;
        color: #ffffff;
    }

    .btn-success:hover {
        background: #15803d;
    }

    .btn-danger {
        background: #dc2626;
        color: #ffffff;
    }

    .btn-danger:hover {
        background: #b91c1c;
    }

    .detail-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }

    .detail-actions form {
        margin: 0;
    }

    @media (max-width: 768px) {

        .detail-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .detail-actions a,
        .detail-actions button {
            width: 100%;
            box-sizing: border-box;
        }

    }

</style>

@endpush


@push('scripts')

<script
    src="{{ asset('js/downtime.js') }}"
></script>

@endpush