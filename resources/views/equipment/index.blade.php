@extends('layouts.app') 

@section('title', 'Equipment - Maintenance X') 
@section('page_title', 'Equipment') 

@section('content')
<div class="space-y-6">

    <!-- Header Section & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Equipment List</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola daftar equipment dan perangkat yang digunakan dalam maintenance.</p>
        </div>
        <button 
            type="button" 
            onclick="document.getElementById('equipmentModal').classList.remove('hidden'); document.getElementById('equipmentModal').classList.add('flex');"
            class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2 shrink-0"
        >
            <span class="text-lg leading-none">+</span> Add Equipment
        </button>
    </div>

    <!-- Main Card & Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs text-slate-400 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Code</th>
                        <th class="py-3.5 px-5">Name</th>
                        <th class="py-3.5 px-5">Location</th>
                        <th class="py-3.5 px-5 max-w-xs">Description</th>
                        <th class="py-3.5 px-5">Status</th>
                        <th class="py-3.5 px-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($equipment as $e)
                        @php
                            // Dynamic Styles for Status
                            $statusStyle = [
                                'ACTIVE' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'MAINTENANCE' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'INACTIVE' => 'bg-slate-100 text-slate-600 border-slate-200',
                            ][strtoupper($e->status)] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                        @endphp

                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-5 font-mono font-bold text-slate-900">
                                {{ $e->equipment_code }}
                            </td>
                            <td class="py-4 px-5 font-semibold text-slate-800">
                                {{ $e->name }}
                            </td>
                            <td class="py-4 px-5 text-slate-600">
                                {{ $e->location }}
                            </td>
                            <td class="py-4 px-5 text-slate-500 max-w-xs truncate" title="{{ $e->description }}">
                                {{ $e->description ?: '-' }}
                            </td>
                            <td class="py-4 px-5">
                                <span class="inline-block px-2.5 py-1 text-[11px] font-semibold rounded-full border {{ $statusStyle }}">
                                    {{ $e->status }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-right">
                                <form method="POST" action="{{ route('equipment.destroy', $e) }}" onsubmit="return confirm('Hapus equipment ini?')" class="inline-block">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-semibold rounded-lg transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-sm font-medium">
                                Belum ada equipment terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($equipment->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $equipment->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add Equipment Modal -->
<div id="equipmentModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden relative animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Add Equipment</h3>
                <p class="text-xs text-slate-500">Tambahkan data equipment baru ke dalam sistem.</p>
            </div>
            <button 
                type="button" 
                onclick="document.getElementById('equipmentModal').classList.remove('flex'); document.getElementById('equipmentModal').classList.add('hidden');" 
                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 transition"
            >
                ✕
            </button>
        </div>

        <!-- Modal Body Form -->
        <form method="POST" action="{{ route('equipment.store') }}" class="p-6 space-y-4">
            @csrf

            <!-- Equipment Code -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Equipment Code</label>
                <input 
                    type="text" 
                    name="equipment_code" 
                    placeholder="EQ-007" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm"
                >
            </div>

            <!-- Name -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Name</label>
                <input 
                    type="text" 
                    name="name" 
                    placeholder="Mesin Produksi 01" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm"
                >
            </div>

            <!-- Location -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Location</label>
                <input 
                    type="text" 
                    name="location" 
                    placeholder="Area Produksi" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm"
                >
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Description</label>
                <textarea 
                    name="description" 
                    rows="3" 
                    placeholder="Keterangan equipment (opsional)..." 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm"
                ></textarea>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Status</label>
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm">
                    <option value="ACTIVE" selected>ACTIVE</option>
                    <option value="MAINTENANCE">MAINTENANCE</option>
                    <option value="INACTIVE">INACTIVE</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-md transition">
                    Save Equipment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection