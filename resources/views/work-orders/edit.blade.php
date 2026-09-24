@extends('layouts.app')

@section('title', 'Edit Work Order')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div>
            <div class="flex items-center gap-3 mb-2">

                <a href="{{ route('work-orders.show', $workOrder) }}"
                   class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-800 transition">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-4 h-4"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M15 19l-7-7 7-7"/>
                    </svg>

                    Kembali ke Detail
                </a>

            </div>

            <h1 class="text-2xl font-bold text-slate-800">
                Edit Work Order
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Perbarui informasi Work Order
                <span class="font-semibold text-slate-700">
                    {{ $workOrder->wo_number }}
                </span>
            </p>
        </div>

        <div class="flex items-center gap-3">

            {{-- STATUS BADGE --}}
            @php
                $statusClasses = [
                    'OPEN' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'ASSIGNED' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                    'IN_PROGRESS' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'ON_HOLD' => 'bg-orange-100 text-orange-700 border-orange-200',
                    'COMPLETED' => 'bg-green-100 text-green-700 border-green-200',
                    'CANCELLED' => 'bg-red-100 text-red-700 border-red-200',
                ];

                $statusLabels = [
                    'OPEN' => 'Open',
                    'ASSIGNED' => 'Assigned',
                    'IN_PROGRESS' => 'In Progress',
                    'ON_HOLD' => 'On Hold',
                    'COMPLETED' => 'Completed',
                    'CANCELLED' => 'Cancelled',
                ];

                $statusClass = $statusClasses[$workOrder->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                $statusLabel = $statusLabels[$workOrder->status] ?? $workOrder->status;
            @endphp

            <span class="inline-flex items-center px-3 py-2 rounded-lg border text-sm font-semibold {{ $statusClass }}">
                {{ $statusLabel }}
            </span>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- VALIDATION ERROR --}}
    {{-- ========================================================= --}}
    @if ($errors->any())

        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">

            <div class="flex items-start gap-3">

                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5 text-red-600"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 9v2m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 2.64h15.64a2 2 0 001.71-2.64l-7.82-13a2 2 0 00-3.42 0z"/>

                    </svg>
                </div>

                <div>

                    <h3 class="font-semibold text-red-800">
                        Terdapat kesalahan pada form
                    </h3>

                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">

                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- SUCCESS MESSAGE --}}
    {{-- ========================================================= --}}
    @if (session('success'))

        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4">

            <div class="flex items-center gap-3">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-5 h-5 text-green-600"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M5 13l4 4L19 7"/>

                </svg>

                <p class="text-sm font-medium text-green-800">
                    {{ session('success') }}
                </p>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- WARNING --}}
    {{-- ========================================================= --}}
    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">

        <div class="flex items-start gap-3">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5 text-amber-600 mt-0.5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 9v2m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 2.64h15.64a2 2 0 001.71-2.64l-7.82-13a2 2 0 00-3.42 0z"/>

            </svg>

            <div>

                <p class="font-semibold text-amber-800">
                    Perhatian
                </p>

                <p class="text-sm text-amber-700 mt-1">
                    Status Work Order tidak diubah dari halaman ini.
                    Gunakan tombol workflow pada halaman detail untuk melakukan
                    Assign, Start, Hold, Resume, Complete, atau Cancel.
                </p>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- FORM --}}
    {{-- ========================================================= --}}
    <form action="{{ route('work-orders.update', $workOrder) }}"
          method="POST">

        @csrf
        @method('PUT')


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


            {{-- ================================================= --}}
            {{-- LEFT CONTENT --}}
            {{-- ================================================= --}}
            <div class="lg:col-span-2 space-y-6">


                {{-- ============================================= --}}
                {{-- BASIC INFORMATION --}}
                {{-- ============================================= --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200">

                    <div class="px-6 py-5 border-b border-slate-200">

                        <h2 class="text-lg font-semibold text-slate-800">
                            Informasi Work Order
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Informasi utama dari Work Order.
                        </p>

                    </div>


                    <div class="p-6 space-y-5">


                        {{-- WO NUMBER --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                WO Number
                            </label>

                            <input type="text"
                                   value="{{ $workOrder->wo_number }}"
                                   readonly
                                   class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-600 cursor-not-allowed">

                            <p class="text-xs text-slate-500 mt-1">
                                WO Number tidak dapat diubah.
                            </p>

                        </div>


                        {{-- MAINTENANCE REQUEST --}}
                        <div>

                            <label for="maintenance_request_id"
                                   class="block text-sm font-medium text-slate-700 mb-2">

                                Maintenance Request

                            </label>

                            <select name="maintenance_request_id"
                                    id="maintenance_request_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                <option value="">
                                    -- Manual Work Order --
                                </option>

                                @foreach ($maintenanceRequests as $request)

                                    <option value="{{ $request->id }}"
                                            data-equipment="{{ $request->equipment_id }}"
                                            data-priority="{{ $request->priority }}"
                                            data-description="{{ $request->description }}"
                                            {{ old('maintenance_request_id', $workOrder->maintenance_request_id) == $request->id ? 'selected' : '' }}>

                                        MR #{{ $request->id }}

                                        @if ($request->equipment)
                                            - {{ $request->equipment->name }}
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                            <p class="text-xs text-slate-500 mt-1">
                                Hanya Maintenance Request yang sudah disetujui yang dapat digunakan.
                            </p>

                        </div>


                        {{-- EQUIPMENT --}}
                        <div>

                            <label for="equipment_id"
                                   class="block text-sm font-medium text-slate-700 mb-2">

                                Equipment
                                <span class="text-red-500">*</span>

                            </label>

                            <select name="equipment_id"
                                    id="equipment_id"
                                    required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                <option value="">
                                    -- Pilih Equipment --
                                </option>

                                @foreach ($equipment as $item)

                                    <option value="{{ $item->id }}"
                                            {{ old('equipment_id', $workOrder->equipment_id) == $item->id ? 'selected' : '' }}>

                                        {{ $item->name }}

                                        @if ($item->code)
                                            - {{ $item->code }}
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                            <p id="equipment-help"
                               class="text-xs text-slate-500 mt-1">

                                Equipment akan otomatis mengikuti Maintenance Request jika MR dipilih.

                            </p>

                        </div>


                        {{-- TECHNICIAN --}}
                        <div>

                            <label for="technician_id"
                                   class="block text-sm font-medium text-slate-700 mb-2">

                                Technician

                            </label>

                            <select name="technician_id"
                                    id="technician_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                <option value="">
                                    -- Belum Ditentukan --
                                </option>

                                @foreach ($technicians as $technician)

                                    <option value="{{ $technician->id }}"
                                            {{ old('technician_id', $workOrder->technician_id) == $technician->id ? 'selected' : '' }}>

                                        {{ $technician->name }}

                                        @if ($technician->role)
                                            - {{ $technician->role }}
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                            <p class="text-xs text-slate-500 mt-1">
                                Technician juga dapat ditentukan melalui proses Assign pada halaman detail.
                            </p>

                        </div>


                        {{-- MAINTENANCE TYPE --}}
                        <div>

                            <label for="maintenance_type"
                                   class="block text-sm font-medium text-slate-700 mb-2">

                                Maintenance Type
                                <span class="text-red-500">*</span>

                            </label>

                            <select name="maintenance_type"
                                    id="maintenance_type"
                                    required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                <option value="CORRECTIVE"
                                    {{ old('maintenance_type', $workOrder->maintenance_type) === 'CORRECTIVE' ? 'selected' : '' }}>
                                    Corrective
                                </option>

                                <option value="PREVENTIVE"
                                    {{ old('maintenance_type', $workOrder->maintenance_type) === 'PREVENTIVE' ? 'selected' : '' }}>
                                    Preventive
                                </option>

                                <option value="INSPECTION"
                                    {{ old('maintenance_type', $workOrder->maintenance_type) === 'INSPECTION' ? 'selected' : '' }}>
                                    Inspection
                                </option>

                                <option value="EMERGENCY"
                                    {{ old('maintenance_type', $workOrder->maintenance_type) === 'EMERGENCY' ? 'selected' : '' }}>
                                    Emergency
                                </option>

                            </select>

                        </div>


                        {{-- PRIORITY --}}
                        <div>

                            <label for="priority"
                                   class="block text-sm font-medium text-slate-700 mb-2">

                                Priority
                                <span class="text-red-500">*</span>

                            </label>

                            <select name="priority"
                                    id="priority"
                                    required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                <option value="LOW"
                                    {{ old('priority', $workOrder->priority) === 'LOW' ? 'selected' : '' }}>
                                    Low
                                </option>

                                <option value="MEDIUM"
                                    {{ old('priority', $workOrder->priority) === 'MEDIUM' ? 'selected' : '' }}>
                                    Medium
                                </option>

                                <option value="HIGH"
                                    {{ old('priority', $workOrder->priority) === 'HIGH' ? 'selected' : '' }}>
                                    High
                                </option>

                                <option value="CRITICAL"
                                    {{ old('priority', $workOrder->priority) === 'CRITICAL' ? 'selected' : '' }}>
                                    Critical
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                {{-- ============================================= --}}
                {{-- PROBLEM & ACTION --}}
                {{-- ============================================= --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200">

                    <div class="px-6 py-5 border-b border-slate-200">

                        <h2 class="text-lg font-semibold text-slate-800">
                            Problem & Action
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Informasi masalah, analisis penyebab, dan tindakan maintenance.
                        </p>

                    </div>


                    <div class="p-6 space-y-5">


                        {{-- PROBLEM DESCRIPTION --}}
                        <div>

                            <label for="problem_description"
                                   class="block text-sm font-medium text-slate-700 mb-2">

                                Problem Description

                            </label>

                            <textarea name="problem_description"
                                      id="problem_description"
                                      rows="5"
                                      class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                      placeholder="Jelaskan masalah atau keluhan equipment...">{{ old('problem_description', $workOrder->problem_description) }}</textarea>

                        </div>


                        {{-- ROOT CAUSE --}}
                        <div>

                            <label for="root_cause"
                                   class="block text-sm font-medium text-slate-700 mb-2">

                                Root Cause

                            </label>

                            <textarea name="root_cause"
                                      id="root_cause"
                                      rows="5"
                                      class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                      placeholder="Jelaskan penyebab utama kerusakan...">{{ old('root_cause', $workOrder->root_cause) }}</textarea>

                        </div>


                        {{-- CORRECTIVE ACTION --}}
                        <div>

                            <label for="corrective_action"
                                   class="block text-sm font-medium text-slate-700 mb-2">

                                Corrective Action

                            </label>

                            <textarea name="corrective_action"
                                      id="corrective_action"
                                      rows="5"
                                      class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                      placeholder="Jelaskan tindakan perbaikan yang dilakukan...">{{ old('corrective_action', $workOrder->corrective_action) }}</textarea>

                        </div>


                        {{-- COMPLETION NOTES --}}
                        <div>

                            <label for="completion_notes"
                                   class="block text-sm font-medium text-slate-700 mb-2">

                                Completion Notes

                            </label>

                            <textarea name="completion_notes"
                                      id="completion_notes"
                                      rows="4"
                                      class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                      placeholder="Catatan penyelesaian Work Order...">{{ old('completion_notes', $workOrder->completion_notes) }}</textarea>

                            <p class="text-xs text-slate-500 mt-1">
                                Catatan ini dapat diperbarui selama proses Work Order.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- RIGHT SIDEBAR --}}
            {{-- ================================================= --}}
            <div class="space-y-6">


                {{-- ============================================= --}}
                {{-- WORK ORDER STATUS --}}
                {{-- ============================================= --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200">

                    <div class="px-6 py-5 border-b border-slate-200">

                        <h2 class="text-lg font-semibold text-slate-800">
                            Status Work Order
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Status dikontrol melalui workflow.
                        </p>

                    </div>


                    <div class="p-6">

                        <div class="rounded-xl border {{ $statusClass }} p-4">

                            <div class="flex items-center justify-between">

                                <span class="text-sm font-medium">
                                    Status Saat Ini
                                </span>

                                <span class="font-bold">
                                    {{ $statusLabel }}
                                </span>

                            </div>

                        </div>


                        <div class="mt-4 rounded-xl bg-slate-50 border border-slate-200 p-4">

                            <p class="text-xs leading-5 text-slate-600">

                                Status Work Order tidak dapat diubah langsung dari halaman Edit.
                                Gunakan halaman Detail Work Order untuk menjalankan proses:

                            </p>

                            <ul class="mt-3 text-xs text-slate-600 space-y-2">

                                <li class="flex gap-2">
                                    <span class="font-semibold">1.</span>
                                    <span>Assign Technician</span>
                                </li>

                                <li class="flex gap-2">
                                    <span class="font-semibold">2.</span>
                                    <span>Start Work Order</span>
                                </li>

                                <li class="flex gap-2">
                                    <span class="font-semibold">3.</span>
                                    <span>Hold / Resume</span>
                                </li>

                                <li class="flex gap-2">
                                    <span class="font-semibold">4.</span>
                                    <span>Complete Work Order</span>
                                </li>

                            </ul>

                        </div>

                    </div>

                </div>


                {{-- ============================================= --}}
                {{-- SCHEDULE --}}
                {{-- ============================================= --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200">

                    <div class="px-6 py-5 border-b border-slate-200">

                        <h2 class="text-lg font-semibold text-slate-800">
                            Schedule
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Rencana pelaksanaan Work Order.
                        </p>

                    </div>


                    <div class="p-6 space-y-5">


                        {{-- PLANNED START --}}
                        <div>

                            <label for="planned_start"
                                   class="block text-sm font-medium text-slate-700 mb-2">

                                Planned Start

                            </label>

                            <input type="datetime-local"
                                   name="planned_start"
                                   id="planned_start"
                                   value="{{ old('planned_start', $workOrder->planned_start ? $workOrder->planned_start->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                        </div>


                        {{-- PLANNED END --}}
                        <div>

                            <label for="planned_end"
                                   class="block text-sm font-medium text-slate-700 mb-2">

                                Planned End

                            </label>

                            <input type="datetime-local"
                                   name="planned_end"
                                   id="planned_end"
                                   value="{{ old('planned_end', $workOrder->planned_end ? $workOrder->planned_end->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                        </div>


                        {{-- ACTUAL START --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Actual Start
                            </label>

                            <div class="relative">

                                <input type="text"
                                       value="{{ $workOrder->actual_start ? $workOrder->actual_start->format('d M Y H:i') : '-' }}"
                                       readonly
                                       class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-600 cursor-not-allowed">

                            </div>

                            <p class="text-xs text-slate-500 mt-1">
                                Diisi otomatis saat Work Order dimulai.
                            </p>

                        </div>


                        {{-- ACTUAL END --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Actual End
                            </label>

                            <div class="relative">

                                <input type="text"
                                       value="{{ $workOrder->actual_end ? $workOrder->actual_end->format('d M Y H:i') : '-' }}"
                                       readonly
                                       class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-600 cursor-not-allowed">

                            </div>

                            <p class="text-xs text-slate-500 mt-1">
                                Diisi otomatis saat Work Order diselesaikan.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- ============================================= --}}
                {{-- INFORMATION --}}
                {{-- ============================================= --}}
                <div class="bg-blue-50 rounded-2xl border border-blue-200 p-5">

                    <div class="flex items-start gap-3">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>

                        </svg>

                        <div>

                            <h3 class="font-semibold text-blue-800">
                                Informasi
                            </h3>

                            <p class="text-sm text-blue-700 mt-1 leading-6">

                                Perubahan pada Work Order akan tercatat pada Activity Log.
                                WO Number, Actual Start, Actual End, dan Status dikelola
                                melalui workflow Work Order.

                            </p>

                        </div>

                    </div>

                </div>


                {{-- ============================================= --}}
                {{-- ACTION BUTTON --}}
                {{-- ============================================= --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">

                    <div class="flex flex-col gap-3">

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-5 h-5"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M5 13l4 4L19 7"/>

                            </svg>

                            Simpan Perubahan

                        </button>


                        <a href="{{ route('work-orders.show', $workOrder) }}"
                           class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">

                            Batal

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>


{{-- ============================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ============================================================= --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const maintenanceRequest =
        document.getElementById('maintenance_request_id');

    const equipment =
        document.getElementById('equipment_id');

    const priority =
        document.getElementById('priority');

    const problemDescription =
        document.getElementById('problem_description');

    const equipmentHelp =
        document.getElementById('equipment-help');


    if (!maintenanceRequest) {
        return;
    }


    function syncMaintenanceRequest() {

        const selected =
            maintenanceRequest.options[
                maintenanceRequest.selectedIndex
            ];


        if (!selected || !selected.value) {

            if (equipmentHelp) {

                equipmentHelp.textContent =
                    'Equipment dapat dipilih secara manual jika Work Order tidak berasal dari Maintenance Request.';

            }

            if (equipment) {

                equipment.disabled = false;

                equipment.classList.remove(
                    'bg-slate-100',
                    'cursor-not-allowed'
                );

                equipment.classList.add('bg-white');

            }

            return;
        }


        const equipmentId =
            selected.dataset.equipment;

        const selectedPriority =
            selected.dataset.priority;

        const description =
            selected.dataset.description;


        /*
         * AUTO SELECT EQUIPMENT
         */
        if (equipment && equipmentId) {

            equipment.value = equipmentId;

            equipment.disabled = true;

            equipment.classList.remove('bg-white');

            equipment.classList.add(
                'bg-slate-100',
                'cursor-not-allowed'
            );

            if (equipmentHelp) {

                equipmentHelp.textContent =
                    'Equipment dikunci karena mengikuti Maintenance Request yang dipilih.';

            }

        }


        /*
         * AUTO SELECT PRIORITY
         */
        if (priority && selectedPriority) {

            const priorityOption =
                Array.from(priority.options).find(
                    option => option.value === selectedPriority
                );

            if (priorityOption) {

                priority.value = selectedPriority;

            }

        }


        /*
         * AUTO FILL PROBLEM DESCRIPTION
         */
        if (
            problemDescription &&
            description &&
            !problemDescription.value.trim()
        ) {

            problemDescription.value = description;

        }

    }


    maintenanceRequest.addEventListener(
        'change',
        syncMaintenanceRequest
    );


    /*
     * Jalankan ketika halaman pertama kali dibuka.
     */
    syncMaintenanceRequest();

});

</script>

@endsection