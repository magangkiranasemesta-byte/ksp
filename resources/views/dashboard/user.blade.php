@extends('layouts.app')

@section('title', 'Portal Pelaporan')
@section('page_title', 'Status Pengajuan Laporan Saya')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Laporan Dikirim</span>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $myReportedCount }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-blue-500 uppercase tracking-wider block">Sedang Diproses</span>
            <div class="text-3xl font-extrabold text-blue-600 mt-2">{{ $myActiveCount }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-emerald-500 uppercase tracking-wider block">Selesai</span>
            <div class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $myCompletedCount }}</div>
        </div>
    </div>
</div>
@endsection