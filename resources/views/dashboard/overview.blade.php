@extends('layouts.app')

@section('title', 'Dashboard - Maintenance X')
@section('page_title', 'Dashboard Overview')

@section('content')
@php
    $u    = auth()->user();
    $card = 'bg-white rounded-2xl border border-slate-200/80 shadow-sm';
    $th   = 'py-3 px-4 font-semibold';
    $td   = 'py-3 px-4';

    $pendingLink = $u->hasPermission('maintenance')
        ? route('maintenance.index', $attention['pending_filter'] ? ['status' => $attention['pending_filter']] : ['search' => 'PENDING'])
        : null;
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Kondisi Maintenance Saat Ini</h2>
            <p class="text-sm text-slate-500 mt-1">Data langsung dari database &middot; {{ now()->translatedFormat('d F Y, H:i') }}</p>
        </div>
        @if($u->hasPermission('maintenance'))
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('maintenance.index') }}" class="inline-flex items-center min-h-[40px] px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">Maintenance Request</a>
                <a href="{{ route('work-orders.index') }}" class="inline-flex items-center min-h-[40px] px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-500/20 transition">Work Order</a>
            </div>
        @endif
    </div>

    {{-- KPI --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-5">
        <div class="{{ $card }} p-4 sm:p-5">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total WO</span>
            <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $kpi['total_wo'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Semua Work Order</p>
        </div>
        <div class="{{ $card }} p-4 sm:p-5">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">In Progress</span>
            <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $kpi['in_progress'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Sedang dikerjakan</p>
        </div>
        <div class="{{ $card }} p-4 sm:p-5">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Completed</span>
            <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $kpi['completed'] }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $kpi['completion_rate'] }}% dari total</p>
        </div>
        <div class="{{ $card }} p-4 sm:p-5">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Downtime</span>
            <p class="text-3xl font-extrabold {{ $kpi['active_downtime'] > 0 ? 'text-rose-600' : 'text-slate-800' }} mt-1">{{ $kpi['active_downtime'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Equipment sedang down</p>
        </div>
    </div>

    {{-- Perlu perhatian --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-5">
        @php
            $tiles = [
                ['Menunggu approval', $attention['pending_approval'], $pendingLink, 'text-amber-600'],
                ['WO belum di-assign', $attention['unassigned'], $u->hasPermission('maintenance') ? route('work-orders.index', ['status' => 'OPEN']) : null, 'text-slate-700'],
                ['PM overdue', $attention['pm_overdue'], $u->hasPermission('maintenance') ? route('maintenance.preventive.index', ['state' => 'overdue']) : null, 'text-rose-600'],
                ['Stok menipis', $attention['low_stock'], $u->hasPermission('spareparts') ? route('spareparts.index') : null, 'text-orange-600'],
            ];
        @endphp

        @foreach($tiles as [$label, $value, $href, $color])
            @if($href)
                <a href="{{ $href }}" class="{{ $card }} p-4 hover:border-slate-300 transition block">
            @else
                <div class="{{ $card }} p-4">
            @endif
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500">{{ $label }}</span>
                    <span class="text-2xl font-bold {{ $value > 0 ? $color : 'text-slate-300' }}">{{ $value }}</span>
                </div>
            @if($href)
                </a>
            @else
                </div>
            @endif
        @endforeach
    </div>

    {{-- WO terbaru + grafik --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <div class="{{ $card }} xl:col-span-2 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Work Order Terbaru</h3>
                @if($u->hasPermission('maintenance'))
                    <a href="{{ route('work-orders.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat semua</a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[720px]">
                    <thead>
                        <tr class="text-left text-xs text-slate-400 bg-slate-50/70 border-b border-slate-100">
                            <th class="{{ $th }}">WO</th>
                            <th class="{{ $th }}">Equipment</th>
                            <th class="{{ $th }}">Tipe</th>
                            <th class="{{ $th }}">Prioritas</th>
                            <th class="{{ $th }}">Technician</th>
                            <th class="{{ $th }}">Status</th>
                            <th class="{{ $th }}">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentWorkOrders as $wo)
                            <tr class="hover:bg-slate-50/60">
                                <td class="{{ $td }} font-semibold whitespace-nowrap">
                                    @if($u->hasPermission('maintenance'))
                                        <a href="{{ route('work-orders.show', $wo) }}" class="text-blue-600 hover:underline">{{ $wo->wo_number }}</a>
                                    @else
                                        {{ $wo->wo_number }}
                                    @endif
                                </td>
                                <td class="{{ $td }} text-slate-700">{{ $wo->equipment->name ?? '-' }}</td>
                                <td class="{{ $td }} text-slate-500 text-xs">{{ $wo->maintenance_type }}</td>
                                <td class="{{ $td }}">@include('partials.wo-badge', ['priority' => $wo->priority])</td>
                                <td class="{{ $td }} text-slate-600">{{ $wo->technician->username ?? '-' }}</td>
                                <td class="{{ $td }}">@include('partials.wo-badge', ['status' => $wo->status])</td>
                                <td class="{{ $td }} text-slate-500 whitespace-nowrap">{{ $wo->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-12 text-center text-sm text-slate-400">Belum ada Work Order.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Grafik status WO --}}
        <div class="{{ $card }} p-5">
            <h3 class="font-bold text-slate-800 mb-4">Status Work Order</h3>

            <div class="flex flex-col items-center gap-5">
                <div class="relative w-40 h-40 rounded-full shrink-0" style="background: {{ $chart['gradient'] }}" role="img"
                     aria-label="Grafik status Work Order, total {{ $chart['total'] }}">
                    <div class="absolute inset-[22px] rounded-full bg-white flex flex-col items-center justify-center">
                        <span class="text-3xl font-extrabold text-slate-800 leading-none">{{ $chart['total'] }}</span>
                        <span class="text-[11px] text-slate-400 mt-1">Work Order</span>
                    </div>
                </div>

                <ul class="w-full space-y-2">
                    @foreach($chart['segments'] as $seg)
                        <li class="flex items-center justify-between gap-3 text-sm">
                            <span class="flex items-center gap-2 text-slate-600 min-w-0">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background: {{ $seg['color'] }}"></span>
                                <span class="truncate">{{ $seg['label'] }}</span>
                            </span>
                            <span class="font-semibold text-slate-800 whitespace-nowrap">
                                {{ $seg['count'] }}
                                <span class="text-xs font-normal text-slate-400">({{ $seg['percent'] }}%)</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- PM + Downtime --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        <div class="{{ $card }} overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-2 px-5 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Maintenance Akan Jatuh Tempo</h3>
                <div class="flex items-center gap-3 text-[11px] font-semibold">
                    <span class="flex items-center gap-1 text-emerald-700"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Aman {{ $pmSummary['safe'] }}</span>
                    <span class="flex items-center gap-1 text-amber-700"><span class="w-2 h-2 rounded-full bg-amber-400"></span>Segera {{ $pmSummary['due_soon'] }}</span>
                    <span class="flex items-center gap-1 text-rose-700"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Overdue {{ $pmSummary['overdue'] }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[560px]">
                    <thead>
                        <tr class="text-left text-xs text-slate-400 bg-slate-50/70 border-b border-slate-100">
                            <th class="{{ $th }}">Equipment</th>
                            <th class="{{ $th }}">Berikutnya</th>
                            <th class="{{ $th }}">Technician</th>
                            <th class="{{ $th }}">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($upcomingPm as $pm)
                            <tr class="hover:bg-slate-50/60">
                                <td class="{{ $td }}">
                                    @if($u->hasPermission('maintenance'))
                                        <a href="{{ route('maintenance.preventive.show', $pm) }}" class="font-semibold text-slate-800 hover:text-blue-600">{{ $pm->equipment->name ?? '-' }}</a>
                                    @else
                                        <span class="font-semibold text-slate-800">{{ $pm->equipment->name ?? '-' }}</span>
                                    @endif
                                    <p class="text-xs text-slate-400 truncate max-w-[200px]">{{ $pm->title }}</p>
                                </td>
                                <td class="{{ $td }} text-slate-600 whitespace-nowrap">{{ $pm->next_maintenance_date?->format('d M Y') ?? '-' }}</td>
                                <td class="{{ $td }} text-slate-600">{{ $pm->technician->username ?? '-' }}</td>
                                <td class="{{ $td }}">@include('partials.pm-badge', ['pm' => $pm])</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-12 text-center text-sm text-slate-400">Belum ada jadwal Preventive Maintenance.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="{{ $card }} overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Equipment Downtime</h3>
                @if($u->hasPermission('tickets'))
                    <a href="{{ route('downtime.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat semua</a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[520px]">
                    <thead>
                        <tr class="text-left text-xs text-slate-400 bg-slate-50/70 border-b border-slate-100">
                            <th class="{{ $th }}">Equipment</th>
                            <th class="{{ $th }}">Mulai</th>
                            <th class="{{ $th }}">Durasi</th>
                            <th class="{{ $th }}">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($downtimes as $d)
                            <tr class="hover:bg-slate-50/60">
                                <td class="{{ $td }}">
                                    <p class="font-semibold text-slate-800">{{ $d->equipment->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-400 truncate max-w-[200px]">{{ $d->reason }}</p>
                                </td>
                                <td class="{{ $td }} text-slate-600 whitespace-nowrap">{{ $d->started_at->format('d/m/Y H:i') }}</td>
                                <td class="{{ $td }} text-slate-600 whitespace-nowrap">{{ $d->duration }}</td>
                                <td class="{{ $td }}">
                                    <span class="inline-block px-2.5 py-1 text-[11px] font-semibold rounded-full border {{ $d->status === 'ONGOING' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                                        {{ $d->status === 'ONGOING' ? 'Berjalan' : 'Selesai' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-12 text-center text-sm text-slate-400">Belum ada data downtime.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Aktivitas + ringkasan sistem --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <div class="{{ $card }} xl:col-span-2 p-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-1">
                <h3 class="font-bold text-slate-800">Aktivitas Terbaru</h3>
                @if($u->hasPermission('activity_logs'))
                    <a href="{{ route('activity-logs.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat semua</a>
                @endif
            </div>

            @forelse($activities as $act)
                <div class="flex items-start gap-3 py-3 border-b border-slate-50 last:border-0">
                    <span class="mt-1.5 w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-slate-700 break-words">
                            <span class="font-semibold text-slate-900">{{ $act->causer->username ?? 'Sistem' }}</span>
                            &middot; {{ $act->description }}
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ str_replace('_', ' ', $act->log_name ?? 'system') }}
                            @if($act->event) &middot; {{ $act->event }} @endif
                            &middot; {{ $act->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400 text-center py-10">Belum ada aktivitas tercatat.</p>
            @endforelse
        </div>

        <div class="space-y-6">
            <div class="{{ $card }} p-5">
                <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Ringkasan Equipment</h3>
                <dl class="mt-3 space-y-2 text-sm">
                    @foreach(['ACTIVE' => 'text-emerald-600', 'MAINTENANCE' => 'text-amber-600', 'INACTIVE' => 'text-slate-500'] as $st => $color)
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">{{ $st }}</dt>
                            <dd class="font-bold {{ $color }}">{{ (int) ($equipmentSummary[$st] ?? 0) }}</dd>
                        </div>
                    @endforeach
                    @if($totalUsers !== null)
                        <div class="flex items-center justify-between border-t border-slate-100 pt-2 mt-2">
                            <dt class="text-slate-500">Total user</dt>
                            <dd class="font-bold text-slate-800">{{ $totalUsers }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="{{ $card }} p-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-800">Ticket (Incident)</h3>
                    @if($u->hasPermission('tickets'))
                        <a href="{{ route('tickets.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Buka</a>
                    @endif
                </div>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex items-center justify-between"><dt class="text-slate-500">Baru</dt><dd class="font-bold text-slate-800">{{ $ticketSummary['open'] }}</dd></div>
                    <div class="flex items-center justify-between"><dt class="text-slate-500">Ditangani</dt><dd class="font-bold text-indigo-600">{{ $ticketSummary['active'] }}</dd></div>
                    <div class="flex items-center justify-between"><dt class="text-slate-500">Selesai</dt><dd class="font-bold text-emerald-600">{{ $ticketSummary['done'] }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
