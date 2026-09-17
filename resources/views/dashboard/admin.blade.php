@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Operational Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Peringatan Stok Menipis -->
    @if(isset($lowStockSpareparts) && $lowStockSpareparts->count() > 0)
        <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-5 flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 font-bold text-lg">⚠️</div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-amber-900">Peringatan: Stok Sparepart Menipis!</h3>
                <p class="text-xs text-amber-700 mt-0.5">Beberapa stok telah mencapai atau berada di bawah batas minimum pemesanan.</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($lowStockSpareparts as $sp)
                        <span class="bg-white border border-amber-300 text-amber-900 px-3 py-1 rounded-lg text-xs font-semibold shadow-xs">
                            {{ $sp->name }} <strong class="text-rose-600">(Sisa: {{ $sp->stock }})</strong>
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Tiket Masuk</span>
            <div class="text-3xl font-extrabold text-slate-900 mt-2">{{ $totalTickets }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-amber-500 uppercase tracking-wider block">Tiket Pending</span>
            <div class="text-3xl font-extrabold text-amber-600 mt-2">{{ $pendingTickets }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-blue-500 uppercase tracking-wider block">Sedang Dikerjakan</span>
            <div class="text-3xl font-extrabold text-blue-600 mt-2">{{ $inProgressTickets }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-emerald-500 uppercase tracking-wider block">Tiket Selesai</span>
            <div class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $completedTickets }}</div>
        </div>
    </div>
</div>
@endsection