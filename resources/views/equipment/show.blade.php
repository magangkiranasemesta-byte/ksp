@extends('layouts.app')

@section('title', 'Equipment Detail - ' . $equipment->equipment_code)

@section('page_title', 'Equipment Detail')

@section('content')

<div class="space-y-6">

```
{{-- Back Navigation --}}
<div>
    <a
        href="{{ route('equipment.index') }}"
        class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition"
    >
        <span>&larr;</span>
        Kembali ke Equipment
    </a>
</div>

{{-- Header --}}
<div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

        <div>

            <div class="flex flex-wrap items-center gap-3">

                <h2 class="text-2xl font-bold text-slate-900">
                    {{ $equipment->name }}
                </h2>

                @php
                    $statusStyle = [
                        'ACTIVE' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'MAINTENANCE' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'INACTIVE' => 'bg-slate-100 text-slate-600 border-slate-200',
                    ][strtoupper($equipment->status)] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                @endphp

                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusStyle }}">
                    {{ $equipment->status }}
                </span>

            </div>

            <p class="mt-2 text-sm text-slate-500">
                Equipment Code:
                <span class="font-mono font-bold text-slate-700">
                    {{ $equipment->equipment_code }}
                </span>
            </p>

        </div>

        {{-- QR Button --}}
        <div class="flex flex-wrap items-center gap-2">

            <a
                href="{{ route('equipment.qr', $equipment) }}"
                target="_blank"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-500/20 transition"
            >
                <span>▣</span>
                Generate QR Code
            </a>

            <a
                href="{{ route('equipment.index') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 text-sm font-semibold rounded-xl transition"
            >
                Back
            </a>

        </div>

    </div>

</div>

{{-- Main Information --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Equipment Information --}}
    <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">

        <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">
            Informasi Equipment
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5">

            {{-- Code --}}
            <div>

                <span class="text-xs text-slate-400 block font-medium">
                    Equipment Code
                </span>

                <p class="text-sm font-mono font-bold text-slate-800 mt-1">
                    {{ $equipment->equipment_code }}
                </p>

            </div>

            {{-- Name --}}
            <div>

                <span class="text-xs text-slate-400 block font-medium">
                    Equipment Name
                </span>

                <p class="text-sm font-semibold text-slate-800 mt-1">
                    {{ $equipment->name }}
                </p>

            </div>

            {{-- Location --}}
            <div>

                <span class="text-xs text-slate-400 block font-medium">
                    Location
                </span>

                <p class="text-sm text-slate-700 mt-1">
                    {{ $equipment->location }}
                </p>

            </div>

            {{-- Status --}}
            <div>

                <span class="text-xs text-slate-400 block font-medium">
                    Status
                </span>

                <span class="inline-block mt-1 px-2.5 py-1 text-[11px] font-semibold rounded-full border {{ $statusStyle }}">
                    {{ $equipment->status }}
                </span>

            </div>

        </div>

        {{-- Description --}}
        <div class="mt-6">

            <span class="text-xs text-slate-400 block font-medium">
                Description
            </span>

            <div class="mt-2 bg-slate-50 p-4 rounded-xl border border-slate-100">

                <p class="text-sm text-slate-600 leading-relaxed">
                    {{ $equipment->description ?: 'Tidak ada deskripsi equipment.' }}
                </p>

            </div>

        </div>

    </div>

    {{-- QR Information --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">

        <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">
            QR Equipment
        </h3>

        <div class="mt-5 text-center">

            <div class="w-20 h-20 mx-auto rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center">

                <span class="text-4xl text-blue-600">
                    ▣
                </span>

            </div>

            <h4 class="mt-4 text-sm font-bold text-slate-800">
                QR Code Equipment
            </h4>

            <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                Gunakan QR Code untuk membuka halaman detail equipment dengan cepat.
            </p>

            <a
                href="{{ route('equipment.qr', $equipment) }}"
                target="_blank"
                class="mt-5 inline-flex w-full items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition"
            >
                Lihat QR Code
            </a>

        </div>

    </div>

</div>

{{-- Maintenance & Downtime --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Maintenance History --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">

        <div class="flex items-center justify-between border-b border-slate-100 pb-3">

            <h3 class="font-bold text-slate-800">
                Maintenance
            </h3>

            <span class="text-xs text-slate-400">
                Riwayat
            </span>

        </div>

        @if($equipment->maintenanceRequests->count() > 0)

            <div class="mt-4 space-y-3">

                @foreach($equipment->maintenanceRequests->take(5) as $maintenance)

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">

                        <div class="flex items-center justify-between gap-3">

                            <span class="text-sm font-semibold text-slate-700">
                                Request #{{ $maintenance->id }}
                            </span>

                            @if(isset($maintenance->status))

                                <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-slate-100 text-slate-600">
                                    {{ $maintenance->status }}
                                </span>

                            @endif

                        </div>

                        @if(isset($maintenance->created_at))

                            <p class="text-xs text-slate-400 mt-1">
                                {{ $maintenance->created_at->format('d/m/Y H:i') }}
                            </p>

                        @endif

                    </div>

                @endforeach

            </div>

        @else

            <div class="mt-5 text-center py-8">

                <p class="text-sm text-slate-400">
                    Belum ada riwayat maintenance.
                </p>

            </div>

        @endif

    </div>

    {{-- Downtime History --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">

        <div class="flex items-center justify-between border-b border-slate-100 pb-3">

            <h3 class="font-bold text-slate-800">
                Downtime
            </h3>

            <span class="text-xs text-slate-400">
                Riwayat
            </span>

        </div>

        @if($equipment->downtimes->count() > 0)

            <div class="mt-4 space-y-3">

                @foreach($equipment->downtimes->take(5) as $downtime)

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">

                        <div class="flex items-center justify-between gap-3">

                            <span class="text-sm font-semibold text-slate-700">
                                Downtime #{{ $downtime->id }}
                            </span>

                            @if(isset($downtime->status))

                                <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-amber-50 text-amber-700">
                                    {{ $downtime->status }}
                                </span>

                            @endif

                        </div>

                        @if(isset($downtime->created_at))

                            <p class="text-xs text-slate-400 mt-1">
                                {{ $downtime->created_at->format('d/m/Y H:i') }}
                            </p>

                        @endif

                    </div>

                @endforeach

            </div>

        @else

            <div class="mt-5 text-center py-8">

                <p class="text-sm text-slate-400">
                    Belum ada riwayat downtime.
                </p>

            </div>

        @endif

    </div>

</div>
```

</div>

@endsection