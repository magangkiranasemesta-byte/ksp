
@extends('layouts.app')

@section('title', 'Create Work Order')

@section('content')

<div class="min-h-screen bg-slate-50/70 -m-4 p-4 sm:-m-6 sm:p-6 lg:p-8">

    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('work-orders.index') }}"
               class="hover:text-blue-600 transition">
                Work Orders
            </a>
            <span>/</span>
            <span class="font-medium text-slate-800">Create New</span>
        </nav>

        {{-- Page Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center
                            rounded-2xl bg-blue-100 text-blue-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="1.8"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                        Create Work Order
                    </h1>
                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Buat dan jadwalkan pekerjaan pemeliharaan equipment.
                    </p>
                </div>
            </div>

            <a href="{{ route('work-orders.index') }}"
               class="inline-flex min-h-11 items-center justify-center gap-2
                      rounded-xl border border-slate-200 bg-white px-4 py-2.5
                      text-sm font-semibold text-slate-700 shadow-sm
                      transition hover:bg-slate-100 hover:border-slate-300
                      focus:outline-none focus:ring-2 focus:ring-blue-500
                      focus:ring-offset-2">

                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>

                Back to Work Orders
            </a>

        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 sm:p-5"
                 role="alert">

                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 9v4m0 4h.01M10.3 3.9L1.8 18.6A1.6 1.6 0 003.2 21h17.6a1.6 1.6 0 001.4-2.4L13.7 3.9a2 2 0 00-3.4 0z"/>
                    </svg>

                    <div>
                        <h2 class="font-semibold text-red-800">
                            Work Order belum dapat disimpan
                        </h2>

                        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('work-orders.store') }}"
              method="POST"
              id="workOrderForm"
              class="space-y-6">

            @csrf

            {{-- Section 1: General Information --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200
                            bg-white shadow-sm">

                <div class="flex items-start gap-3 border-b border-slate-100
                            px-5 py-5 sm:px-7">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                rounded-xl bg-blue-50 text-blue-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.8"
                                  d="M9 5h6m-7 4h8m-8 4h8m-8 4h5M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="font-bold text-slate-900">
                            General Information
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Pilih sumber pekerjaan dan equipment yang akan ditangani.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:gap-6 sm:p-7">

                    {{-- Maintenance Request --}}
                    <div class="sm:col-span-2">
                        <label for="maintenance_request_id"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Maintenance Request
                            <span class="ml-1 font-normal text-slate-400">(Optional)</span>
                        </label>

                        <select name="maintenance_request_id"
                                id="maintenance_request_id"
                                class="form-input">
                            <option value="">Manual Work Order — tanpa request</option>

                            @foreach ($maintenanceRequests as $requestItem)
                                <option
                                    value="{{ $requestItem->id }}"
                                    data-equipment="{{ $requestItem->equipment_id }}"
                                    data-priority="{{ $requestItem->priority }}"
                                    data-description="{{ $requestItem->description }}"
                                    @selected(
                                        (string) old(
                                            'maintenance_request_id',
                                            $selectedRequest?->id
                                        ) === (string) $requestItem->id
                                    )>

                                    MR-{{ $requestItem->id }}
                                    — {{ $requestItem->equipment?->equipment_code }}
                                    — {{ $requestItem->equipment?->name }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Pilih request yang sudah disetujui untuk mengisi informasi pekerjaan secara otomatis.
                        </p>

                        <div id="requestNotice"
                             class="mt-3 hidden rounded-xl border border-blue-100
                                    bg-blue-50 p-3 text-sm text-blue-800">
                            <span class="font-semibold">Request terpilih.</span>
                            Equipment, priority, dan deskripsi akan disesuaikan dengan request.
                        </div>
                    </div>

                    {{-- Equipment --}}
                    <div>
                        <label for="equipment_id"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Equipment <span class="text-red-500">*</span>
                        </label>

                        <select name="equipment_id"
                                id="equipment_id"
                                required
                                class="form-input @error('equipment_id') border-red-400 @enderror">

                            <option value="">-- Select Equipment --</option>

                            @foreach ($equipment as $item)
                                <option
                                    value="{{ $item->id }}"
                                    @selected(
                                        (string) old(
                                            'equipment_id',
                                            $selectedRequest?->equipment_id
                                        ) === (string) $item->id
                                    )>

                                    {{ $item->equipment_code }} — {{ $item->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('equipment_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Technician --}}
                    <div>
                        <label for="technician_id"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Technician / Engineer
                            <span class="font-normal text-slate-400">(Optional)</span>
                        </label>

                        <select name="technician_id"
                                id="technician_id"
                                class="form-input">

                            <option value="">Unassigned — belum ditugaskan</option>

                            @foreach ($technicians as $technician)
                                <option
                                    value="{{ $technician->id }}"
                                    @selected(
                                        (string) old('technician_id') ===
                                        (string) $technician->id
                                    )>

                                    {{ $technician->username }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-slate-500">
                            Teknisi dapat ditugaskan sekarang atau nanti.
                        </p>
                    </div>

                    {{-- Maintenance Type --}}
                    <div>
                        <label for="maintenance_type"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Maintenance Type
                        </label>

                        <select name="maintenance_type"
                                id="maintenance_type"
                                class="form-input">

                            <option value="CORRECTIVE"
                                @selected(old('maintenance_type', 'CORRECTIVE') === 'CORRECTIVE')>
                                Corrective Maintenance
                            </option>

                            <option value="PREVENTIVE"
                                @selected(old('maintenance_type') === 'PREVENTIVE')>
                                Preventive Maintenance
                            </option>

                            <option value="INSPECTION"
                                @selected(old('maintenance_type') === 'INSPECTION')>
                                Inspection
                            </option>
                        </select>
                    </div>

                    {{-- Priority --}}
                    <div>
                        <label for="priority"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Priority
                        </label>

                        <select name="priority"
                                id="priority"
                                class="form-input">

                            @foreach ([
                                'LOW' => 'Low',
                                'MEDIUM' => 'Medium',
                                'HIGH' => 'High',
                                'CRITICAL' => 'Critical'
                            ] as $value => $label)

                                <option
                                    value="{{ $value }}"
                                    @selected(
                                        old(
                                            'priority',
                                            $selectedRequest?->priority ?? 'MEDIUM'
                                        ) === $value
                                    )>

                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-slate-500">
                            Tentukan tingkat urgensi pekerjaan.
                        </p>
                    </div>

                </div>
            </section>

            {{-- Section 2: Problem Details --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200
                            bg-white shadow-sm">

                <div class="flex items-start gap-3 border-b border-slate-100
                            px-5 py-5 sm:px-7">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                rounded-xl bg-amber-50 text-amber-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.8"
                                  d="M12 9v4m0 4h.01M10.3 3.9L1.8 18.6A1.6 1.6 0 003.2 21h17.6a1.6 1.6 0 001.4-2.4L13.7 3.9a2 2 0 00-3.4 0z"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="font-bold text-slate-900">
                            Problem & Maintenance Details
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Dokumentasikan masalah dan tindakan perbaikan equipment.
                        </p>
                    </div>
                </div>

                <div class="space-y-6 p-5 sm:p-7">

                    {{-- Problem Description --}}
                    <div>
                        <label for="problem_description"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Problem Description
                        </label>

                        <textarea
                            name="problem_description"
                            id="problem_description"
                            rows="4"
                            class="form-input resize-y"
                            placeholder="Jelaskan kerusakan, gejala, atau pekerjaan yang harus dilakukan..."
                        >{{ old('problem_description', $selectedRequest?->description) }}</textarea>

                        <p class="mt-2 text-xs text-slate-500">
                            Deskripsikan kondisi equipment dengan jelas agar pekerjaan mudah dipahami.
                        </p>
                    </div>

                    {{-- Root Cause --}}
                    <div>
                        <label for="root_cause"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Root Cause
                            <span class="ml-1 font-normal text-slate-400">(Optional)</span>
                        </label>

                        <textarea
                            name="root_cause"
                            id="root_cause"
                            rows="3"
                            class="form-input resize-y"
                            placeholder="Penyebab utama kerusakan, jika sudah diketahui..."
                        >{{ old('root_cause') }}</textarea>
                    </div>

                    {{-- Corrective Action --}}
                    <div>
                        <label for="corrective_action"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Corrective Action
                            <span class="ml-1 font-normal text-slate-400">(Optional)</span>
                        </label>

                        <textarea
                            name="corrective_action"
                            id="corrective_action"
                            rows="3"
                            class="form-input resize-y"
                            placeholder="Tindakan perbaikan yang direncanakan atau dilakukan..."
                        >{{ old('corrective_action') }}</textarea>
                    </div>

                </div>
            </section>

            {{-- Section 3: Schedule --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200
                            bg-white shadow-sm">

                <div class="flex items-start gap-3 border-b border-slate-100
                            px-5 py-5 sm:px-7">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.8"
                                  d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 012 2v13a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="font-bold text-slate-900">
                            Maintenance Schedule
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Tentukan rencana waktu pengerjaan maintenance.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:gap-6 sm:p-7">

                    <div>
                        <label for="planned_start"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Planned Start
                        </label>

                        <input
                            type="datetime-local"
                            name="planned_start"
                            id="planned_start"
                            value="{{ old('planned_start') }}"
                            class="form-input"
                        >

                        <p class="mt-2 text-xs text-slate-500">
                            Tanggal dan waktu mulai yang direncanakan.
                        </p>
                    </div>

                    <div>
                        <label for="planned_end"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Planned End
                        </label>

                        <input
                            type="datetime-local"
                            name="planned_end"
                            id="planned_end"
                            value="{{ old('planned_end') }}"
                            class="form-input"
                        >

                        <p class="mt-2 text-xs text-slate-500">
                            Estimasi waktu penyelesaian pekerjaan.
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <div class="flex items-start gap-3 rounded-xl border
                                    border-blue-100 bg-blue-50/70 p-4">

                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-blue-600"
                                 fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>

                            <div>
                                <p class="text-sm font-semibold text-blue-900">
                                    Schedule Reminder
                                </p>
                                <p class="mt-1 text-xs leading-5 text-blue-800">
                                    Pastikan waktu selesai tidak lebih awal daripada waktu mulai.
                                    Jadwal merupakan estimasi, bukan waktu aktual pengerjaan.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            {{-- Form Actions --}}
            <div class="sticky bottom-0 z-10 -mx-4 border-t border-slate-200
                        bg-white/95 px-4 py-4 backdrop-blur
                        sm:static sm:mx-0 sm:rounded-2xl sm:border
                        sm:px-6 sm:shadow-sm">

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                    <a href="{{ route('work-orders.index') }}"
                       class="inline-flex min-h-12 items-center justify-center
                              rounded-xl border border-slate-200 bg-white px-6
                              text-sm font-semibold text-slate-700
                              transition hover:bg-slate-50
                              focus:outline-none focus:ring-2 focus:ring-slate-400">

                        Cancel
                    </a>

                    <button type="submit"
                            id="submitButton"
                            class="inline-flex min-h-12 items-center justify-center
                                   gap-2 rounded-xl bg-blue-600 px-6
                                   text-sm font-semibold text-white shadow-sm
                                   transition hover:bg-blue-700
                                   active:bg-blue-800
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-500 focus:ring-offset-2
                                   disabled:cursor-not-allowed disabled:opacity-60">

                        <svg class="h-5 w-5" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>

                        <span id="submitButtonText">Create Work Order</span>
                    </button>

                </div>
            </div>

        </form>

    </div>
</div>

{{-- Component Styles --}}
<style>
    .form-input {
        display: block;
        width: 100%;
        min-height: 46px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        background-color: #fff;
        padding: 11px 13px;
        color: #0f172a;
        font-size: 14px;
        line-height: 1.5;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .form-input::placeholder {
        color: #94a3b8;
    }

    .form-input:hover {
        border-color: #94a3b8;
    }

    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgb(59 130 246 / 12%);
    }

    .form-input:disabled {
        cursor: not-allowed;
        background-color: #f1f5f9;
        color: #64748b;
    }

    textarea.form-input {
        min-height: 100px;
    }

    @media (max-width: 360px) {
        .form-input {
            font-size: 13px;
            padding-left: 10px;
            padding-right: 10px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .form-input {
            transition: none;
        }
    }
</style>

{{-- Auto Fill & Validation --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const requestSelect = document.getElementById('maintenance_request_id');
    const equipmentSelect = document.getElementById('equipment_id');
    const prioritySelect = document.getElementById('priority');
    const descriptionField = document.getElementById('problem_description');
    const requestNotice = document.getElementById('requestNotice');

    const form = document.getElementById('workOrderForm');
    const submitButton = document.getElementById('submitButton');
    const submitButtonText = document.getElementById('submitButtonText');
    const plannedStart = document.getElementById('planned_start');
    const plannedEnd = document.getElementById('planned_end');

    // Isi otomatis saat Maintenance Request dipilih.
    function fillFromRequest() {
        const selected = requestSelect.options[requestSelect.selectedIndex];

        if (!requestSelect.value) {
            requestNotice.classList.add('hidden');
            return;
        }

        const equipmentId = selected.dataset.equipment;
        const priority = selected.dataset.priority;
        const description = selected.dataset.description;

        if (equipmentId) {
            equipmentSelect.value = equipmentId;
        }

        if (priority) {
            prioritySelect.value = priority;
        }

        // Isi deskripsi dari request. Pengguna tetap bisa mengeditnya.
        if (description !== undefined && descriptionField.value.trim() === '') {
            descriptionField.value = description;
        }

        requestNotice.classList.remove('hidden');
    }

    requestSelect.addEventListener('change', fillFromRequest);

    // Terapkan juga jika halaman dibuka dengan request yang sudah dipilih.
    if (requestSelect.value) {
        fillFromRequest();
    }

    // Planned End tidak boleh lebih awal dari Planned Start.
    function updateEndMinimum() {
        if (plannedStart.value) {
            plannedEnd.min = plannedStart.value;
        } else {
            plannedEnd.removeAttribute('min');
        }

        if (plannedStart.value && plannedEnd.value &&
            plannedEnd.value < plannedStart.value) {
            plannedEnd.setCustomValidity(
                'Planned End tidak boleh lebih awal dari Planned Start.'
            );
        } else {
            plannedEnd.setCustomValidity('');
        }
    }

    plannedStart.addEventListener('change', updateEndMinimum);
    plannedEnd.addEventListener('change', updateEndMinimum);
    updateEndMinimum();

    // Cegah klik submit berulang kali.
    form.addEventListener('submit', function (event) {
        updateEndMinimum();

        if (!form.checkValidity()) {
            event.preventDefault();
            form.reportValidity();
            return;
        }

        submitButton.disabled = true;
        submitButtonText.textContent = 'Creating...';
    });
});
</script>

@endsection
