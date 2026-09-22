@extends('layouts.app')

@section('title', 'Edit Work Order')

@section('content')

<div class="min-h-screen bg-slate-100 p-6">

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div class="flex items-center gap-4">

            {{-- Back --}}
            <a href="{{ route('work-orders.show', $workOrder) }}"
               class="w-10 h-10 rounded-xl
                      bg-white
                      border border-slate-200
                      text-slate-600
                      flex items-center justify-center
                      hover:bg-slate-50
                      hover:text-blue-600
                      transition"
               title="Kembali">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-5 h-5"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M15 19l-7-7 7-7" />

                </svg>

            </a>


            <div>

                <div class="flex items-center gap-3">

                    <h1 class="text-2xl font-bold text-slate-800">
                        Edit Work Order
                    </h1>

                    <span class="px-3 py-1
                                 bg-blue-100
                                 text-blue-700
                                 rounded-full
                                 text-xs
                                 font-semibold">

                        {{ $workOrder->wo_number }}

                    </span>

                </div>

                <p class="text-sm text-slate-500 mt-1">
                    Perbarui informasi dan status pekerjaan maintenance
                </p>

            </div>

        </div>

    </div>


    {{-- =========================================================
        VALIDATION ERROR
    ========================================================== --}}
    @if($errors->any())

        <div class="mb-6
                    bg-red-50
                    border border-red-200
                    rounded-xl
                    p-4">

            <div class="flex items-start gap-3">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-5 h-5 text-red-600 flex-shrink-0"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 8v4m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z" />

                </svg>

                <div>

                    <p class="text-sm font-semibold text-red-700">
                        Terdapat kesalahan pada form.
                    </p>

                    <ul class="mt-2 text-sm text-red-600 list-disc list-inside">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    {{-- =========================================================
        WARNING
    ========================================================== --}}
    @if(session('warning'))

        <div class="mb-6
                    bg-amber-50
                    border border-amber-200
                    rounded-xl
                    p-4">

            <div class="flex items-start gap-3">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-5 h-5 text-amber-600 flex-shrink-0"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z" />

                </svg>

                <p class="text-sm font-medium text-amber-700">
                    {{ session('warning') }}
                </p>

            </div>

        </div>

    @endif


    {{-- =========================================================
        FORM
    ========================================================== --}}
    <form method="POST"
          action="{{ route('work-orders.update', $workOrder) }}">

        @csrf
        @method('PUT')


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


            {{-- =================================================
                LEFT
            ================================================== --}}
            <div class="lg:col-span-2 space-y-6">


                {{-- =============================================
                    BASIC INFORMATION
                ============================================== --}}
                <div class="bg-white rounded-2xl
                            border border-slate-200
                            shadow-sm">

                    <div class="px-5 py-4
                                border-b border-slate-200">

                        <h2 class="text-lg font-semibold text-slate-800">
                            Informasi Work Order
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Informasi utama pekerjaan maintenance.
                        </p>

                    </div>


                    <div class="p-5 space-y-5">

                        {{-- WO Number --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                WO Number
                            </label>

                            <input type="text"
                                   value="{{ $workOrder->wo_number }}"
                                   readonly
                                   class="w-full px-4 py-2.5
                                          bg-slate-100
                                          border border-slate-200
                                          rounded-xl
                                          text-sm
                                          text-slate-500
                                          cursor-not-allowed">

                            <p class="text-xs text-slate-400 mt-1">
                                Nomor Work Order tidak dapat diubah.
                            </p>

                        </div>


                        {{-- Maintenance Request --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Maintenance Request
                            </label>

                            <select name="maintenance_request_id"
                                    id="maintenance_request_id"
                                    class="w-full px-4 py-2.5
                                           border border-slate-300
                                           rounded-xl
                                           text-sm
                                           bg-white
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-blue-500">

                                <option value="">
                                    -- Tidak terkait Maintenance Request --
                                </option>

                                @foreach($maintenanceRequests as $maintenanceRequest)

                                    <option value="{{ $maintenanceRequest->id }}"
                                            data-equipment="{{ $maintenanceRequest->equipment_id }}"
                                            data-priority="{{ $maintenanceRequest->priority }}"
                                            data-description="{{ $maintenanceRequest->description }}"
                                            {{ old(
                                                'maintenance_request_id',
                                                $workOrder->maintenance_request_id
                                            ) == $maintenanceRequest->id ? 'selected' : '' }}>

                                        MR #{{ $maintenanceRequest->id }}

                                        @if($maintenanceRequest->equipment)
                                            - {{ $maintenanceRequest->equipment->name }}
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Equipment --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Equipment
                                <span class="text-red-500">*</span>
                            </label>

                            <select name="equipment_id"
                                    id="equipment_id"
                                    required
                                    class="w-full px-4 py-2.5
                                           border border-slate-300
                                           rounded-xl
                                           text-sm
                                           bg-white
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-blue-500">

                                <option value="">
                                    -- Pilih Equipment --
                                </option>

                                @foreach($equipment as $item)

                                    <option value="{{ $item->id }}"
                                        {{ old(
                                            'equipment_id',
                                            $workOrder->equipment_id
                                        ) == $item->id ? 'selected' : '' }}>

                                        {{ $item->equipment_code }}
                                        -
                                        {{ $item->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Technician --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Technician / Engineer
                            </label>

                            <select name="technician_id"
                                    class="w-full px-4 py-2.5
                                           border border-slate-300
                                           rounded-xl
                                           text-sm
                                           bg-white
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-blue-500">

                                <option value="">
                                    -- Belum ditugaskan --
                                </option>

                                @foreach($technicians as $technician)

                                    <option value="{{ $technician->id }}"
                                        {{ old(
                                            'technician_id',
                                            $workOrder->technician_id
                                        ) == $technician->id ? 'selected' : '' }}>

                                        {{ $technician->username }}

                                        @if($technician->role)
                                            ({{ $technician->role }})
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Type + Priority --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- Type --}}
                            <div>

                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Maintenance Type
                                    <span class="text-red-500">*</span>
                                </label>

                                <select name="maintenance_type"
                                        required
                                        class="w-full px-4 py-2.5
                                               border border-slate-300
                                               rounded-xl
                                               text-sm
                                               bg-white
                                               focus:outline-none
                                               focus:ring-2
                                               focus:ring-blue-500">

                                    <option value="CORRECTIVE"
                                        {{ old(
                                            'maintenance_type',
                                            $workOrder->maintenance_type
                                        ) === 'CORRECTIVE' ? 'selected' : '' }}>
                                        Corrective
                                    </option>

                                    <option value="PREVENTIVE"
                                        {{ old(
                                            'maintenance_type',
                                            $workOrder->maintenance_type
                                        ) === 'PREVENTIVE' ? 'selected' : '' }}>
                                        Preventive
                                    </option>

                                    <option value="INSPECTION"
                                        {{ old(
                                            'maintenance_type',
                                            $workOrder->maintenance_type
                                        ) === 'INSPECTION' ? 'selected' : '' }}>
                                        Inspection
                                    </option>

                                </select>

                            </div>


                            {{-- Priority --}}
                            <div>

                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Priority
                                    <span class="text-red-500">*</span>
                                </label>

                                <select name="priority"
                                        id="priority"
                                        required
                                        class="w-full px-4 py-2.5
                                               border border-slate-300
                                               rounded-xl
                                               text-sm
                                               bg-white
                                               focus:outline-none
                                               focus:ring-2
                                               focus:ring-blue-500">

                                    <option value="LOW"
                                        {{ old(
                                            'priority',
                                            $workOrder->priority
                                        ) === 'LOW' ? 'selected' : '' }}>
                                        Low
                                    </option>

                                    <option value="MEDIUM"
                                        {{ old(
                                            'priority',
                                            $workOrder->priority
                                        ) === 'MEDIUM' ? 'selected' : '' }}>
                                        Medium
                                    </option>

                                    <option value="HIGH"
                                        {{ old(
                                            'priority',
                                            $workOrder->priority
                                        ) === 'HIGH' ? 'selected' : '' }}>
                                        High
                                    </option>

                                    <option value="CRITICAL"
                                        {{ old(
                                            'priority',
                                            $workOrder->priority
                                        ) === 'CRITICAL' ? 'selected' : '' }}>
                                        Critical
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =============================================
                    PROBLEM & ACTION
                ============================================== --}}
                <div class="bg-white rounded-2xl
                            border border-slate-200
                            shadow-sm">

                    <div class="px-5 py-4
                                border-b border-slate-200">

                        <h2 class="text-lg font-semibold text-slate-800">
                            Problem & Action
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Informasi masalah dan tindakan maintenance.
                        </p>

                    </div>


                    <div class="p-5 space-y-5">

                        {{-- Problem --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Problem Description
                            </label>

                            <textarea name="problem_description"
                                      rows="4"
                                      placeholder="Jelaskan masalah yang ditemukan..."
                                      class="w-full px-4 py-3
                                             border border-slate-300
                                             rounded-xl
                                             text-sm
                                             resize-none
                                             focus:outline-none
                                             focus:ring-2
                                             focus:ring-blue-500">{{ old('problem_description', $workOrder->problem_description) }}</textarea>

                        </div>


                        {{-- Root Cause --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Root Cause
                            </label>

                            <textarea name="root_cause"
                                      rows="4"
                                      placeholder="Jelaskan penyebab utama masalah..."
                                      class="w-full px-4 py-3
                                             border border-slate-300
                                             rounded-xl
                                             text-sm
                                             resize-none
                                             focus:outline-none
                                             focus:ring-2
                                             focus:ring-blue-500">{{ old('root_cause', $workOrder->root_cause) }}</textarea>

                        </div>


                        {{-- Corrective Action --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Corrective Action
                            </label>

                            <textarea name="corrective_action"
                                      rows="4"
                                      placeholder="Jelaskan tindakan perbaikan yang dilakukan..."
                                      class="w-full px-4 py-3
                                             border border-slate-300
                                             rounded-xl
                                             text-sm
                                             resize-none
                                             focus:outline-none
                                             focus:ring-2
                                             focus:ring-blue-500">{{ old('corrective_action', $workOrder->corrective_action) }}</textarea>

                        </div>


                        {{-- Completion Notes --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Completion Notes
                            </label>

                            <textarea name="completion_notes"
                                      rows="4"
                                      placeholder="Catatan penyelesaian pekerjaan..."
                                      class="w-full px-4 py-3
                                             border border-slate-300
                                             rounded-xl
                                             text-sm
                                             resize-none
                                             focus:outline-none
                                             focus:ring-2
                                             focus:ring-blue-500">{{ old('completion_notes', $workOrder->completion_notes) }}</textarea>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
                RIGHT
            ================================================== --}}
            <div class="space-y-6">


                {{-- =============================================
                    STATUS
                ============================================== --}}
                <div class="bg-white rounded-2xl
                            border border-slate-200
                            shadow-sm">

                    <div class="px-5 py-4
                                border-b border-slate-200">

                        <h2 class="text-lg font-semibold text-slate-800">
                            Status Work Order
                        </h2>

                    </div>


                    <div class="p-5">

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Status
                            <span class="text-red-500">*</span>
                        </label>

                        <select name="status"
                                id="status"
                                required
                                class="w-full px-4 py-2.5
                                       border border-slate-300
                                       rounded-xl
                                       text-sm
                                       bg-white
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-blue-500">

                            <option value="OPEN"
                                {{ old(
                                    'status',
                                    $workOrder->status
                                ) === 'OPEN' ? 'selected' : '' }}>
                                Open
                            </option>

                            <option value="ASSIGNED"
                                {{ old(
                                    'status',
                                    $workOrder->status
                                ) === 'ASSIGNED' ? 'selected' : '' }}>
                                Assigned
                            </option>

                            <option value="IN_PROGRESS"
                                {{ old(
                                    'status',
                                    $workOrder->status
                                ) === 'IN_PROGRESS' ? 'selected' : '' }}>
                                In Progress
                            </option>

                            <option value="ON_HOLD"
                                {{ old(
                                    'status',
                                    $workOrder->status
                                ) === 'ON_HOLD' ? 'selected' : '' }}>
                                On Hold
                            </option>

                            <option value="COMPLETED"
                                {{ old(
                                    'status',
                                    $workOrder->status
                                ) === 'COMPLETED' ? 'selected' : '' }}>
                                Completed
                            </option>

                            <option value="CANCELLED"
                                {{ old(
                                    'status',
                                    $workOrder->status
                                ) === 'CANCELLED' ? 'selected' : '' }}>
                                Cancelled
                            </option>

                        </select>

                    </div>

                </div>


                {{-- =============================================
                    SCHEDULE
                ============================================== --}}
                <div class="bg-white rounded-2xl
                            border border-slate-200
                            shadow-sm">

                    <div class="px-5 py-4
                                border-b border-slate-200">

                        <h2 class="text-lg font-semibold text-slate-800">
                            Schedule
                        </h2>

                    </div>


                    <div class="p-5 space-y-5">

                        {{-- Planned Start --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Planned Start
                            </label>

                            <input type="datetime-local"
                                   name="planned_start"
                                   value="{{ old(
                                       'planned_start',
                                       $workOrder->planned_start
                                           ? $workOrder->planned_start->format('Y-m-d\TH:i')
                                           : ''
                                   ) }}"
                                   class="w-full px-4 py-2.5
                                          border border-slate-300
                                          rounded-xl
                                          text-sm
                                          focus:outline-none
                                          focus:ring-2
                                          focus:ring-blue-500">

                        </div>


                        {{-- Planned End --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Planned End
                            </label>

                            <input type="datetime-local"
                                   name="planned_end"
                                   value="{{ old(
                                       'planned_end',
                                       $workOrder->planned_end
                                           ? $workOrder->planned_end->format('Y-m-d\TH:i')
                                           : ''
                                   ) }}"
                                   class="w-full px-4 py-2.5
                                          border border-slate-300
                                          rounded-xl
                                          text-sm
                                          focus:outline-none
                                          focus:ring-2
                                          focus:ring-blue-500">

                        </div>


                        <div class="border-t border-slate-100"></div>


                        {{-- Actual Start --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Actual Start
                            </label>

                            <input type="datetime-local"
                                   name="actual_start"
                                   value="{{ old(
                                       'actual_start',
                                       $workOrder->actual_start
                                           ? $workOrder->actual_start->format('Y-m-d\TH:i')
                                           : ''
                                   ) }}"
                                   class="w-full px-4 py-2.5
                                          border border-slate-300
                                          rounded-xl
                                          text-sm
                                          focus:outline-none
                                          focus:ring-2
                                          focus:ring-blue-500">

                        </div>


                        {{-- Actual End --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Actual End
                            </label>

                            <input type="datetime-local"
                                   name="actual_end"
                                   value="{{ old(
                                       'actual_end',
                                       $workOrder->actual_end
                                           ? $workOrder->actual_end->format('Y-m-d\TH:i')
                                           : ''
                                   ) }}"
                                   class="w-full px-4 py-2.5
                                          border border-slate-300
                                          rounded-xl
                                          text-sm
                                          focus:outline-none
                                          focus:ring-2
                                          focus:ring-blue-500">

                        </div>

                    </div>

                </div>


                {{-- =============================================
                    INFORMATION
                ============================================== --}}
                <div class="bg-blue-50
                            border border-blue-100
                            rounded-2xl
                            p-5">

                    <div class="flex items-start gap-3">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z" />

                        </svg>

                        <div>

                            <p class="text-sm font-semibold text-blue-800">
                                Informasi
                            </p>

                            <p class="text-xs text-blue-700 mt-1 leading-relaxed">
                                Perubahan Work Order akan dicatat ke Activity Log.
                                Nomor Work Order tidak dapat diubah.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            FORM ACTION
        ====================================================== --}}
        <div class="mt-6
                    bg-white
                    border border-slate-200
                    rounded-2xl
                    shadow-sm
                    p-5">

            <div class="flex flex-col-reverse sm:flex-row
                        sm:items-center
                        sm:justify-between
                        gap-3">

                <a href="{{ route('work-orders.show', $workOrder) }}"
                   class="inline-flex items-center justify-center
                          gap-2
                          px-5 py-2.5
                          border border-slate-300
                          text-slate-600
                          text-sm font-semibold
                          rounded-xl
                          hover:bg-slate-50
                          transition">

                    Batal

                </a>


                <button type="submit"
                        class="inline-flex items-center justify-center
                               gap-2
                               px-6 py-2.5
                               bg-blue-600
                               hover:bg-blue-700
                               text-white
                               text-sm font-semibold
                               rounded-xl
                               shadow-sm
                               transition">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M5 13l4 4L19 7" />

                    </svg>

                    Simpan Perubahan

                </button>

            </div>

        </div>

    </form>

</div>


{{-- =============================================================
    AUTO FILL DARI MAINTENANCE REQUEST
============================================================= --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const requestSelect = document.getElementById('maintenance_request_id');
    const equipmentSelect = document.getElementById('equipment_id');
    const prioritySelect = document.getElementById('priority');
    const problemField = document.querySelector(
        'textarea[name="problem_description"]'
    );


    if (!requestSelect) {
        return;
    }


    requestSelect.addEventListener('change', function () {

        const selectedOption =
            this.options[this.selectedIndex];


        if (!selectedOption || !selectedOption.value) {
            return;
        }


        const equipmentId =
            selectedOption.dataset.equipment;

        const priority =
            selectedOption.dataset.priority;

        const description =
            selectedOption.dataset.description;


        /*
        |--------------------------------------------------------------------------
        | Equipment
        |--------------------------------------------------------------------------
        */

        if (equipmentId && equipmentSelect) {

            equipmentSelect.value = equipmentId;

        }


        /*
        |--------------------------------------------------------------------------
        | Priority
        |--------------------------------------------------------------------------
        */

        if (priority && prioritySelect) {

            prioritySelect.value = priority;

        }


        /*
        |--------------------------------------------------------------------------
        | Problem Description
        |--------------------------------------------------------------------------
        */

        if (
            description &&
            problemField &&
            !problemField.value.trim()
        ) {

            problemField.value = description;

        }

    });

});

</script>

@endsection