@extends('layouts.app')

@section('title', 'Detail Work Order')

@section('content')

<div class="min-h-screen bg-slate-100 p-6">

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div class="flex items-center gap-4">

            {{-- Back --}}
            <a href="{{ route('work-orders.index') }}"
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
                        {{ $workOrder->wo_number }}
                    </h1>

                    @php
                        $statusClasses = [
                            'OPEN' => 'bg-slate-100 text-slate-700',
                            'ASSIGNED' => 'bg-amber-100 text-amber-700',
                            'IN_PROGRESS' => 'bg-blue-100 text-blue-700',
                            'ON_HOLD' => 'bg-orange-100 text-orange-700',
                            'COMPLETED' => 'bg-emerald-100 text-emerald-700',
                            'CANCELLED' => 'bg-red-100 text-red-700',
                        ];

                        $statusLabels = [
                            'OPEN' => 'Open',
                            'ASSIGNED' => 'Assigned',
                            'IN_PROGRESS' => 'In Progress',
                            'ON_HOLD' => 'On Hold',
                            'COMPLETED' => 'Completed',
                            'CANCELLED' => 'Cancelled',
                        ];

                        $statusClass =
                            $statusClasses[$workOrder->status]
                            ?? 'bg-slate-100 text-slate-600';

                        $statusLabel =
                            $statusLabels[$workOrder->status]
                            ?? $workOrder->status;
                    @endphp

                    <span class="inline-flex items-center gap-1.5
                                 px-3 py-1
                                 rounded-full
                                 text-xs font-semibold
                                 {{ $statusClass }}">

                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                        {{ $statusLabel }}

                    </span>

                </div>

                <p class="text-sm text-slate-500 mt-1">
                    Detail pekerjaan maintenance equipment
                </p>

            </div>

        </div>


        {{-- Actions --}}
        <div class="flex items-center gap-2">

            <a href="{{ route('work-orders.edit', $workOrder) }}"
               class="inline-flex items-center gap-2
                      px-4 py-2.5
                      bg-amber-500
                      hover:bg-amber-600
                      text-white
                      text-sm font-semibold
                      rounded-xl
                      transition">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-4 h-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />

                </svg>

                Edit

            </a>

        </div>

    </div>


    {{-- =========================================================
        FLASH MESSAGE
    ========================================================== --}}
    @if(session('success'))

        <div class="mb-6 flex items-start gap-3
                    bg-emerald-50
                    border border-emerald-200
                    text-emerald-700
                    rounded-xl
                    p-4">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5 mt-0.5 flex-shrink-0"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />

            </svg>

            <span class="text-sm font-medium">
                {{ session('success') }}
            </span>

        </div>

    @endif

    {{-- ==========================================
     WORK ORDER ACTION PANEL
========================================== --}}

<div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">
                Workflow Work Order
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Kelola proses pengerjaan Work Order sesuai statusnya.
            </p>
        </div>

        <span class="rounded-full px-3 py-1 text-xs font-bold
            @if($workOrder->status === 'OPEN')
                bg-slate-100 text-slate-700
            @elseif($workOrder->status === 'ASSIGNED')
                bg-blue-100 text-blue-700
            @elseif($workOrder->status === 'IN_PROGRESS')
                bg-yellow-100 text-yellow-700
            @elseif($workOrder->status === 'ON_HOLD')
                bg-orange-100 text-orange-700
            @elseif($workOrder->status === 'COMPLETED')
                bg-green-100 text-green-700
            @elseif($workOrder->status === 'CANCELLED')
                bg-red-100 text-red-700
            @endif
        ">
            {{ str_replace('_', ' ', $workOrder->status) }}
        </span>
    </div>


    {{-- OPEN --}}
    @if($workOrder->status === 'OPEN')

        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">

            <p class="mb-4 text-sm text-blue-800">
                Work Order masih OPEN. Pilih Technician/Engineer untuk
                melanjutkan proses assignment.
            </p>

            <form
                action="{{ route('work-orders.assign', $workOrder) }}"
                method="POST"
                class="flex flex-col gap-3 sm:flex-row"
            >

                @csrf

                <select
                    name="technician_id"
                    required
                    class="flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-sm
                           focus:border-blue-500 focus:ring-blue-500"
                >

                    <option value="">
                        -- Pilih Technician / Engineer --
                    </option>

                    @foreach($technicians as $technician)

                        <option value="{{ $technician->id }}">
                            {{ $technician->username }}
                            ({{ $technician->role }})
                        </option>

                    @endforeach

                </select>

                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white
                           transition hover:bg-blue-700"
                >
                    Assign Technician
                </button>

            </form>

        </div>

    @endif


    {{-- ASSIGNED --}}
    @if($workOrder->status === 'ASSIGNED')

        <div class="flex flex-col gap-4 rounded-xl border border-blue-100 bg-blue-50 p-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="font-semibold text-blue-800">
                    Work Order sudah di-assign.
                </p>

                <p class="mt-1 text-sm text-blue-700">
                    Technician:
                    <strong>
                        {{ $workOrder->technician?->username ?? '-' }}
                    </strong>
                </p>
            </div>

            <form
                action="{{ route('work-orders.start', $workOrder) }}"
                method="POST"
            >
                @csrf

                <button
                    type="submit"
                    class="rounded-xl bg-green-600 px-5 py-2.5 text-sm font-semibold text-white
                           transition hover:bg-green-700"
                >
                    ▶ Mulai Pekerjaan
                </button>
            </form>

        </div>

    @endif


    {{-- IN PROGRESS --}}
    @if($workOrder->status === 'IN_PROGRESS')

        <div class="rounded-xl border border-yellow-100 bg-yellow-50 p-4">

            <p class="mb-4 font-semibold text-yellow-800">
                Work Order sedang dikerjakan.
            </p>

            <div class="flex flex-wrap gap-3">

                <form
                    action="{{ route('work-orders.hold', $workOrder) }}"
                    method="POST"
                >
                    @csrf

                    <button
                        type="submit"
                        class="rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white
                               transition hover:bg-orange-600"
                    >
                        ⏸ Tunda
                    </button>
                </form>


                <form
                    action="{{ route('work-orders.complete', $workOrder) }}"
                    method="POST"
                    class="flex flex-1 gap-2 sm:flex-none"
                >
                    @csrf

                    <input
                        type="text"
                        name="completion_notes"
                        required
                        placeholder="Catatan penyelesaian..."
                        class="min-w-0 flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-sm
                               focus:border-green-500 focus:ring-green-500 sm:w-72"
                    >

                    <button
                        type="submit"
                        class="rounded-xl bg-green-600 px-5 py-2.5 text-sm font-semibold text-white
                               transition hover:bg-green-700"
                    >
                        ✓ Selesaikan
                    </button>

                </form>

            </div>

        </div>

    @endif


    {{-- ON HOLD --}}
    @if($workOrder->status === 'ON_HOLD')

        <div class="rounded-xl border border-orange-100 bg-orange-50 p-4">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <p class="font-semibold text-orange-800">
                        Work Order sedang ditunda.
                    </p>

                    <p class="mt-1 text-sm text-orange-700">
                        Pekerjaan dapat dilanjutkan kembali.
                    </p>
                </div>

                <form
                    action="{{ route('work-orders.resume', $workOrder) }}"
                    method="POST"
                >
                    @csrf

                    <button
                        type="submit"
                        class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white
                               transition hover:bg-blue-700"
                    >
                        ▶ Lanjutkan
                    </button>
                </form>

            </div>

        </div>

    @endif


    {{-- COMPLETED --}}
    @if($workOrder->status === 'COMPLETED')

        <div class="rounded-xl border border-green-100 bg-green-50 p-4">

            <p class="font-semibold text-green-800">
                ✓ Work Order telah selesai.
            </p>

            <p class="mt-1 text-sm text-green-700">
                Work Order ini sudah tidak memiliki proses aktif.
            </p>

        </div>

    @endif


    {{-- CANCELLED --}}
    @if($workOrder->status === 'CANCELLED')

        <div class="rounded-xl border border-red-100 bg-red-50 p-4">

            <p class="font-semibold text-red-800">
                Work Order dibatalkan.
            </p>

            <p class="mt-1 text-sm text-red-700">
                Work Order ini sudah tidak dapat diproses kembali.
            </p>

        </div>

    @endif


    {{-- CANCEL BUTTON --}}
    @if(in_array($workOrder->status, ['OPEN', 'ASSIGNED', 'ON_HOLD']))

        <div class="mt-4 border-t border-slate-100 pt-4">

            <form
                action="{{ route('work-orders.cancel', $workOrder) }}"
                method="POST"
                onsubmit="return confirm('Yakin ingin membatalkan Work Order ini?')"
            >
                @csrf

                <button
                    type="submit"
                    class="text-sm font-semibold text-red-600 hover:text-red-700"
                >
                    Batalkan Work Order
                </button>
            </form>

        </div>

    @endif

</div>


    {{-- =========================================================
        OVERVIEW CARDS
    ========================================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        {{-- Equipment --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">

            <div class="flex items-start gap-4">

                <div class="w-11 h-11 rounded-xl
                            bg-blue-50
                            flex items-center justify-center
                            flex-shrink-0">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6 text-blue-600"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M10.5 6h3m-7.5 4h15m-13 4h11m-9 4h7M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />

                    </svg>

                </div>

                <div class="min-w-0">

                    <p class="text-xs text-slate-500 uppercase font-semibold tracking-wide">
                        Equipment
                    </p>

                    <p class="font-semibold text-slate-800 mt-1 truncate">
                        {{ $workOrder->equipment?->name ?? '-' }}
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        {{ $workOrder->equipment?->equipment_code ?? '-' }}
                    </p>

                </div>

            </div>

        </div>


        {{-- Technician --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">

            <div class="flex items-start gap-4">

                <div class="w-11 h-11 rounded-xl
                            bg-violet-50
                            flex items-center justify-center
                            flex-shrink-0">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6 text-violet-600"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM5 21a7 7 0 0114 0" />

                    </svg>

                </div>

                <div class="min-w-0">

                    <p class="text-xs text-slate-500 uppercase font-semibold tracking-wide">
                        Technician
                    </p>

                    @if($workOrder->technician)

                        <p class="font-semibold text-slate-800 mt-1 truncate">
                            {{ $workOrder->technician->username }}
                        </p>

                        <p class="text-xs text-slate-400 mt-1">
                            {{ $workOrder->technician->role ?? 'Technician' }}
                        </p>

                    @else

                        <p class="font-semibold text-slate-400 mt-1">
                            Belum ditugaskan
                        </p>

                    @endif

                </div>

            </div>

        </div>


        {{-- Maintenance Type --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">

            <div class="flex items-start gap-4">

                <div class="w-11 h-11 rounded-xl
                            bg-emerald-50
                            flex items-center justify-center
                            flex-shrink-0">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6 text-emerald-600"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 12l2 2 4-4m5-5v5a9 9 0 11-18 0V5l9-3 9 3z" />

                    </svg>

                </div>

                <div>

                    <p class="text-xs text-slate-500 uppercase font-semibold tracking-wide">
                        Maintenance Type
                    </p>

                    @php
                        $typeLabels = [
                            'CORRECTIVE' => 'Corrective',
                            'PREVENTIVE' => 'Preventive',
                            'INSPECTION' => 'Inspection',
                        ];
                    @endphp

                    <p class="font-semibold text-slate-800 mt-1">
                        {{ $typeLabels[$workOrder->maintenance_type] ?? $workOrder->maintenance_type }}
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        Priority: {{ $workOrder->priority }}
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        MAIN CONTENT
    ========================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


        {{-- =====================================================
            LEFT CONTENT
        ====================================================== --}}
        <div class="lg:col-span-2 space-y-6">


            {{-- Work Order Information --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">

                <div class="px-5 py-4 border-b border-slate-200">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Informasi Work Order
                    </h2>

                </div>

                <div class="p-5">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- WO Number --}}
                        <div>

                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                WO Number
                            </p>

                            <p class="text-sm font-semibold text-blue-600 mt-1">
                                {{ $workOrder->wo_number }}
                            </p>

                        </div>


                        {{-- Priority --}}
                        <div>

                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                Priority
                            </p>

                            @php
                                $priorityClasses = [
                                    'LOW' => 'bg-slate-100 text-slate-600',
                                    'MEDIUM' => 'bg-blue-100 text-blue-700',
                                    'HIGH' => 'bg-orange-100 text-orange-700',
                                    'CRITICAL' => 'bg-red-100 text-red-700',
                                ];
                            @endphp

                            <div class="mt-1">

                                <span class="inline-flex px-2.5 py-1
                                             rounded-full
                                             text-xs font-semibold
                                             {{ $priorityClasses[$workOrder->priority] ?? 'bg-slate-100 text-slate-600' }}">

                                    {{ $workOrder->priority }}

                                </span>

                            </div>

                        </div>


                        {{-- Created --}}
                        <div>

                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                Dibuat
                            </p>

                            <p class="text-sm text-slate-700 mt-1">
                                {{ $workOrder->created_at?->format('d M Y, H:i') ?? '-' }}
                            </p>

                        </div>


                        {{-- Updated --}}
                        <div>

                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                Terakhir Diperbarui
                            </p>

                            <p class="text-sm text-slate-700 mt-1">
                                {{ $workOrder->updated_at?->format('d M Y, H:i') ?? '-' }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Maintenance Request --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">

                <div class="px-5 py-4 border-b border-slate-200">

                    <div class="flex items-center justify-between">

                        <h2 class="text-lg font-semibold text-slate-800">
                            Maintenance Request
                        </h2>

                        @if($workOrder->maintenanceRequest)

                            <span class="text-xs font-medium text-blue-600">
                                MR #{{ $workOrder->maintenanceRequest->id }}
                            </span>

                        @endif

                    </div>

                </div>

                <div class="p-5">

                    @if($workOrder->maintenanceRequest)

                        <div class="space-y-4">

                            <div>

                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                    Equipment
                                </p>

                                <p class="text-sm text-slate-700 mt-1">
                                    {{ $workOrder->maintenanceRequest->equipment?->name ?? '-' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                    Deskripsi Request
                                </p>

                                <div class="mt-2
                                            bg-slate-50
                                            border border-slate-200
                                            rounded-xl
                                            p-4">

                                    <p class="text-sm text-slate-700 whitespace-pre-line">
                                        {{ $workOrder->maintenanceRequest->description ?? 'Tidak ada deskripsi.' }}
                                    </p>

                                </div>

                            </div>


                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                                <div>

                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                        Requester / Engineer
                                    </p>

                                    <p class="text-sm text-slate-700 mt-1">
                                        {{ $workOrder->maintenanceRequest->engineer?->username ?? '-' }}
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                        Status Request
                                    </p>

                                    <p class="text-sm text-slate-700 mt-1">
                                        {{ $workOrder->maintenanceRequest->status ?? '-' }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    @else

                        <div class="py-8 text-center">

                            <p class="text-sm text-slate-400">
                                Work Order ini tidak berasal dari Maintenance Request.
                            </p>

                        </div>

                    @endif

                </div>

            </div>


            {{-- Problem & Analysis --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">

                <div class="px-5 py-4 border-b border-slate-200">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Problem & Analysis
                    </h2>

                </div>

                <div class="p-5 space-y-5">

                    {{-- Problem --}}
                    <div>

                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                            Problem Description
                        </p>

                        <div class="mt-2
                                    bg-slate-50
                                    border border-slate-200
                                    rounded-xl
                                    p-4">

                            <p class="text-sm text-slate-700 whitespace-pre-line">
                                {{ $workOrder->problem_description ?? 'Belum ada deskripsi masalah.' }}
                            </p>

                        </div>

                    </div>


                    {{-- Root Cause --}}
                    <div>

                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                            Root Cause
                        </p>

                        <div class="mt-2
                                    bg-slate-50
                                    border border-slate-200
                                    rounded-xl
                                    p-4">

                            <p class="text-sm text-slate-700 whitespace-pre-line">
                                {{ $workOrder->root_cause ?? 'Belum dianalisis.' }}
                            </p>

                        </div>

                    </div>


                    {{-- Corrective Action --}}
                    <div>

                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                            Corrective Action
                        </p>

                        <div class="mt-2
                                    bg-slate-50
                                    border border-slate-200
                                    rounded-xl
                                    p-4">

                            <p class="text-sm text-slate-700 whitespace-pre-line">
                                {{ $workOrder->corrective_action ?? 'Belum ada tindakan perbaikan.' }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Completion Notes --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">

                <div class="px-5 py-4 border-b border-slate-200">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Completion Notes
                    </h2>

                </div>

                <div class="p-5">

                    <div class="bg-slate-50
                                border border-slate-200
                                rounded-xl
                                p-4">

                        <p class="text-sm text-slate-700 whitespace-pre-line">

                            {{ $workOrder->completion_notes ?? 'Belum ada catatan penyelesaian.' }}

                        </p>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            RIGHT CONTENT
        ====================================================== --}}
        <div class="space-y-6">


            {{-- Schedule --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">

                <div class="px-5 py-4 border-b border-slate-200">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Schedule
                    </h2>

                </div>

                <div class="p-5 space-y-5">

                    {{-- Planned Start --}}
                    <div>

                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                            Planned Start
                        </p>

                        <div class="flex items-center gap-3 mt-2">

                            <div class="w-9 h-9 rounded-lg
                                        bg-blue-50
                                        flex items-center justify-center">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-5 h-5 text-blue-600"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor"
                                     stroke-width="2">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H3a2 2 0 00-2 2v12a2 2 0 002 2z" />

                                </svg>

                            </div>

                            <div>

                                <p class="text-sm font-medium text-slate-700">

                                    {{ $workOrder->planned_start?->format('d M Y') ?? '-' }}

                                </p>

                                <p class="text-xs text-slate-400">

                                    {{ $workOrder->planned_start?->format('H:i') ?? '-' }}

                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- Planned End --}}
                    <div>

                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                            Planned End
                        </p>

                        <div class="flex items-center gap-3 mt-2">

                            <div class="w-9 h-9 rounded-lg
                                        bg-amber-50
                                        flex items-center justify-center">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-5 h-5 text-amber-600"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor"
                                     stroke-width="2">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H3a2 2 0 00-2 2v12a2 2 0 002 2z" />

                                </svg>

                            </div>

                            <div>

                                <p class="text-sm font-medium text-slate-700">

                                    {{ $workOrder->planned_end?->format('d M Y') ?? '-' }}

                                </p>

                                <p class="text-xs text-slate-400">

                                    {{ $workOrder->planned_end?->format('H:i') ?? '-' }}

                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="border-t border-slate-100"></div>


                    {{-- Actual Start --}}
                    <div>

                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                            Actual Start
                        </p>

                        <p class="text-sm text-slate-700 mt-2">

                            {{ $workOrder->actual_start?->format('d M Y, H:i') ?? 'Belum dimulai' }}

                        </p>

                    </div>


                    {{-- Actual End --}}
                    <div>

                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                            Actual End
                        </p>

                        <p class="text-sm text-slate-700 mt-2">

                            {{ $workOrder->actual_end?->format('d M Y, H:i') ?? 'Belum selesai' }}

                        </p>

                    </div>

                </div>

            </div>


            {{-- Equipment Information --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">

                <div class="px-5 py-4 border-b border-slate-200">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Equipment
                    </h2>

                </div>

                <div class="p-5">

                    @if($workOrder->equipment)

                        <div class="space-y-4">

                            <div>

                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                    Equipment Code
                                </p>

                                <p class="text-sm font-semibold text-blue-600 mt-1">
                                    {{ $workOrder->equipment->equipment_code }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                    Name
                                </p>

                                <p class="text-sm text-slate-700 mt-1">
                                    {{ $workOrder->equipment->name }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                    Location
                                </p>

                                <p class="text-sm text-slate-700 mt-1">
                                    {{ $workOrder->equipment->location ?? '-' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                    Status
                                </p>

                                <p class="text-sm text-slate-700 mt-1">
                                    {{ $workOrder->equipment->status ?? '-' }}
                                </p>

                            </div>

                        </div>

                    @else

                        <p class="text-sm text-slate-400">
                            Data equipment tidak tersedia.
                        </p>

                    @endif

                </div>

            </div>


            {{-- Technician Information --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">

                <div class="px-5 py-4 border-b border-slate-200">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Technician
                    </h2>

                </div>

                <div class="p-5">

                    @if($workOrder->technician)

                        <div class="flex items-center gap-3">

                            <div class="w-11 h-11 rounded-full
                                        bg-blue-100
                                        text-blue-700
                                        flex items-center justify-center
                                        font-bold">

                                {{ strtoupper(substr($workOrder->technician->username, 0, 1)) }}

                            </div>

                            <div>

                                <p class="font-semibold text-slate-800">
                                    {{ $workOrder->technician->username }}
                                </p>

                                <p class="text-xs text-slate-400 mt-1">
                                    {{ $workOrder->technician->role ?? 'Technician' }}
                                </p>

                            </div>

                        </div>

                        @if($workOrder->technician->email)

                            <div class="mt-4 pt-4 border-t border-slate-100">

                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                    Email
                                </p>

                                <p class="text-sm text-slate-600 mt-1 break-all">
                                    {{ $workOrder->technician->email }}
                                </p>

                            </div>

                        @endif

                    @else

                        <div class="text-center py-4">

                            <div class="w-10 h-10 rounded-full
                                        bg-slate-100
                                        flex items-center justify-center
                                        mx-auto mb-2">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-5 h-5 text-slate-400"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor"
                                     stroke-width="2">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM5 21a7 7 0 0114 0" />

                                </svg>

                            </div>

                            <p class="text-sm text-slate-400">
                                Belum ada technician yang ditugaskan.
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@endsection