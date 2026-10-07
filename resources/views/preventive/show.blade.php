@extends('layouts.app')

@section('title', 'Detail Jadwal Preventive')
@section('page_title', 'Detail Preventive Maintenance')

@section('content')
<div class="space-y-6">
    <a href="{{ route('maintenance.preventive.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Kembali ke Preventive Maintenance</a>

    {{-- Header --}}
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-xl font-bold text-slate-900 break-words">{{ $preventive->title }}</h2>
                @include('partials.pm-badge', ['pm' => $preventive])
            </div>
            <p class="text-sm text-slate-500 mt-1">
                {{ $preventive->equipment->name ?? '-' }}
                <span class="font-mono text-xs text-slate-400">{{ $preventive->equipment->equipment_code ?? '' }}</span>
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @can('update', $preventive)
                <a href="{{ route('maintenance.preventive.edit', $preventive) }}"
                   class="inline-flex items-center min-h-[44px] px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl">Edit</a>
            @endcan

            @if($openWorkOrder)
                <a href="{{ route('work-orders.show', $openWorkOrder) }}"
                   class="inline-flex items-center min-h-[44px] px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-xl">
                    Buka {{ $openWorkOrder->wo_number }}
                </a>
            @else
                @can('generateWorkOrder', $preventive)
                    <form method="POST" action="{{ route('maintenance.preventive.work-order', $preventive) }}"
                          onsubmit="return confirm('Buat Work Order dari jadwal ini?')">
                        @csrf
                        <button class="min-h-[44px] px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl">Generate Work Order</button>
                    </form>
                @endcan
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- Informasi --}}
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Informasi Jadwal</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 text-sm">
                    <div><dt class="text-xs text-slate-400">Frekuensi</dt><dd class="font-semibold text-slate-800">{{ \App\Models\PreventiveMaintenance::FREQUENCIES[$preventive->frequency] ?? $preventive->frequency }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Technician</dt><dd class="font-semibold text-slate-800">{{ $preventive->technician->username ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Maintenance terakhir</dt><dd class="font-semibold text-slate-800">{{ $preventive->last_maintenance_date?->format('d M Y') ?? '-' }}</dd></div>
                    <div><dt class="text-xs text-slate-400">Maintenance berikutnya</dt><dd class="font-semibold text-slate-800">{{ $preventive->next_maintenance_date?->format('d M Y') ?? '-' }}</dd></div>
                </dl>
                @if($preventive->notes)
                    <div class="mt-4 bg-slate-50 border border-slate-100 rounded-xl p-4 text-sm text-slate-600 whitespace-pre-line">{{ $preventive->notes }}</div>
                @endif
            </div>

            {{-- Riwayat --}}
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Riwayat Maintenance</h3>

                @forelse($preventive->logs as $log)
                    <div class="py-3 border-b border-slate-50 last:border-0 text-sm">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-semibold text-slate-800">{{ $log->performed_at->format('d M Y H:i') }}</p>
                            @if($log->workOrder)
                                <a href="{{ route('work-orders.show', $log->workOrder) }}" class="text-xs font-semibold text-blue-600 hover:underline">{{ $log->workOrder->wo_number }}</a>
                            @else
                                <span class="text-xs text-slate-400">Manual</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Oleh {{ $log->performer->username ?? '-' }}
                            @if($log->due_date) &middot; jadwal {{ $log->due_date->format('d M Y') }} @endif
                            @if($log->next_maintenance_date) &middot; berikutnya {{ $log->next_maintenance_date->format('d M Y') }} @endif
                        </p>
                        @if($log->notes)<p class="text-slate-600 mt-1 whitespace-pre-line break-words">{{ $log->notes }}</p>@endif
                    </div>
                @empty
                    <p class="text-sm text-slate-400 text-center py-8">Belum ada riwayat pelaksanaan.</p>
                @endforelse
            </div>
        </div>

        {{-- Sisi kanan --}}
        <div class="space-y-6">
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Work Order Terkait</h3>
                @forelse($preventive->workOrders as $wo)
                    <a href="{{ route('work-orders.show', $wo) }}" class="flex items-center justify-between gap-2 py-2.5 text-sm hover:bg-slate-50 rounded-lg px-1">
                        <span class="font-semibold text-slate-700">{{ $wo->wo_number }}</span>
                        <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-slate-100 text-slate-600">{{ str_replace('_', ' ', $wo->status) }}</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-400 text-center py-6">Belum ada Work Order.</p>
                @endforelse
            </div>

            {{-- Penyelesaian manual --}}
            @if(! $openWorkOrder)
                @can('complete', $preventive)
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                        <h3 class="font-bold text-slate-800">Catat Selesai Manual</h3>
                        <p class="text-xs text-slate-400 mt-1">Untuk pekerjaan yang dilakukan tanpa Work Order. Jadwal berikutnya dihitung dari hari ini.</p>
                        <form method="POST" action="{{ route('maintenance.preventive.complete', $preventive) }}" class="mt-3 space-y-3"
                              onsubmit="return confirm('Catat jadwal ini selesai hari ini?')">
                            @csrf
                            @method('PATCH')
                            <textarea name="notes" rows="2" maxlength="2000" placeholder="Catatan (opsional)"
                                      class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm"></textarea>
                            <button class="w-full min-h-[44px] px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl">Tandai Selesai</button>
                        </form>
                    </div>
                @endcan
            @endif
        </div>
    </div>
</div>
@endsection
