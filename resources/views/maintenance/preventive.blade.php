@extends('layouts.app')

@section('title', 'Preventive Maintenance - Maintenance X')
@section('page_title', 'Preventive Maintenance')

@section('content')
<div class="space-y-6">
    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Jadwal Perawatan Berkala</h2>
            <p class="text-sm text-slate-500">Kelola aktivitas pemeliharaan pencegahan rutin (*preventive maintenance*).</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('maintenance.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-sm font-semibold transition-all">
                ← Maintenance Request
            </a>
            <button onclick="toggleModal('modalPreventive', true)" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-blue-600/20 transition-all">
                + Tambah Jadwal Rutin
            </button>
        </div>
    </div>

    {{-- Tabel Data Preventive Maintenance --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="p-4">Equipment / Mesin</th>
                        <th class="p-4">Aktivitas Perawatan</th>
                        <th class="p-4">Frekuensi</th>
                        <th class="p-4">Jadwal Berikutnya</th>
                        <th class="p-4">Teknisi / Engineer</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($preventives as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-4 font-bold text-slate-900">
                                {{ $item->equipment->name ?? '-' }}
                                <span class="block text-xs font-normal text-slate-400">{{ $item->equipment->code ?? '' }}</span>
                            </td>
                            <td class="p-4 font-medium text-slate-800">
                                {{ $item->title }}
                                @if($item->notes)
                                    <span class="block text-xs text-slate-400 mt-0.5">{{ Str::limit($item->notes, 40) }}</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 text-xs font-bold bg-blue-50 text-blue-700 rounded-md border border-blue-100 capitalize">
                                    {{ $item->frequency }}
                                </span>
                            </td>
                            <td class="p-4 font-semibold text-slate-800">
                                {{ \Carbon\Carbon::parse($item->next_maintenance_date)->format('d M Y') }}
                            </td>
                            <td class="p-4">
                                {{ $item->technician->username ?? 'Belum Ditunjuk' }}
                            </td>
                            <td class="p-4 text-center">
                                <form action="{{ route('maintenance.preventive.complete', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('Apakah pekerjaan perawatan ini sudah selesai?')" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold text-xs rounded-xl transition-all">
                                        ✓ Selesai
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                Belum ada jadwal pemeliharaan rutin yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Form Tambah Jadwal --}}
<div id="modalPreventive" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-lg p-6 m-4">
        <div class="flex justify-between items-center mb-5 pb-3 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-900">Tambah Jadwal Perawatan Rutin</h3>
            <button onclick="toggleModal('modalPreventive', false)" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form action="{{ route('maintenance.preventive.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Equipment / Mesin</label>
                <select name="equipment_id" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition">
                    <option value="">-- Pilih Equipment --</option>
                    @foreach($equipments as $eq)
                        <option value="{{ $eq->id }}">{{ $eq->name }} ({{ $eq->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Aktivitas / Judul Perawatan</label>
                <input type="text" name="title" required placeholder="Contoh: Pembersihan Filter & Pengecekan Oli" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Frekuensi</label>
                    <select name="frequency" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition">
                        <option value="daily">Harian (Daily)</option>
                        <option value="weekly">Mingguan (Weekly)</option>
                        <option value="monthly" selected>Bulanan (Monthly)</option>
                        <option value="yearly">Tahunan (Yearly)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Tanggal Rutin</label>
                    <input type="date" name="next_maintenance_date" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Teknisi Penanggung Jawab</label>
                <select name="assigned_to" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition">
                    <option value="">-- Pilih Teknisi (Opsional) --</option>
                    @foreach($engineers as $eng)
                        <option value="{{ $eng->id }}">{{ $eng->username }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Detail instruksi kerja..." class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="toggleModal('modalPreventive', false)" class="px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-blue-600/20 transition">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (show) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection