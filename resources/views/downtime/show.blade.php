@extends('layouts.app')

@section('title', 'Downtime Details')

@section('content')

<div class="downtime-page">

```
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

                <div class="detail-item">

                    <span class="detail-label">
                        Start Downtime
                    </span>

                    <strong class="detail-value">

                        {{ $downtime->start_time
                            ? \Carbon\Carbon::parse(
                                $downtime->start_time
                            )->format('d M Y, H:i')
                            : '-'
                        }}

                    </strong>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        End Downtime
                    </span>

                    <strong class="detail-value">

                        {{ $downtime->end_time
                            ? \Carbon\Carbon::parse(
                                $downtime->end_time
                            )->format('d M Y, H:i')
                            : 'Still Down'
                        }}

                    </strong>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Duration
                    </span>

                    <strong class="detail-value">
                        {{ $downtime->duration ?? '-' }}
                    </strong>

                </div>


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


        {{-- ACTION --}}
        <div class="detail-actions">

            <a
                href="{{ route('downtime.index') }}"
                class="btn-secondary"
            >
                Back to Downtime
            </a>

            <a
                href="{{ route('downtime.edit', $downtime->id) }}"
                class="btn-primary"
            >
                Edit Downtime
            </a>

        </div>

    </div>

</div>
```

</div>

@endsection

@push('styles')

<link
    rel="stylesheet"
    href="{{ asset('css/downtime.css') }}"
>

@endpush

@push('scripts')

<script
    src="{{ asset('js/downtime.js') }}"
></script>

@endpush
