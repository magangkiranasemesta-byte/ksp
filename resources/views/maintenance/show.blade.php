@extends('layouts.app')

@section('title', 'Detail Request #' . $maintenanceRequest->id)
@section('page_title', 'Detail Maintenance Request')

@section('content')
@php
    $mr = $maintenanceRequest;

    $statusStyle = [
        'PENDING_SUPERVISOR' => 'bg-amber-50 text-amber-700 border-amber-200',
        'PENDING_MANAGER'    => 'bg-purple-50 text-purple-700 border-purple-200',
        'APPROVED'           => 'bg-blue-50 text-blue-700 border-blue-200',
        'REJECTED'           => 'bg-rose-50 text-rose-700 border-rose-200',
        'IN_PROGRESS'        => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'COMPLETED'          => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    ][$mr->status] ?? 'bg-slate-50 text-slate-700 border-slate-200';

    $priorityStyle = [
        'LOW'      => 'bg-slate-100 text-slate-700',
        'MEDIUM'   => 'bg-blue-50 text-blue-700',
        'HIGH'     => 'bg-amber-50 text-amber-700',
        'CRITICAL' => 'bg-red-100 text-red-700',
    ][strtoupper($mr->priority)] ?? 'bg-slate-100 text-slate-700';
@endphp

<div class="space-y-6">

    <div>
        <a href="{{ route('maintenance.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            &larr; Kembali ke Maintenance Request
        </a>
    </div>

    {{-- Header --}}
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-xl font-bold text-slate-900">Request #{{ $mr->id }}</h2>
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusStyle }}">
                    {{ str_replace('_', ' ', $mr->status) }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Dibuat pada {{ $mr->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @can('approve', $mr)
                <form method="POST" action="{{ route('maintenance.approve', $mr) }}"
                      onsubmit="return confirm('Setujui Maintenance Request #{{ $mr->id }}?')">
                    @csrf
                    <button class="min-h-[44px] px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm transition">
                        Approve
                    </button>
                </form>
            @endcan

            @can('reject', $mr)
                <form method="POST" action="{{ route('maintenance.reject', $mr) }}"
                      onsubmit="var r = prompt('Alasan penolakan (min. 5 karakter):'); if (!r || r.trim().length < 5) { alert('Alasan penolakan wajib diisi (min. 5 karakter).'); return false; } this.note.value = r.trim(); return true;">
                    @csrf
                    <input type="hidden" name="note" value="">
                    <button class="min-h-[44px] px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold rounded-xl text-sm transition">
                        Reject
                    </button>
                </form>
            @endcan

            @if($mr->status === 'APPROVED')
                @if($mr->workOrder)
                    <a href="{{ route('work-orders.show', $mr->workOrder) }}"
                       class="inline-flex items-center min-h-[44px] px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-xl text-sm transition">
                        Lihat {{ $mr->workOrder->wo_number }}
                    </a>
                @else
                    @can('create', \App\Models\WorkOrder::class)
                        <a href="{{ route('work-orders.create', ['maintenance_request_id' => $mr->id]) }}"
                           class="inline-flex items-center min-h-[44px] px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition">
                            Buat Work Order
                        </a>
                    @endcan
                @endif
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Informasi --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Informasi Masalah</h3>

                <div>
                    <span class="text-xs text-slate-400 block font-medium">Equipment</span>
                    <p class="text-sm font-semibold text-slate-800 mt-0.5">
                        {{ $mr->equipment->name ?? '-' }}
                        <span class="text-xs text-slate-400 font-mono">{{ $mr->equipment->equipment_code ?? '' }}</span>
                    </p>
                </div>

                <div>
                    <span class="text-xs text-slate-400 block font-medium">Deskripsi</span>
                    <p class="text-sm text-slate-600 mt-1 bg-slate-50 p-4 rounded-xl border border-slate-100 whitespace-pre-line">{{ $mr->description }}</p>
                </div>
            </div>

            {{-- Riwayat approval --}}
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3 mb-4">Riwayat Approval</h3>

                <ol class="space-y-4">
                    <li class="flex gap-3">
                        <span class="mt-1 w-2.5 h-2.5 rounded-full bg-slate-300 shrink-0"></span>
                        <div class="text-sm">
                            <p class="font-semibold text-slate-800">Request dibuat</p>
                            <p class="text-xs text-slate-400">
                                {{ $mr->engineer->username ?? '-' }} &middot; {{ $mr->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </li>

                    @foreach($mr->approvals as $history)
                        @php $isReject = $history->action === 'REJECT'; @endphp
                        <li class="flex gap-3">
                            <span class="mt-1 w-2.5 h-2.5 rounded-full shrink-0 {{ $isReject ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                            <div class="text-sm min-w-0">
                                <p class="font-semibold {{ $isReject ? 'text-rose-700' : 'text-emerald-700' }}">
                                    {{ $isReject ? 'Ditolak' : 'Disetujui' }} oleh {{ $history->role }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ $history->user->username ?? '-' }} &middot; {{ optional($history->created_at)->format('d/m/Y H:i') }}
                                </p>
                                @if($history->note)
                                    <p class="mt-1 text-slate-600 bg-slate-50 border border-slate-100 rounded-lg px-3 py-2 break-words">
                                        {{ $history->note }}
                                    </p>
                                @endif
                            </div>
                        </li>
                    @endforeach

                    @if($mr->approvals->isEmpty())
                        <li class="text-xs text-slate-400 pl-5">Belum ada keputusan approval.</li>
                    @endif
                </ol>
            </div>
        </div>

        {{-- Meta --}}
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4 h-fit">
            <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Informasi Tambahan</h3>

            <div>
                <span class="text-xs text-slate-400 block font-medium">Engineer / Pelapor</span>
                <p class="text-sm font-medium text-slate-700 mt-0.5">{{ $mr->engineer->username ?? '-' }}</p>
            </div>

            <div>
                <span class="text-xs text-slate-400 block font-medium">Prioritas</span>
                <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold rounded uppercase {{ $priorityStyle }}">
                    {{ $mr->priority }}
                </span>
            </div>

            <div>
                <span class="text-xs text-slate-400 block font-medium">Work Order</span>
                @if($mr->workOrder)
                    <a href="{{ route('work-orders.show', $mr->workOrder) }}" class="text-sm font-semibold text-blue-600 hover:underline">
                        {{ $mr->workOrder->wo_number }}
                    </a>
                    <p class="text-xs text-slate-400">{{ str_replace('_', ' ', $mr->workOrder->status) }}</p>
                @else
                    <p class="text-sm text-slate-500 mt-0.5">Belum dibuat</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
