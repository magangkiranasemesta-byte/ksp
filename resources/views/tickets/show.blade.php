@extends('layouts.app') {{-- Sesuaikan lokasi layout utama Anda --}}

@section('title', 'Detail Tiket #' . $ticket->ticket_number . ' - Maintenance X')
@section('page_title', 'Tickets')

@section('content')
<div class="space-y-6">
    
    <!-- Header Tiket -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 text-xs font-bold rounded-full uppercase bg-blue-100 text-blue-800">
                    {{ $ticket->ticket_number }}
                </span>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full 
                    {{ $ticket->priority == 'urgent' ? 'bg-red-100 text-red-700' : '' }}
                    {{ $ticket->priority == 'high' ? 'bg-orange-100 text-orange-700' : '' }}
                    {{ $ticket->priority == 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $ticket->priority == 'low' ? 'bg-slate-100 text-slate-700' : '' }}">
                    Prioritas: {{ strtoupper($ticket->priority) }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-2">{{ $ticket->title }}</h1>
            <p class="text-sm text-slate-500 mt-1">
                Dilaporkan oleh <span class="font-semibold text-slate-700">{{ $ticket->reporter->name ?? 'Pengguna' }}</span> pada {{ $ticket->created_at->format('d M Y, H:i') }}
            </p>
        </div>

        <!-- Panel Status Saat Ini -->
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex items-center space-x-4">
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase">Status Saat Ini</p>
                <p class="text-lg font-bold text-blue-600 uppercase">{{ str_replace('_', ' ', $ticket->status) }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri (Detail & Log Perbaikan) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Card Detail Laporan -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Detail Perangkat & Masalah</h3>
                
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-slate-500">Perangkat</p>
                        <p class="font-semibold text-slate-800">{{ $ticket->device->name ?? 'Dihapus' }} ({{ $ticket->device->asset_number ?? '-' }})</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Lokasi</p>
                        <p class="font-semibold text-slate-800">{{ $ticket->device->location ?? '-' }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-slate-500 mb-1">Deskripsi Kerusakan:</p>
                    <div class="p-4 bg-slate-50 rounded-xl text-slate-700 text-sm whitespace-pre-line border border-slate-100">
                        {{ $ticket->description }}
                    </div>
                </div>

                @if($ticket->image_proof)
                    <div>
                        <p class="text-sm text-slate-500 mb-2">Foto Bukti Kerusakan:</p>
                        <a href="{{ asset('storage/' . $ticket->image_proof) }}" target="_blank" class="inline-block">
                            <img src="{{ asset('storage/' . $ticket->image_proof) }}" alt="Bukti Kerusakan" class="h-48 rounded-xl object-cover border border-slate-200 hover:opacity-95 transition-opacity">
                        </a>
                    </div>
                @endif
            </div>

            <!-- Card Form Tambah Log Tindakan Perbaikan (Teknisi) -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Tambah Catatan Perbaikan (Log)</h3>
                
                <form action="{{ route('tickets.addLog', $ticket->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tindakan Perbaikan</label>
                        <textarea name="action_taken" rows="3" required placeholder="Contoh: Mengganti RAM DDR4 8GB dan membersihkan kipas..." class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Jenis Perbaikan</label>
                            <select name="maintenance_type" class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                <option value="corrective">Corrective (Perbaikan Kerusakan)</option>
                                <option value="preventive">Preventive (Perawatan Rutin)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Biaya Perbaikan (Rp)</label>
                            <input type="number" name="cost" min="0" placeholder="0" class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                        </div>
                    </div>

                    <button type="submit" class="px-4 py-2.5 text-sm font-semibold text-white bg-slate-800 rounded-xl hover:bg-slate-900 transition-all shadow-sm">
                        Simpan Log Perbaikan
                    </button>
                </form>

                <!-- List Log Perbaikan -->
                <div class="mt-6 space-y-3 pt-4 border-t border-slate-100">
                    <h4 class="text-sm font-bold text-slate-700">Riwayat Perbaikan Fisik:</h4>
                    @forelse($ticket->logs as $log)
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-sm space-y-1">
                            <div class="flex justify-between font-semibold text-slate-800">
                                <span>{{ $log->technician->name ?? 'Teknisi' }}</span>
                                <span class="text-xs text-slate-500 font-normal">{{ $log->completed_at ? $log->completed_at->format('d/m/Y H:i') : $log->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="text-slate-600">{{ $log->action_taken }}</p>
                            <div class="text-xs text-slate-500 flex justify-between pt-1">
                                <span>Tipe: <strong class="uppercase text-slate-700">{{ $log->maintenance_type }}</strong></span>
                                <span>Biaya: <strong class="text-slate-700">Rp {{ number_format($log->cost, 0, ',', '.') }}</strong></span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 italic">Belum ada tindakan perbaikan yang dicatat.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Kolom Kanan (Update Status & Timeline Histori) -->
        <div class="space-y-6">
            
            <!-- Panel Update Status Tiket -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Aksi Tiket</h3>
                
                <form action="{{ route('tickets.status', $ticket->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Ubah Status Tiket</label>
                        <select name="status" class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="pending_sparepart" {{ $ticket->status == 'pending_sparepart' ? 'selected' : '' }}>Pending Sparepart</option>
                            <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved (Selesai)</option>
                            <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="cancelled" {{ $ticket->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Catatan Perubahan</label>
                        <input type="text" name="notes" placeholder="Alasan update status..." class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                    </div>
                    <button type="submit" class="w-full py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-500/20">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Timeline Tracking Status -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3">Tracking Status</h3>
                
                <div class="relative border-l-2 border-slate-200 ml-3 space-y-6">
                    @foreach($ticket->statusHistories as $history)
                        <div class="ml-4 relative">
                            <div class="absolute -left-[23px] top-1.5 w-3 h-3 bg-blue-600 rounded-full border-2 border-white"></div>
                            <p class="text-xs text-slate-400">{{ $history->created_at->format('d M Y, H:i') }}</p>
                            <p class="text-sm font-bold text-slate-800 uppercase mt-0.5">
                                {{ str_replace('_', ' ', $history->new_status) }}
                            </p>
                            <p class="text-xs text-slate-600">Oleh: {{ $history->user->name ?? 'Sistem' }}</p>
                            @if($history->notes)
                                <p class="text-xs text-slate-500 italic mt-1 bg-slate-50 p-2 rounded-lg border border-slate-100">"{{ $history->notes }}"</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>
</div>
@endsection