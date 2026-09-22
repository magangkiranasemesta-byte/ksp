@extends('layouts.app')

@section('title', 'Work Order')

@section('content')

<div class="min-h-screen bg-slate-100 p-6">

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div>
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-600/20">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6 text-white"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>

                <div>
                    <h1 class="text-2xl font-bold text-slate-800">
                        Work Order
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Kelola pekerjaan maintenance equipment
                    </p>
                </div>
            </div>
        </div>

        <a href="{{ route('work-orders.create') }}"
           class="inline-flex items-center justify-center gap-2
                  px-4 py-2.5
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
                      d="M12 4v16m8-8H4" />
            </svg>

            Buat Work Order
        </a>

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


    @if(session('warning'))

        <div class="mb-6 flex items-start gap-3
                    bg-amber-50
                    border border-amber-200
                    text-amber-700
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
                      d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z" />
            </svg>

            <span class="text-sm font-medium">
                {{ session('warning') }}
            </span>

        </div>

    @endif


    {{-- =========================================================
        STATISTICS
    ========================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

        {{-- Total --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        Total Work Order
                    </p>

                    <p class="text-2xl font-bold text-slate-800 mt-2">
                        {{ $totalWorkOrders }}
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6 text-blue-600"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>

                </div>

            </div>

        </div>


        {{-- Open --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        Open / Assigned
                    </p>

                    <p class="text-2xl font-bold text-amber-600 mt-2">
                        {{ $openWorkOrders }}
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6 text-amber-600"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                </div>

            </div>

        </div>


        {{-- In Progress --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        In Progress
                    </p>

                    <p class="text-2xl font-bold text-blue-600 mt-2">
                        {{ $inProgressWorkOrders }}
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6 text-blue-600"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>

                </div>

            </div>

        </div>


        {{-- Completed --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        Completed
                    </p>

                    <p class="text-2xl font-bold text-emerald-600 mt-2">
                        {{ $completedWorkOrders }}
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6 text-emerald-600"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M5 13l4 4L19 7" />
                    </svg>

                </div>

            </div>

        </div>


        {{-- On Hold --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        On Hold
                    </p>

                    <p class="text-2xl font-bold text-red-600 mt-2">
                        {{ $onHoldWorkOrders }}
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-6 h-6 text-red-600"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M10 9v6m4-6v6m5-3a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        FILTER & SEARCH
    ========================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">

        <form method="GET"
              action="{{ route('work-orders.index') }}"
              class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- Search --}}
            <div class="md:col-span-2">

                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Cari Work Order
                </label>

                <div class="relative">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="absolute left-3 top-1/2 -translate-y-1/2
                                w-5 h-5 text-slate-400"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M21 21l-4.35-4.35m2.35-5.65a8 8 0 11-16 0 8 8 0 0116 0z" />
                    </svg>

                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nomor WO, equipment, teknisi..."
                           class="w-full pl-10 pr-4 py-2.5
                                  border border-slate-300
                                  rounded-xl
                                  text-sm
                                  focus:outline-none
                                  focus:ring-2
                                  focus:ring-blue-500
                                  focus:border-blue-500">

                </div>

            </div>


            {{-- Status --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Status
                </label>

                <select name="status"
                        class="w-full px-4 py-2.5
                               border border-slate-300
                               rounded-xl
                               text-sm
                               bg-white
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-500">

                    <option value="">
                        Semua Status
                    </option>

                    <option value="OPEN"
                        {{ request('status') === 'OPEN' ? 'selected' : '' }}>
                        Open
                    </option>

                    <option value="ASSIGNED"
                        {{ request('status') === 'ASSIGNED' ? 'selected' : '' }}>
                        Assigned
                    </option>

                    <option value="IN_PROGRESS"
                        {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>
                        In Progress
                    </option>

                    <option value="ON_HOLD"
                        {{ request('status') === 'ON_HOLD' ? 'selected' : '' }}>
                        On Hold
                    </option>

                    <option value="COMPLETED"
                        {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>
                        Completed
                    </option>

                    <option value="CANCELLED"
                        {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>
                        Cancelled
                    </option>

                </select>

            </div>


            {{-- Priority --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Priority
                </label>

                <select name="priority"
                        class="w-full px-4 py-2.5
                               border border-slate-300
                               rounded-xl
                               text-sm
                               bg-white
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-500">

                    <option value="">
                        Semua Priority
                    </option>

                    <option value="LOW"
                        {{ request('priority') === 'LOW' ? 'selected' : '' }}>
                        Low
                    </option>

                    <option value="MEDIUM"
                        {{ request('priority') === 'MEDIUM' ? 'selected' : '' }}>
                        Medium
                    </option>

                    <option value="HIGH"
                        {{ request('priority') === 'HIGH' ? 'selected' : '' }}>
                        High
                    </option>

                    <option value="CRITICAL"
                        {{ request('priority') === 'CRITICAL' ? 'selected' : '' }}>
                        Critical
                    </option>

                </select>

            </div>


            {{-- Buttons --}}
            <div class="md:col-span-4 flex flex-wrap gap-2 justify-end">

                <a href="{{ route('work-orders.index') }}"
                   class="inline-flex items-center gap-2
                          px-4 py-2.5
                          border border-slate-300
                          text-slate-600
                          text-sm font-medium
                          rounded-xl
                          hover:bg-slate-50
                          transition">

                    Reset

                </a>

                <button type="submit"
                        class="inline-flex items-center gap-2
                               px-5 py-2.5
                               bg-slate-800
                               hover:bg-slate-900
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
                              d="M21 21l-4.35-4.35m2.35-5.65a8 8 0 11-16 0 8 8 0 0116 0z" />
                    </svg>

                    Filter

                </button>

            </div>

        </form>

    </div>


    {{-- =========================================================
        TABLE
    ========================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Table Header --}}
        <div class="px-5 py-4 border-b border-slate-200">

            <div class="flex items-center justify-between">

                <div>
                    <h2 class="text-lg font-semibold text-slate-800">
                        Daftar Work Order
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Menampilkan {{ $workOrders->count() }} dari {{ $workOrders->total() }} Work Order
                    </p>
                </div>

            </div>

        </div>


        @if($workOrders->count() > 0)

            <div class="overflow-x-auto">

                <table class="w-full min-w-[1100px]">

                    <thead class="bg-slate-50">

                        <tr class="border-b border-slate-200">

                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                WO Number
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Equipment
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Technician
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Type
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Priority
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Schedule
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Status
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @foreach($workOrders as $workOrder)

                            <tr class="hover:bg-slate-50 transition">

                                {{-- WO Number --}}
                                <td class="px-5 py-4">

                                    <div class="font-semibold text-blue-600">
                                        {{ $workOrder->wo_number }}
                                    </div>

                                    @if($workOrder->maintenanceRequest)
                                        <div class="text-xs text-slate-400 mt-1">
                                            MR #{{ $workOrder->maintenanceRequest->id }}
                                        </div>
                                    @endif

                                </td>


                                {{-- Equipment --}}
                                <td class="px-5 py-4">

                                    <div class="font-medium text-slate-700">
                                        {{ $workOrder->equipment?->name ?? '-' }}
                                    </div>

                                    <div class="text-xs text-slate-400 mt-1">
                                        {{ $workOrder->equipment?->equipment_code ?? '-' }}
                                    </div>

                                </td>


                                {{-- Technician --}}
                                <td class="px-5 py-4">

                                    @if($workOrder->technician)

                                        <div class="flex items-center gap-2">

                                            <div class="w-8 h-8 rounded-full
                                                        bg-blue-100
                                                        text-blue-700
                                                        flex items-center justify-center
                                                        text-xs font-bold">

                                                {{ strtoupper(substr($workOrder->technician->username, 0, 1)) }}

                                            </div>

                                            <div>
                                                <div class="font-medium text-slate-700">
                                                    {{ $workOrder->technician->username }}
                                                </div>

                                                <div class="text-xs text-slate-400">
                                                    {{ $workOrder->technician->role ?? 'Technician' }}
                                                </div>
                                            </div>

                                        </div>

                                    @else

                                        <span class="text-sm text-slate-400 italic">
                                            Belum ditugaskan
                                        </span>

                                    @endif

                                </td>


                                {{-- Maintenance Type --}}
                                <td class="px-5 py-4">

                                    @php
                                        $typeLabels = [
                                            'CORRECTIVE' => 'Corrective',
                                            'PREVENTIVE' => 'Preventive',
                                            'INSPECTION' => 'Inspection',
                                        ];
                                    @endphp

                                    <span class="text-sm text-slate-600">
                                        {{ $typeLabels[$workOrder->maintenance_type] ?? $workOrder->maintenance_type }}
                                    </span>

                                </td>


                                {{-- Priority --}}
                                <td class="px-5 py-4">

                                    @php
                                        $priorityClasses = [
                                            'LOW' => 'bg-slate-100 text-slate-600',
                                            'MEDIUM' => 'bg-blue-100 text-blue-700',
                                            'HIGH' => 'bg-orange-100 text-orange-700',
                                            'CRITICAL' => 'bg-red-100 text-red-700',
                                        ];

                                        $priorityClass =
                                            $priorityClasses[$workOrder->priority]
                                            ?? 'bg-slate-100 text-slate-600';
                                    @endphp

                                    <span class="inline-flex items-center px-2.5 py-1
                                                 rounded-full text-xs font-semibold
                                                 {{ $priorityClass }}">

                                        {{ $workOrder->priority }}

                                    </span>

                                </td>


                                {{-- Schedule --}}
                                <td class="px-5 py-4">

                                    @if($workOrder->planned_start)

                                        <div class="text-sm font-medium text-slate-700">
                                            {{ $workOrder->planned_start->format('d M Y') }}
                                        </div>

                                        <div class="text-xs text-slate-400 mt-1">
                                            {{ $workOrder->planned_start->format('H:i') }}

                                            @if($workOrder->planned_end)
                                                -
                                                {{ $workOrder->planned_end->format('H:i') }}
                                            @endif
                                        </div>

                                    @else

                                        <span class="text-sm text-slate-400 italic">
                                            Belum dijadwalkan
                                        </span>

                                    @endif

                                </td>


                                {{-- Status --}}
                                <td class="px-5 py-4">

                                    @php

                                        $statusClasses = [
                                            'OPEN' =>
                                                'bg-slate-100 text-slate-700',

                                            'ASSIGNED' =>
                                                'bg-amber-100 text-amber-700',

                                            'IN_PROGRESS' =>
                                                'bg-blue-100 text-blue-700',

                                            'ON_HOLD' =>
                                                'bg-orange-100 text-orange-700',

                                            'COMPLETED' =>
                                                'bg-emerald-100 text-emerald-700',

                                            'CANCELLED' =>
                                                'bg-red-100 text-red-700',
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

                                    @endphp

                                    <span class="inline-flex items-center gap-1.5
                                                 px-2.5 py-1
                                                 rounded-full
                                                 text-xs font-semibold
                                                 {{ $statusClass }}">

                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                        {{ $statusLabels[$workOrder->status] ?? $workOrder->status }}

                                    </span>

                                </td>


                                {{-- Action --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        <a href="{{ route('work-orders.show', $workOrder) }}"
                                           title="Lihat Detail"
                                           class="w-9 h-9
                                                  inline-flex items-center justify-center
                                                  rounded-lg
                                                  bg-blue-50
                                                  text-blue-600
                                                  hover:bg-blue-100
                                                  transition">

                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="w-4 h-4"
                                                 fill="none"
                                                 viewBox="0 0 24 24"
                                                 stroke="currentColor"
                                                 stroke-width="2">

                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />

                                            </svg>

                                        </a>


                                        <a href="{{ route('work-orders.edit', $workOrder) }}"
                                           title="Edit"
                                           class="w-9 h-9
                                                  inline-flex items-center justify-center
                                                  rounded-lg
                                                  bg-amber-50
                                                  text-amber-600
                                                  hover:bg-amber-100
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

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            <div class="px-5 py-4 border-t border-slate-200">

                {{ $workOrders->links() }}

            </div>

        @else

            {{-- =====================================================
                EMPTY STATE
            ====================================================== --}}
            <div class="py-20 px-6 text-center">

                <div class="w-16 h-16 mx-auto
                            rounded-2xl
                            bg-slate-100
                            flex items-center justify-center
                            mb-5">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-8 h-8 text-slate-400"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.5">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />

                    </svg>

                </div>

                <h3 class="text-lg font-semibold text-slate-700">
                    Belum Ada Work Order
                </h3>

                <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto">
                    Belum terdapat Work Order yang tersedia.
                    Buat Work Order baru untuk memulai proses maintenance.
                </p>

                <a href="{{ route('work-orders.create') }}"
                   class="inline-flex items-center gap-2
                          mt-6
                          px-5 py-2.5
                          bg-blue-600
                          hover:bg-blue-700
                          text-white
                          text-sm font-semibold
                          rounded-xl
                          transition">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 4v16m8-8H4" />
                    </svg>

                    Buat Work Order

                </a>

            </div>

        @endif

    </div>

</div>

@endsection