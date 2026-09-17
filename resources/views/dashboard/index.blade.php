@extends('layouts.app')

@section('title', 'Dashboard - Maintenance X')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Header Action / Welcome Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Selamat Datang di Maintenance X</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pantau status perangkat dan aktivitas pemeliharaan secara real-time.</p>
        </div>
        <a href="{{ route('maintenance.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition-all shadow-md shadow-blue-500/20 whitespace-nowrap">
            <span class="text-lg leading-none mr-1.5">+</span> Request Maintenance
        </a>
    </div>

    <!-- Top Grid: Cards + Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Stats Cards (7 cols) -->
        <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- Card 1: System Overview -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 text-slate-800 font-bold mb-1">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <h3>Equipment</h3>
                    </div>
                    <p class="text-xs text-slate-400">Total aset terdaftar di sistem</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-3xl font-bold text-slate-900">{{ $totalEquipment ?? 3 }}</span>
                        <span class="block text-xs text-slate-500 font-medium">Unit perangkat</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Card 2: Active / In Progress Stat -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 text-slate-800 font-bold mb-1">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <h3>Sedang Diproses</h3>
                    </div>
                    <p class="text-xs text-slate-400">Maintenance aktif berjalan</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-3xl font-bold text-slate-900">{{ $inProgressCount ?? 0 }}</span>
                        <span class="block text-xs text-slate-500 font-medium">Approved / In Progress</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Card 3: Pending Approval -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-2xl font-bold text-slate-900">{{ $pendingCount ?? 1 }}</span>
                    <span class="block text-xs text-slate-500 font-medium">Menunggu approval</span>
                </div>
            </div>

            <!-- Card 4: Completed -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span class="text-2xl font-bold text-slate-900">{{ $completedCount ?? 0 }}</span>
                    <span class="block text-xs text-slate-500 font-medium">Maintenance selesai</span>
                </div>
            </div>
        </div>

        <!-- Right Chart: Maintenance Trend (5 cols) -->
        <div class="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Maintenance Trend</h3>
                <p class="text-xs text-slate-400">Aktivitas 12 bulan tahun {{ date('Y') }}</p>
            </div>

            <!-- Simulated Bar Chart -->
            <div class="mt-6">
                <div class="h-44 flex items-end justify-between gap-1 border-b border-slate-200 pb-2 px-1">
                    @php
                        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    @endphp
                    @foreach($months as $m)
                        <div class="flex-1 flex flex-col items-center gap-1 group relative">
                            @if($m === 'Jul')
                                <span class="text-[10px] font-bold text-blue-600">1</span>
                                <div class="w-full bg-blue-600 rounded-t-sm h-28 shadow-md shadow-blue-500/20"></div>
                            @else
                                <span class="text-[10px] text-slate-300 opacity-0 group-hover:opacity-100">0</span>
                                <div class="w-full bg-slate-100 rounded-t-sm h-1 hover:bg-slate-200 transition"></div>
                            @endif
                            <span class="text-[10px] font-medium text-slate-400 mt-1">{{ $m }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Approval History Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Approval History</h3>
            <p class="text-xs text-slate-400">Aktivitas approval terbaru</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs text-slate-400 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">ID</th>
                        <th class="py-3 px-5">Equipment</th>
                        <th class="py-3 px-5">Engineer</th>
                        <th class="py-3 px-5">Priority</th>
                        <th class="py-3 px-5">Status</th>
                        <th class="py-3 px-5">Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="py-8 text-center text-xs text-slate-400 font-medium italic">
                            Belum ada approval history.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 3: Recent Maintenance Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Recent Maintenance</h3>
                <p class="text-xs text-slate-400">Request terbaru</p>
            </div>
            <a href="{{ route('tickets.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-1">
                Lihat semua tiket &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs text-slate-400 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">ID</th>
                        <th class="py-3 px-5">Equipment</th>
                        <th class="py-3 px-5">Engineer</th>
                        <th class="py-3 px-5">Priority</th>
                        <th class="py-3 px-5">Status</th>
                        <th class="py-3 px-5">Dibuat</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3.5 px-5 font-bold text-blue-600">#1</td>
                        <td class="py-3.5 px-5">
                            <span class="font-semibold text-slate-800 block">Generator 01</span>
                            <span class="text-[11px] text-slate-400 font-mono">EQ-003</span>
                        </td>
                        <td class="py-3.5 px-5 font-medium text-slate-700">engineer</td>
                        <td class="py-3.5 px-5">
                            <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded bg-red-100 text-red-700 uppercase">HIGH</span>
                        </td>
                        <td class="py-3.5 px-5">
                            <span class="inline-block px-2.5 py-1 text-[11px] font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200/60">
                                PENDING SUPERVISOR
                            </span>
                        </td>
                        <td class="py-3.5 px-5 text-xs text-slate-400">28/08/2026 11:07</td>
                        <td class="py-3.5 px-5 text-right whitespace-nowrap">
                            <a href="{{ route('tickets.show', 1) }}" class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                Detail
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection