@extends('layouts.app')

@section('title', 'Dashboard Engineer - Maintenance X')
@section('page_title', 'Dashboard Saya')

@section('content')
@php
    $u    = auth()->user();
    $card = 'bg-white rounded-2xl border border-slate-200/80 shadow-sm';
@endphp

<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Halo, {{ $u->username }}</h2>
            <p class="text-sm text-slate-500 mt-1">Ringkasan pekerjaan dan request milik Anda.</p>
        </div>
        @if($u->hasPermission('maintenance'))
            <a href="{{ route('maintenance.index', ['open' => 1]) }}"
               class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-500/20 transition">
                + Buat Maintenance Request
            </a>
        @endif
    </div>

    {{-- KPI Work Order saya --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-5">
        <div class="{{ $card }} p-4 sm:p-5">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Siap dikerjakan</span>
            <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $stats['assigned'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Status ASSIGNED</p>
        </div>
        <div class="{{ $card }} p-4 sm:p-5">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">In Progress</span>
            <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $stats['in_progress'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Sedang dikerjakan</p>
        </div>
        <div class="{{ $card }} p-4 sm:p-5">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">On Hold</span>
            <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ $stats['on_hold'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Ditunda</p>
        </div>
        <div class="{{ $card }} p-4 sm:p-5">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Completed</span>
            <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $stats['completed'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Selesai</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- WO saya --}}
        <div class="{{ $card }} p-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800">Work Order Saya</h3>
                @if($u->hasPermission('maintenance'))
                    <a href="{{ route('work-orders.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat semua</a>
                @endif
            </div>

            @forelse($myWorkOrders as $wo)
                <a href="{{ route('work-orders.show', $wo) }}" class="flex flex-wrap items-center justify-between gap-2 py-3 border-b border-slate-50 last:border-0 hover:bg-slate-50 rounded-lg px-1">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800">{{ $wo->wo_number }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $wo->equipment->name ?? '-' }} &middot; {{ $wo->maintenance_type }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @include('partials.wo-badge', ['priority' => $wo->priority])
                        @include('partials.wo-badge', ['status' => $wo->status])
                    </div>
                </a>
            @empty
                <p class="text-sm text-slate-400 text-center py-10">Tidak ada Work Order aktif yang ditugaskan kepada Anda.</p>
            @endforelse
        </div>

        {{-- Request saya --}}
        <div class="{{ $card }} p-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800">Request Saya</h3>
                <div class="flex items-center gap-3 text-[11px] font-semibold">
                    <span class="text-amber-700">Menunggu {{ $requests['pending'] }}</span>
                    <span class="text-blue-700">Disetujui {{ $requests['approved'] }}</span>
                    <span class="text-rose-700">Ditolak {{ $requests['rejected'] }}</span>
                </div>
            </div>

            @forelse($myRequests as $mr)
                <a href="{{ route('maintenance.show', $mr) }}" class="flex flex-wrap items-center justify-between gap-2 py-3 border-b border-slate-50 last:border-0 hover:bg-slate-50 rounded-lg px-1">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800">Request #{{ $mr->id }} &middot; {{ $mr->equipment->name ?? '-' }}</p>
                        <p class="text-xs text-slate-400">{{ $mr->created_at->format('d/m/Y H:i') }}@if($mr->workOrder) &middot; {{ $mr->workOrder->wo_number }}@endif</p>
                    </div>
                    <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-slate-100 text-slate-600 whitespace-nowrap">{{ str_replace('_', ' ', $mr->status) }}</span>
                </a>
            @empty
                <p class="text-sm text-slate-400 text-center py-10">Anda belum pernah membuat request.</p>
            @endforelse
        </div>

        {{-- PM ditugaskan --}}
        <div class="{{ $card }} p-5">
            <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Jadwal Preventive Saya</h3>

            @forelse($myPreventives as $pm)
                <a href="{{ route('maintenance.preventive.show', $pm) }}" class="flex flex-wrap items-center justify-between gap-2 py-3 border-b border-slate-50 last:border-0 hover:bg-slate-50 rounded-lg px-1">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $pm->title }}</p>
                        <p class="text-xs text-slate-400">{{ $pm->equipment->name ?? '-' }} &middot; {{ $pm->next_maintenance_date?->format('d M Y') }}</p>
                    </div>
                    @include('partials.pm-badge', ['pm' => $pm])
                </a>
            @empty
                <p class="text-sm text-slate-400 text-center py-10">Tidak ada jadwal preventive untuk Anda.</p>
            @endforelse
        </div>

        {{-- Aktivitas saya + ticket --}}
        <div class="{{ $card }} p-5">
            <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Aktivitas Saya</h3>

            @forelse($myActivities as $act)
                <div class="flex items-start gap-3 py-3 border-b border-slate-50 last:border-0">
                    <span class="mt-1.5 w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
                    <div class="min-w-0">
                        <p class="text-sm text-slate-700 break-words">{{ $act->description }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $act->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400 text-center py-10">Belum ada aktivitas.</p>
            @endforelse

            @if($u->hasPermission('tickets'))
                <a href="{{ route('tickets.index') }}" class="mt-3 flex items-center justify-between text-sm bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-xl px-4 py-3 transition">
                    <span class="text-slate-600">Ticket (Incident) aktif untuk saya</span>
                    <span class="font-bold text-slate-800">{{ $myTickets }}</span>
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
