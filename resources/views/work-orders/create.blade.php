@extends('layouts.app')

@section('title', 'Create Work Order')

@section('content')

<div class="max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Create Work Order
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Buat Work Order untuk pekerjaan maintenance equipment.
            </p>
        </div>

        <a
            href="{{ route('work-orders.index') }}"
            class="inline-flex items-center px-4 py-2
                   bg-slate-200 hover:bg-slate-300
                   text-slate-700 rounded-lg
                   transition"
        >
            ← Back
        </a>

    </div>


    {{-- Validation Error --}}
    @if ($errors->any())

        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">

            <div class="font-semibold text-red-700 mb-2">
                Terdapat kesalahan:
            </div>

            <ul class="list-disc list-inside text-sm text-red-600">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Form --}}
    <form
        action="{{ route('work-orders.store') }}"
        method="POST"
    >

        @csrf


        {{-- Main Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200">

            {{-- Card Header --}}
            <div class="px-6 py-4 border-b border-slate-200">

                <h2 class="font-semibold text-slate-800">
                    Work Order Information
                </h2>

            </div>


            {{-- Card Body --}}
            <div class="p-6 space-y-6">


                {{-- Maintenance Request --}}
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Maintenance Request
                    </label>

                    <select
                        name="maintenance_request_id"
                        id="maintenance_request_id"
                        class="w-full rounded-lg border-slate-300
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >

                        <option value="">
                            -- Manual Work Order --
                        </option>

                        @foreach ($maintenanceRequests as $requestItem)

                            <option
                                value="{{ $requestItem->id }}"
                                data-equipment="{{ $requestItem->equipment_id }}"
                                data-priority="{{ $requestItem->priority }}"
                                data-description="{{ $requestItem->description }}"
                                @selected(
                                    old(
                                        'maintenance_request_id',
                                        $selectedRequest?->id
                                    ) == $requestItem->id
                                )
                            >

                                {{ $requestItem->id }}
                                -
                                {{ $requestItem->equipment?->equipment_code }}
                                -
                                {{ $requestItem->equipment?->name }}

                            </option>

                        @endforeach

                    </select>

                    <p class="text-xs text-slate-500 mt-1">
                        Pilih Maintenance Request jika Work Order berasal dari request yang sudah ada.
                    </p>

                </div>


                {{-- Equipment --}}
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Equipment <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="equipment_id"
                        id="equipment_id"
                        required
                        class="w-full rounded-lg border-slate-300
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >

                        <option value="">
                            -- Pilih Equipment --
                        </option>

                        @foreach ($equipment as $item)

                            <option
                                value="{{ $item->id }}"
                                @selected(
                                    old(
                                        'equipment_id',
                                        $selectedRequest?->equipment_id
                                    ) == $item->id
                                )
                            >

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

                    <select
                        name="technician_id"
                        class="w-full rounded-lg border-slate-300
                               focus:border-blue-500
                               focus:ring-blue-500"
                    >

                        <option value="">
                            -- Belum Ditugaskan --
                        </option>

                        @foreach ($technicians as $technician)

                            <option
                                value="{{ $technician->id }}"
                                @selected(
                                    old('technician_id') == $technician->id
                                )
                            >

                                {{ $technician->username }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Two Columns --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                    {{-- Maintenance Type --}}
                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Maintenance Type
                        </label>

                        <select
                            name="maintenance_type"
                            class="w-full rounded-lg border-slate-300
                                   focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                            <option
                                value="CORRECTIVE"
                                @selected(old('maintenance_type', 'CORRECTIVE') === 'CORRECTIVE')
                            >
                                Corrective
                            </option>

                            <option
                                value="PREVENTIVE"
                                @selected(old('maintenance_type') === 'PREVENTIVE')
                            >
                                Preventive
                            </option>

                            <option
                                value="INSPECTION"
                                @selected(old('maintenance_type') === 'INSPECTION')
                            >
                                Inspection
                            </option>

                        </select>

                    </div>


                    {{-- Priority --}}
                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Priority
                        </label>

                        <select
                            name="priority"
                            id="priority"
                            class="w-full rounded-lg border-slate-300
                                   focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                            @foreach ([
                                'LOW' => 'Low',
                                'MEDIUM' => 'Medium',
                                'HIGH' => 'High',
                                'CRITICAL' => 'Critical'
                            ] as $value => $label)

                                <option
                                    value="{{ $value }}"
                                    @selected(old('priority', $selectedRequest?->priority ?? 'MEDIUM') === $value)
                                >
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>


                {{-- Problem Description --}}
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Problem Description
                    </label>

                    <textarea
                        name="problem_description"
                        id="problem_description"
                        rows="4"
                        class="w-full rounded-lg border-slate-300
                               focus:border-blue-500
                               focus:ring-blue-500"
                        placeholder="Jelaskan masalah atau pekerjaan yang harus dilakukan..."
                    >{{ old('problem_description', $selectedRequest?->description) }}</textarea>

                </div>


                {{-- Root Cause --}}
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Root Cause
                    </label>

                    <textarea
                        name="root_cause"
                        rows="3"
                        class="w-full rounded-lg border-slate-300
                               focus:border-blue-500
                               focus:ring-blue-500"
                        placeholder="Akan diisi setelah penyebab masalah diketahui..."
                    >{{ old('root_cause') }}</textarea>

                </div>


                {{-- Corrective Action --}}
                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Corrective Action
                    </label>

                    <textarea
                        name="corrective_action"
                        rows="3"
                        class="w-full rounded-lg border-slate-300
                               focus:border-blue-500
                               focus:ring-blue-500"
                        placeholder="Tindakan perbaikan yang dilakukan..."
                    >{{ old('corrective_action') }}</textarea>

                </div>


                {{-- Schedule --}}
                <div>

                    <h3 class="font-semibold text-slate-800 mb-4">
                        Maintenance Schedule
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Planned Start --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Planned Start
                            </label>

                            <input
                                type="datetime-local"
                                name="planned_start"
                                value="{{ old('planned_start') }}"
                                class="w-full rounded-lg border-slate-300
                                       focus:border-blue-500
                                       focus:ring-blue-500"
                            >

                        </div>


                        {{-- Planned End --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Planned End
                            </label>

                            <input
                                type="datetime-local"
                                name="planned_end"
                                value="{{ old('planned_end') }}"
                                class="w-full rounded-lg border-slate-300
                                       focus:border-blue-500
                                       focus:ring-blue-500"
                            >

                        </div>

                    </div>

                </div>


            </div>


            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-slate-200
                        flex justify-end gap-3">

                <a
                    href="{{ route('work-orders.index') }}"
                    class="px-5 py-2.5 rounded-lg
                           bg-slate-200 hover:bg-slate-300
                           text-slate-700 transition"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="px-5 py-2.5 rounded-lg
                           bg-blue-600 hover:bg-blue-700
                           text-white transition"
                >
                    Create Work Order
                </button>

            </div>

        </div>

    </form>

</div>


{{-- Auto Fill --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const requestSelect = document.getElementById(
        'maintenance_request_id'
    );

    const equipmentSelect = document.getElementById(
        'equipment_id'
    );

    const prioritySelect = document.getElementById(
        'priority'
    );

    const descriptionField = document.getElementById(
        'problem_description'
    );


    requestSelect.addEventListener('change', function () {

        const selected =
            this.options[this.selectedIndex];


        if (!this.value) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Equipment
        |--------------------------------------------------------------------------
        */

        const equipmentId =
            selected.dataset.equipment;

        if (equipmentId) {

            equipmentSelect.value =
                equipmentId;

        }


        /*
        |--------------------------------------------------------------------------
        | Priority
        |--------------------------------------------------------------------------
        */

        const priority =
            selected.dataset.priority;

        if (priority) {

            prioritySelect.value =
                priority;

        }


        /*
        |--------------------------------------------------------------------------
        | Description
        |--------------------------------------------------------------------------
        */

        const description =
            selected.dataset.description;

        if (
            description &&
            !descriptionField.value
        ) {

            descriptionField.value =
                description;

        }

    });

});

</script>

@endsection