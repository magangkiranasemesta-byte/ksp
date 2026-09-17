@extends('layouts.app')

@section('title', 'Supervisor Control')
@section('page_title', 'Pengawasan Tim & Assignment')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Tiket Masuk</span>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalTickets }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-amber-500 uppercase tracking-wider block">Belum Dilengkapi Teknisi</span>
            <div class="text-3xl font-extrabold text-amber-600 mt-2">{{ $pendingTickets }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-blue-500 uppercase tracking-wider block">Dalam Pengerjaan</span>
            <div class="text-3xl font-extrabold text-blue-600 mt-2">{{ $inProgressTickets }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-emerald-500 uppercase tracking-wider block">Selesai Ditinjau</span>
            <div class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $completedTickets }}</div>
        </div>
    </div>
</div>
@endsection