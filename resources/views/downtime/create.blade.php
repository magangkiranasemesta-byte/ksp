
@extends('layouts.app')

@section('title', 'Create Downtime Equipment')

@section('content')

<div class="downtime-page">

    {{-- Breadcrumb --}}
    <nav class="downtime-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('downtime.index') }}">Downtime Equipment</a>
        <span>/</span>
        <span class="current">Create New</span>
    </nav>

    {{-- Page Header --}}
    <header class="downtime-header">
        <div class="downtime-header-left">

            <div class="downtime-page-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 9v4m0 4h.01M10.3 3.9 2 18a2 2 0 0 0 1.7 3h16.6a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"
                          stroke-width="1.8"
                          stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </div>

            <div class="downtime-heading-text">
                <h1>Create Downtime</h1>
                <p>Catat gangguan dan waktu berhentinya operasional equipment.</p>
            </div>

        </div>

        <a href="{{ route('downtime.index') }}" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="m15 18-6-6 6-6"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"/>
            </svg>
            <span>Back to Downtime</span>
        </a>
    </header>

    {{-- Validation Summary --}}
    @if ($errors->any())
        <div class="downtime-alert" role="alert">
            <div class="downtime-alert-icon">!</div>
            <div>
                <h2>Please check your input</h2>
                <p>Periksa kembali informasi berikut sebelum menyimpan data.</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Form Card --}}
    <section class="downtime-form-card">

        <div class="downtime-form-header">
            <div class="downtime-section-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M4 5h16v14H4zM8 9h8m-8 4h5"
                          stroke-width="1.8"
                          stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </div>

            <div>
                <h2>Downtime Information</h2>
                <p>Lengkapi detail equipment, waktu gangguan, dan status penanganan.</p>
            </div>
        </div>

        <form
            action="{{ route('downtime.store') }}"
            method="POST"
            id="downtimeForm"
            class="downtime-form"
        >
            @csrf

            {{-- Equipment --}}
            <div class="form-group">
                <label for="equipment_id">
                    Equipment <span class="required">*</span>
                </label>

                <select
                    name="equipment_id"
                    id="equipment_id"
                    class="form-control @error('equipment_id') is-invalid @enderror"
                    required
                    aria-describedby="equipmentHelp"
                >
                    <option value="">-- Select Equipment --</option>

                    @foreach ($equipments as $equipment)
                        <option
                            value="{{ $equipment->id }}"
                            @selected((string) old('equipment_id') === (string) $equipment->id)
                        >
                            {{ $equipment->equipment_code }} — {{ $equipment->name }}
                        </option>
                    @endforeach
                </select>

                <small class="form-hint" id="equipmentHelp">
                    Pilih equipment yang mengalami gangguan atau berhenti beroperasi.
                </small>

                @error('equipment_id')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Downtime Period --}}
            <div class="downtime-subsection">
                <div class="subsection-heading">
                    <div class="subsection-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="9"
                                    stroke-width="1.8"/>
                            <path d="M12 7v5l3 2"
                                  stroke-width="1.8"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <h3>Downtime Period</h3>
                        <p>Masukkan waktu mulai dan waktu selesai gangguan.</p>
                    </div>
                </div>

                <div class="form-row">

                    <div class="form-group">
                        <label for="start_time">
                            Start Downtime <span class="required">*</span>
                        </label>

                        <input
                            type="datetime-local"
                            name="start_time"
                            id="start_time"
                            class="form-control @error('start_time') is-invalid @enderror"
                            value="{{ old('start_time') }}"
                            required
                        >

                        @error('start_time')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="end_time">
                            End Downtime
                            <span class="optional-label">Optional</span>
                        </label>

                        <input
                            type="datetime-local"
                            name="end_time"
                            id="end_time"
                            class="form-control @error('end_time') is-invalid @enderror"
                            value="{{ old('end_time') }}"
                            aria-describedby="endTimeHelp"
                        >

                        <small class="form-hint" id="endTimeHelp">
                            Kosongkan jika equipment masih mengalami downtime.
                        </small>

                        @error('end_time')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="downtime-duration-preview" id="durationPreview" hidden>
                    <span class="duration-preview-label">Estimated downtime duration</span>
                    <strong id="durationValue">—</strong>
                </div>
            </div>

            {{-- Reason --}}
            <div class="form-group">
                <label for="reason">
                    Downtime Reason <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="reason"
                    id="reason"
                    class="form-control @error('reason') is-invalid @enderror"
                    value="{{ old('reason') }}"
                    placeholder="Contoh: Motor overheating, power failure..."
                    maxlength="255"
                    required
                >

                <small class="form-hint">
                    Tuliskan penyebab atau indikasi utama terjadinya downtime.
                </small>

                @error('reason')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label for="description">
                    Description
                    <span class="optional-label">Optional</span>
                </label>

                <textarea
                    name="description"
                    id="description"
                    class="form-control form-textarea @error('description') is-invalid @enderror"
                    rows="5"
                    placeholder="Jelaskan kondisi equipment, gejala gangguan, dampak operasional, atau informasi pendukung lainnya..."
                >{{ old('description') }}</textarea>

                <div class="textarea-footer">
                    <small class="form-hint">
                        Tambahkan detail untuk membantu proses investigasi dan perbaikan.
                    </small>
                    <small class="character-counter">
                        <span id="descriptionCount">0</span> karakter
                    </small>
                </div>

                @error('description')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Status --}}
            <div class="form-group">
                <label for="status">
                    Downtime Status <span class="required">*</span>
                </label>

                <select
                    name="status"
                    id="status"
                    class="form-control @error('status') is-invalid @enderror"
                    required
                >
                    <option
                        value="ACTIVE"
                        @selected(old('status', 'ACTIVE') === 'ACTIVE')
                    >
                        Active — Equipment masih mengalami downtime
                    </option>

                    <option
                        value="COMPLETED"
                        @selected(old('status') === 'COMPLETED')
                    >
                        Completed — Downtime telah selesai
                    </option>
                </select>

                <div class="status-info" id="statusInfo">
                    <span class="status-indicator"></span>
                    <p id="statusDescription">
                        Downtime aktif akan tercatat sebagai gangguan yang masih berlangsung.
                    </p>
                </div>

                @error('status')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Footer Actions --}}
            <div class="downtime-form-actions">
                <p class="required-note">
                    <span class="required">*</span> Wajib diisi
                </p>

                <div class="downtime-action-buttons">
                    <a href="{{ route('downtime.index') }}" class="btn-secondary">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn-primary"
                        id="submitDowntime"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="m5 12 4 4L19 6"
                                  stroke-width="2"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>
                        <span id="submitDowntimeText">Create Downtime</span>
                    </button>
                </div>
            </div>

        </form>
    </section>

    <p class="downtime-footer-note">
        Pastikan informasi yang dimasukkan sesuai dengan kondisi aktual equipment.
    </p>

</div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/downtime.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/downtime.js') }}" defer></script>
@endpush
