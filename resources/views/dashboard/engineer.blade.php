```blade
@extends('layouts.app')

@section('title', 'Engineer Workstation')
@section('page_title', 'Tugas & Perbaikan Saya')

@section('content')
<div class="space-y-6">

    {{-- =========================
         STATUS PEKERJAAN
    ========================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

        <div class="bg-white p-5 rounded-2xl border border-amber-100 shadow-sm bg-gradient-to-br from-amber-50/50 to-white">
            <span class="text-xs font-bold text-amber-600 uppercase tracking-wider block">
                Tugas Menunggu Kategori
            </span>
            <div class="text-3xl font-extrabold text-amber-600 mt-2">
                {{ $myPendingTickets }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-blue-100 shadow-sm bg-gradient-to-br from-blue-50/50 to-white">
            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block">
                Sedang Saya Kerjakan
            </span>
            <div class="text-3xl font-extrabold text-blue-600 mt-2">
                {{ $myActiveTickets }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-sm bg-gradient-to-br from-emerald-50/50 to-white">
            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block">
                Selesai Ditangani
            </span>
            <div class="text-3xl font-extrabold text-emerald-600 mt-2">
                {{ $myDoneTickets }}
            </div>
        </div>

    </div>


    {{-- =========================
         SPAREPART
    ========================== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">

            <div>
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center text-xl">
                        📦
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            Sparepart
                        </h3>

                        <p class="text-xs text-slate-400 mt-0.5">
                            Kelola dan catat penggunaan sparepart saat melakukan perbaikan.
                        </p>
                    </div>
                </div>
            </div>

            <a href="{{ route('sparepart-usages.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition">
                <span>+</span>
                Catat Pemakaian
            </a>

        </div>


        {{-- MENU SPAREPART --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <a href="{{ route('spareparts.index') }}"
               class="group p-4 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50/50 transition">

                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        📦
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-bold text-slate-800 group-hover:text-blue-600">
                            Data Sparepart
                        </p>

                        <p class="text-xs text-slate-400 mt-0.5">
                            Lihat daftar dan stok sparepart
                        </p>
                    </div>

                    <span class="text-slate-400 group-hover:text-blue-600">
                        →
                    </span>

                </div>

            </a>


            <a href="{{ route('sparepart-usages.index') }}"
               class="group p-4 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/50 transition">

                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                        🧾
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-bold text-slate-800 group-hover:text-emerald-600">
                            Riwayat Pemakaian
                        </p>

                        <p class="text-xs text-slate-400 mt-0.5">
                            Lihat sparepart yang sudah digunakan
                        </p>
                    </div>

                    <span class="text-slate-400 group-hover:text-emerald-600">
                        →
                    </span>

                </div>

            </a>

        </div>

    </div>


    {{-- =========================
         DAFTAR TUGAS TIKET AKTIF
    ========================== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">

        <div class="flex items-center justify-between mb-4">

            <h3 class="text-base font-bold text-slate-900">
                Daftar Penugasan Tiket
            </h3>

            <a href="{{ route('tickets.index') }}"
               class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                Lihat Semua →
            </a>

        </div>

        <div class="space-y-3">

            @forelse($myTickets as $ticket)

                <div class="flex items-center justify-between p-4 rounded-xl border border-slate-100 hover:border-slate-200 transition bg-slate-50/30">

                    <div>

                        <span class="text-xs font-mono font-bold text-blue-600">
                            #{{ $ticket->id }}
                        </span>

                        <h4 class="text-sm font-semibold text-slate-800 mt-0.5">
                            {{ $ticket->title ?? 'Laporan Maintenance' }}
                        </h4>

                        <span class="text-xs text-slate-400">
                            Dibuat: {{ $ticket->created_at->diffForHumans() }}
                        </span>

                    </div>

                    <a href="{{ route('tickets.show', $ticket->id) }}"
                       class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs">
                        Kerjakan
                    </a>

                </div>

            @empty

                <div class="text-center py-8 text-slate-400 text-sm">
                    Tidak ada tugas perbaikan aktif yang ditugaskan kepada Anda.
                </div>

            @endforelse

        </div>

    </div>

</div>
@endsection
```
