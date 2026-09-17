@extends('layouts.app')

@section('title', 'Executive Summary')
@section('page_title', 'Managerial Executive Summary')

@section('content')
<div class="space-y-6">
    <!-- Ringkasan Eksekutif KPI -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Laporan Masuk</span>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalTickets }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-emerald-500 uppercase tracking-wider block">Tingkat Penyelesaian (SLA)</span>
            <div class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $completionRate }}%</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-blue-500 uppercase tracking-wider block">Aset Equipment Aktif</span>
            <div class="text-3xl font-extrabold text-blue-600 mt-2">{{ $totalEquipment }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-purple-500 uppercase tracking-wider block">Katalog Spareparts</span>
            <div class="text-3xl font-extrabold text-purple-600 mt-2">{{ $totalSpareparts }}</div>
        </div>
    </div>
</div>
@endsection