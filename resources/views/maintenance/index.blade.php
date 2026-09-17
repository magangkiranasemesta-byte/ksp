@extends('layouts.app') 

@section('title', 'Maintenance - Maintenance X') 
@section('page_title', 'Maintenance') 

@section('content')
<div class="space-y-6">

    <!-- Header Section & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Maintenance Request</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola request dan alur approval: <span class="font-semibold text-slate-700">Engineer → Supervisor → Manager</span>.</p>
        </div>
        <button 
            type="button" 
            onclick="document.getElementById('requestModal').classList.remove('hidden'); document.getElementById('requestModal').classList.add('flex');"
            class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2 shrink-0"
        >
            <span class="text-lg leading-none">+</span> Request Maintenance
        </button>
    </div>

    <!-- Main Card: Search & Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        
        <!-- Search Bar -->
        <div class="p-5 border-b border-slate-100 bg-slate-50/50">
            <form method="GET" class="flex items-center gap-2 max-w-md">
                <div class="relative flex-1">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search maintenance..."
                        class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent text-sm bg-white"
                    >
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl transition">
                    Search
                </button>
            </form>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs text-slate-400 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">ID</th>
                        <th class="py-3.5 px-5">Equipment</th>
                        <th class="py-3.5 px-5">Engineer</th>
                        <th class="py-3.5 px-5 max-w-xs">Description</th>
                        <th class="py-3.5 px-5">Priority</th>
                        <th class="py-3.5 px-5">Status</th>
                        <th class="py-3.5 px-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $r)
                        @php
                            // Dynamic Styles for Priority
                            $pStyle = [
                                'LOW' => 'bg-slate-100 text-slate-700 border-slate-200',
                                'MEDIUM' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'HIGH' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'CRITICAL' => 'bg-red-50 text-red-700 border-red-200 font-bold',
                            ][strtoupper($r->priority)] ?? 'bg-slate-100 text-slate-700 border-slate-200';

                            // Dynamic Styles for Status
                            $sStyle = [
                                'PENDING_SUPERVISOR' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'PENDING_MANAGER' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'APPROVED' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'IN_PROGRESS' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'COMPLETED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200',
                            ][strtoupper($r->status)] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                        @endphp

                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-5 font-bold text-slate-900">#{{ $r->id }}</td>
                            <td class="py-4 px-5">
                                <span class="font-semibold text-slate-800 block">{{ $r->equipment->name ?? '-' }}</span>
                                <span class="text-xs text-slate-400 font-mono">{{ $r->equipment->equipment_code ?? '' }}</span>
                            </td>
                            <td class="py-4 px-5 text-slate-700 font-medium">
                                {{ $r->engineer->username ?? '-' }}
                            </td>
                            <td class="py-4 px-5 text-slate-600 max-w-xs truncate" title="{{ $r->description }}">
                                {{ $r->description }}
                            </td>
                            <td class="py-4 px-5">
                                <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold tracking-wider rounded border {{ $pStyle }}">
                                    {{ $r->priority }}
                                </span>
                            </td>
                            <td class="py-4 px-5">
                                <span class="inline-block px-2.5 py-1 text-[11px] font-semibold rounded-full border {{ $sStyle }}">
                                    {{ str_replace('_', ' ', $r->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- SUPERVISOR APPROVAL --}}
                                    @if(strtoupper(auth()->user()->role) === 'SUPERVISOR' && $r->status === 'PENDING_SUPERVISOR')
                                        <form method="POST" action="{{ route('maintenance.status', $r) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="PENDING_MANAGER">
                                            <button class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('maintenance.status', $r) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="REJECTED">
                                            <button class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">Reject</button>
                                        </form>

                                    {{-- MANAGER APPROVAL --}}
                                    @elseif(strtoupper(auth()->user()->role) === 'MANAGER' && $r->status === 'PENDING_MANAGER')
                                        <form method="POST" action="{{ route('maintenance.status', $r) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="APPROVED">
                                            <button class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('maintenance.status', $r) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="REJECTED">
                                            <button class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">Reject</button>
                                        </form>

                                    {{-- ENGINEER START --}}
                                    @elseif(strtoupper(auth()->user()->role) === 'ENGINEER' && $r->engineer_id === auth()->id() && $r->status === 'APPROVED')
                                        <form method="POST" action="{{ route('maintenance.status', $r) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="IN_PROGRESS">
                                            <button class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">Start</button>
                                        </form>

                                    {{-- ENGINEER COMPLETE --}}
                                    @elseif(strtoupper(auth()->user()->role) === 'ENGINEER' && $r->engineer_id === auth()->id() && $r->status === 'IN_PROGRESS')
                                        <form method="POST" action="{{ route('maintenance.status', $r) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="COMPLETED">
                                            <button class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">Complete</button>
                                        </form>

                                    @else
                                        <span class="text-slate-300 font-bold px-2">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                Belum ada request maintenance.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($requests->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Request Maintenance Modal -->
<div id="requestModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden relative animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Request Maintenance</h3>
                <p class="text-xs text-slate-500">Request baru otomatis masuk status <b class="text-slate-700">PENDING SUPERVISOR</b>.</p>
            </div>
            <button 
                type="button" 
                onclick="document.getElementById('requestModal').classList.remove('flex'); document.getElementById('requestModal').classList.add('hidden');" 
                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 transition"
            >
                ✕
            </button>
        </div>

        <!-- Modal Body Form -->
        <form method="POST" action="{{ route('maintenance.store') }}" class="p-6 space-y-4">
            @csrf

            <!-- Select Equipment -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Equipment</label>
                <select name="equipment_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm">
                    <option value="">Pilih equipment</option>
                    @foreach($equipment as $e)
                        <option value="{{ $e->id }}">{{ $e->equipment_code }} — {{ $e->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Select Engineer (If Admin/Supervisor/Manager) -->
            @if(strtoupper(auth()->user()->role) !== 'ENGINEER')
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Engineer</label>
                    <select name="engineer_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm">
                        <option value="">Gunakan user login</option>
                        @foreach($engineers as $e)
                            <option value="{{ $e->id }}">{{ $e->username }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Select Priority -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Priority</label>
                <select name="priority" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm">
                    <option value="LOW">LOW</option>
                    <option value="MEDIUM" selected>MEDIUM</option>
                    <option value="HIGH">HIGH</option>
                    <option value="CRITICAL">CRITICAL</option>
                </select>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi</label>
                <textarea 
                    name="description" 
                    rows="3" 
                    placeholder="Jelaskan masalah equipment..." 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm"
                ></textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-md transition">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection