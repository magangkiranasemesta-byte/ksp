@extends('layouts.app')

@section('title', 'Activity Logs - Maintenance X')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Activity & Audit Logs</h1>
            <p class="text-sm text-slate-500 mt-1">Rekam jejak seluruh aktivitas dan perubahan data dalam sistem untuk transparansi dan keamanan.</p>
        </div>

        <!-- Filter Modul -->
        <form method="GET" action="{{ route('activity-logs.index') }}" class="flex items-center gap-2">
            <select name="module" onchange="this.form.submit()" class="bg-white border border-slate-300 text-slate-700 text-sm rounded-xl px-3.5 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                <option value="">Semua Modul</option>
                <option value="ticket" {{ request('module') == 'ticket' ? 'selected' : '' }}>Ticket</option>
                <option value="maintenance_request" {{ request('module') == 'maintenance_request' ? 'selected' : '' }}>Maintenance Request</option>
                <option value="preventive_maintenance" {{ request('module') == 'preventive_maintenance' ? 'selected' : '' }}>Preventive Maintenance</option>
                <option value="equipment" {{ request('module') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                <option value="user" {{ request('module') == 'user' ? 'selected' : '' }}>User</option>
            </select>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold text-xs uppercase tracking-wider">
                        <th class="py-3.5 px-4">Waktu</th>
                        <th class="py-3.5 px-4">Pengguna</th>
                        <th class="py-3.5 px-4">Modul</th>
                        <th class="py-3.5 px-4">Aktivitas</th>
                        <th class="py-3.5 px-4">Detail Perubahan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Waktu -->
                            <td class="py-3.5 px-4 whitespace-nowrap text-xs">
                                <span class="font-medium text-slate-900 block">{{ $log->created_at->format('d M Y, H:i') }}</span>
                                <span class="text-slate-400 text-[11px]">{{ $log->created_at->diffForHumans() }}</span>
                            </td>

                            <!-- User/Causer -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-700 uppercase">
                                        {{ substr($log->causer->username ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-semibold text-slate-900 block text-xs">{{ $log->causer->username ?? 'System / Anonymous' }}</span>
                                        <span class="text-slate-400 text-[10px]">{{ $log->causer->role ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Modul -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wider">
                                    {{ str_replace('_', ' ', $log->log_name) }}
                                </span>
                            </td>

                            <!-- Event Description -->
                            <td class="py-3.5 px-4 text-xs font-medium text-slate-800">
                                {{ $log->description }}
                            </td>

                            <!-- Perubahan Data -->
                            <td class="py-3.5 px-4 text-xs">
                                @if(!empty($log->properties['attributes']))
                                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200 font-mono text-[11px] space-y-1 max-w-xs overflow-x-auto">
                                        @if(!empty($log->properties['old']))
                                            <div class="text-red-600">
                                                <span class="font-bold">Sebelum:</span>
                                                <pre class="inline whitespace-pre-wrap">{{ json_encode($log->properties['old'], JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        @endif
                                        <div class="text-emerald-600">
                                            <span class="font-bold">Sesudah:</span>
                                            <pre class="inline whitespace-pre-wrap">{{ json_encode($log->properties['attributes'], JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-sm">
                                Belum ada riwayat aktivitas yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection