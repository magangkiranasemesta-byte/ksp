@extends('layouts.app')

@section('title', 'Detail Request #' . $ticket->id)
@section('page_title', 'Detail Maintenance Request')

@section('content')
<div class="space-y-6">
    <!-- Navigation Back -->
    <div>
        <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <!-- Header Detail -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-slate-900">Request #{{ $ticket->id }}</h2>
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200/60">
                    {{ $ticket->status }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Dibuat pada {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <!-- Tombol Aksi Persetujuan/Update (Bisa disesuaikan role) -->
        <div class="flex items-center gap-2">
            <button class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs transition">
                Approve
            </button>
            <button class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl text-xs transition">
                Reject
            </button>
        </div>
    </div>

    <!-- Grid Informasi Utama -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Detail Info (Left 2 Cols) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Informasi Masalah</h3>
            
            <div>
                <span class="text-xs text-slate-400 block font-medium">Judul / Perangkat</span>
                <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ $ticket->equipment_name ?? 'Generator 01' }}</p>
            </div>

            <div>
                <span class="text-xs text-slate-400 block font-medium">Deskripsi Kerusakan</span>
                <p class="text-sm text-slate-600 mt-1 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    {{ $ticket->description ?? 'Tidak ada deskripsi detail yang dicantumkan.' }}
                </p>
            </div>
        </div>

        <!-- Meta Info (Right 1 Col) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Informasi Tambahan</h3>

            <div>
                <span class="text-xs text-slate-400 block font-medium">Engineer / Pelapor</span>
                <p class="text-sm font-medium text-slate-700 mt-0.5">{{ $ticket->engineer_name ?? 'engineer' }}</p>
            </div>

            <div>
                <span class="text-xs text-slate-400 block font-medium">Prioritas</span>
                <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold rounded bg-red-100 text-red-700 uppercase">
                    {{ $ticket->priority ?? 'HIGH' }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection