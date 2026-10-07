@extends('layouts.app')

@section('title', 'Equipment Detail - ' . $equipment->equipment_code)
@section('page_title', 'Equipment Detail')

@section('content')
@php
    $statusStyle = [
        'ACTIVE'      => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'MAINTENANCE' => 'bg-amber-50 text-amber-700 border-amber-200',
        'INACTIVE'    => 'bg-slate-100 text-slate-600 border-slate-200',
    ][strtoupper($equipment->status)] ?? 'bg-slate-50 text-slate-700 border-slate-200';

    $woStyle = [
        'OPEN' => 'bg-slate-100 text-slate-600', 'ASSIGNED' => 'bg-blue-50 text-blue-700',
        'IN_PROGRESS' => 'bg-indigo-50 text-indigo-700', 'ON_HOLD' => 'bg-amber-50 text-amber-700',
        'COMPLETED' => 'bg-emerald-50 text-emerald-700', 'CANCELLED' => 'bg-rose-50 text-rose-700',
    ];

    $fmtMinutes = function (int $m) {
        $d = intdiv($m, 1440); $h = intdiv($m % 1440, 60); $min = $m % 60;
        return trim(($d ? "{$d} hari " : '') . ($h ? "{$h} jam " : '') . ($min || ! ($d || $h) ? "{$min} menit" : ''));
    };

    $card = 'bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm';
@endphp

<div class="space-y-6">

    <a href="{{ route('equipment.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800">
        &larr; Kembali ke Equipment
    </a>

    {{-- Header --}}
    <div class="{{ $card }} flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-2xl font-bold text-slate-900 break-words">{{ $equipment->name }}</h2>
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusStyle }}">{{ $equipment->status }}</span>
                @if($equipment->ongoingDowntime)
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border bg-rose-50 text-rose-700 border-rose-200">Downtime berjalan</span>
                @endif
            </div>
            <p class="mt-2 text-sm text-slate-500">
                Kode: <span class="font-mono font-bold text-slate-700">{{ $equipment->equipment_code }}</span>
                &middot; {{ $equipment->location }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('equipment.edit', $equipment) }}"
               class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">Edit</a>
            @can('create', \App\Models\MaintenanceRequest::class)
                <a href="{{ route('maintenance.index', ['equipment_id' => $equipment->id, 'open' => 1]) }}"
                   class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-semibold rounded-xl transition">Ajukan Request</a>
            @endcan
            <a href="{{ route('equipment.qr', $equipment) }}" target="_blank"
               class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-500/20 transition">QR Code</a>
        </div>
    </div>

    {{-- Kondisi saat ini --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 {{ $card }}">
            <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Kondisi Saat Ini</h3>

            <div class="mt-4 space-y-4">
                {{-- Work Order aktif --}}
                <div>
                    <p class="text-xs text-slate-400 font-medium mb-1.5">Work Order Aktif</p>
                    @if($equipment->currentWorkOrder)
                        @php $cw = $equipment->currentWorkOrder; @endphp
                        <a href="{{ route('work-orders.show', $cw) }}" class="flex flex-wrap items-center justify-between gap-2 p-3 bg-indigo-50/60 border border-indigo-100 rounded-xl hover:bg-indigo-50 transition">
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $cw->wo_number }}</p>
                                <p class="text-xs text-slate-500">Technician: {{ $cw->technician->username ?? '-' }} &middot; mulai {{ optional($cw->actual_start)->format('d M Y H:i') ?? '-' }}</p>
                            </div>
                            <span class="text-[10px] font-semibold px-2 py-1 rounded-full {{ $woStyle[$cw->status] ?? 'bg-slate-100 text-slate-600' }}">{{ str_replace('_', ' ', $cw->status) }}</span>
                        </a>
                    @else
                        <p class="text-sm text-slate-500">Tidak ada Work Order yang sedang dikerjakan.</p>
                    @endif
                </div>

                {{-- Downtime aktif --}}
                <div>
                    <p class="text-xs text-slate-400 font-medium mb-1.5">Downtime Aktif</p>
                    @if($equipment->ongoingDowntime)
                        @php $dt = $equipment->ongoingDowntime; @endphp
                        <a href="{{ route('downtime.show', $dt) }}" class="block p-3 bg-rose-50/60 border border-rose-100 rounded-xl hover:bg-rose-50 transition">
                            <p class="text-sm font-bold text-slate-800">{{ $dt->reason }}</p>
                            <p class="text-xs text-slate-500">Sejak {{ $dt->started_at->format('d M Y H:i') }} &middot; {{ $dt->duration }}</p>
                        </a>
                    @else
                        <p class="text-sm text-slate-500">Tidak ada downtime berjalan.</p>
                    @endif
                </div>

                <dl class="grid grid-cols-2 gap-4 text-sm pt-2">
                    <div><dt class="text-xs text-slate-400">Total Downtime</dt><dd class="font-semibold text-slate-800">{{ $fmtMinutes($totalDowntimeMinutes) }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Jadwal Preventive</dt><dd class="font-semibold text-slate-800">{{ $equipment->preventiveMaintenances->count() }}</dd></div>
                </dl>
            </div>

            @if($equipment->description)
                <div class="mt-5 bg-slate-50 p-4 rounded-xl border border-slate-100 text-sm text-slate-600">{{ $equipment->description }}</div>
            @endif
        </div>

        {{-- Preventive --}}
        <div class="{{ $card }}">
            <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Preventive Maintenance</h3>
            @forelse($equipment->preventiveMaintenances as $pm)
                <a href="{{ route('maintenance.preventive.show', $pm) }}" class="block py-3 border-b border-slate-50 last:border-0 hover:bg-slate-50 rounded-lg px-1">
                    <p class="text-sm font-semibold text-slate-700 truncate">{{ $pm->title }}</p>
                    <div class="flex flex-wrap items-center justify-between gap-2 mt-1">
                        <span class="text-xs text-slate-400">{{ $pm->next_maintenance_date?->format('d M Y') }}</span>
                        @include('partials.pm-badge', ['pm' => $pm])
                    </div>
                </a>
            @empty
                <p class="text-sm text-slate-400 text-center py-8">Belum ada jadwal preventive.</p>
            @endforelse
        </div>
    </div>

    {{-- Riwayat --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        <div class="{{ $card }}">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800">Riwayat Work Order</h3>
                <span class="text-xs text-slate-400">10 terbaru</span>
            </div>
            @forelse($workOrders as $wo)
                <a href="{{ route('work-orders.show', $wo) }}" class="flex flex-wrap items-center justify-between gap-2 py-3 border-b border-slate-50 last:border-0 hover:bg-slate-50 rounded-lg px-1">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-700">{{ $wo->wo_number }}
                            <span class="text-xs font-normal text-slate-400">{{ $wo->maintenance_type }}</span></p>
                        <p class="text-xs text-slate-400">{{ $wo->technician->username ?? '-' }} &middot; {{ $wo->created_at->format('d/m/Y') }}</p>
                    </div>
                    <span class="text-[10px] font-semibold px-2 py-1 rounded-full {{ $woStyle[$wo->status] ?? 'bg-slate-100 text-slate-600' }}">{{ str_replace('_', ' ', $wo->status) }}</span>
                </a>
            @empty
                <p class="text-sm text-slate-400 text-center py-8">Belum ada Work Order.</p>
            @endforelse
        </div>

        <div class="{{ $card }}">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800">Riwayat Downtime</h3>
                <span class="text-xs text-slate-400">10 terbaru</span>
            </div>
            @forelse($downtimes as $d)
                <a href="{{ route('downtime.show', $d) }}" class="flex flex-wrap items-center justify-between gap-2 py-3 border-b border-slate-50 last:border-0 hover:bg-slate-50 rounded-lg px-1">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $d->reason }}</p>
                        <p class="text-xs text-slate-400">{{ $d->started_at->format('d/m/Y H:i') }} &middot; {{ $d->duration }}</p>
                    </div>
                    <span class="text-[10px] font-semibold px-2 py-1 rounded-full {{ $d->status === 'ONGOING' ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $d->status }}</span>
                </a>
            @empty
                <p class="text-sm text-slate-400 text-center py-8">Belum ada riwayat downtime.</p>
            @endforelse
        </div>

        <div class="{{ $card }}">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800">Maintenance Request</h3>
                <span class="text-xs text-slate-400">5 terbaru</span>
            </div>
            @forelse($maintenanceRequests as $mr)
                <a href="{{ route('maintenance.show', $mr) }}" class="flex flex-wrap items-center justify-between gap-2 py-3 border-b border-slate-50 last:border-0 hover:bg-slate-50 rounded-lg px-1">
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Request #{{ $mr->id }}</p>
                        <p class="text-xs text-slate-400">{{ $mr->created_at->format('d/m/Y H:i') }}@if($mr->workOrder) &middot; {{ $mr->workOrder->wo_number }}@endif</p>
                    </div>
                    <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-slate-100 text-slate-600">{{ str_replace('_', ' ', $mr->status) }}</span>
                </a>
            @empty
                <p class="text-sm text-slate-400 text-center py-8">Belum ada request.</p>
            @endforelse
        </div>

        <div class="{{ $card }}">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800">Sparepart Terpakai</h3>
                <span class="text-xs text-slate-400">8 terbaru</span>
            </div>
            @forelse($sparepartUsages as $u)
                <div class="flex flex-wrap items-center justify-between gap-2 py-3 border-b border-slate-50 last:border-0">
                    <div>
                        <p class="text-sm font-semibold text-slate-700">{{ $u->sparepart->name ?? '-' }}</p>
                        <p class="text-xs text-slate-400">{{ optional($u->used_at)->format('d/m/Y') }} &middot; {{ $u->workOrder->wo_number ?? '' }}</p>
                    </div>
                    <span class="text-sm font-semibold text-slate-600">{{ $u->quantity }} {{ $u->sparepart->unit ?? '' }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-400 text-center py-8">Belum ada pemakaian sparepart.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
