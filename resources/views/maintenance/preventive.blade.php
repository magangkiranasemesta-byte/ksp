@extends('layouts.app')

@section('title', 'Preventive Maintenance - Maintenance X')
@section('page_title', 'Preventive Maintenance')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Jadwal Perawatan Berkala</h2>
            <p class="text-sm text-slate-500 mt-1">
                Jadwal jatuh tempo → <span class="font-semibold text-slate-700">Generate Work Order</span> → selesai → jadwal berikutnya bergeser otomatis.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('maintenance.index') }}"
               class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-sm font-semibold rounded-xl transition">
                Maintenance Request
            </a>
            @can('create', \App\Models\PreventiveMaintenance::class)
                <button type="button" onclick="document.getElementById('pmModal').classList.remove('hidden')"
                        class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-500/20 transition">
                    + Tambah Jadwal
                </button>
            @endcan
        </div>
    </div>

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-sm">
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Ringkasan (klik untuk memfilter) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @foreach([
            ['Total Jadwal', $counts['total'], null, 'text-slate-900'],
            ['Overdue', $counts['overdue'], 'overdue', 'text-rose-600'],
            ['Due Soon (≤ ' . \App\Models\PreventiveMaintenance::DUE_SOON_DAYS . ' hari)', $counts['due_soon'], 'due_soon', 'text-amber-600'],
            ['Sedang Dikerjakan', $counts['in_progress'], 'in_progress', 'text-indigo-600'],
        ] as [$title, $count, $state, $color])
            <a href="{{ route('maintenance.preventive.index', array_filter(['state' => $state])) }}"
               class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-slate-300 transition">
                <p class="text-xs text-slate-400 font-medium">{{ $title }}</p>
                <p class="text-2xl font-bold mt-1 {{ $color }}">{{ $count }}</p>
            </a>
        @endforeach
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('maintenance.preventive.index') }}"
          class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_180px_180px_auto] gap-3">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari judul, equipment, technician…"
               class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
        <select name="frequency" class="px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
            <option value="">Semua frekuensi</option>
            @foreach(\App\Models\PreventiveMaintenance::FREQUENCIES as $key => $text)
                <option value="{{ $key }}" @selected(request('frequency') === $key)>{{ $text }}</option>
            @endforeach
        </select>
        <select name="state" class="px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
            <option value="">Semua kondisi</option>
            <option value="overdue" @selected(request('state') === 'overdue')>Overdue</option>
            <option value="due_soon" @selected(request('state') === 'due_soon')>Due soon</option>
            <option value="safe" @selected(request('state') === 'safe')>Aman</option>
            <option value="in_progress" @selected(request('state') === 'in_progress')>Sedang dikerjakan</option>
        </select>
        <div class="flex gap-2">
            <button class="flex-1 min-h-[44px] px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl transition">Filter</button>
            @if(request()->hasAny(['search', 'frequency', 'state']))
                <a href="{{ route('maintenance.preventive.index') }}" class="inline-flex items-center min-h-[44px] px-3 text-sm text-slate-500 hover:text-slate-800">Reset</a>
            @endif
        </div>
    </form>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[820px]">
                <thead>
                    <tr class="text-left text-xs text-slate-400 bg-slate-50/70 border-b border-slate-100">
                        <th class="py-3 px-5 font-semibold">Equipment</th>
                        <th class="py-3 px-5 font-semibold">Pekerjaan</th>
                        <th class="py-3 px-5 font-semibold">Frekuensi</th>
                        <th class="py-3 px-5 font-semibold">Terakhir</th>
                        <th class="py-3 px-5 font-semibold">Berikutnya</th>
                        <th class="py-3 px-5 font-semibold">Technician</th>
                        <th class="py-3 px-5 font-semibold">Status</th>
                        <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($preventives as $item)
                        <tr class="hover:bg-slate-50/60">
                            <td class="py-3.5 px-5">
                                <p class="font-semibold text-slate-800">{{ $item->equipment->name ?? '-' }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $item->equipment->equipment_code ?? '' }}</p>
                            </td>
                            <td class="py-3.5 px-5 max-w-[240px]">
                                <p class="font-medium text-slate-700 truncate">{{ $item->title }}</p>
                                @if($item->notes)<p class="text-xs text-slate-400 truncate">{{ \Illuminate\Support\Str::limit($item->notes, 50) }}</p>@endif
                            </td>
                            <td class="py-3.5 px-5 text-slate-600">{{ \App\Models\PreventiveMaintenance::FREQUENCIES[$item->frequency] ?? $item->frequency }}</td>
                            <td class="py-3.5 px-5 text-slate-500 whitespace-nowrap">{{ $item->last_maintenance_date?->format('d M Y') ?? '-' }}</td>
                            <td class="py-3.5 px-5 text-slate-700 font-medium whitespace-nowrap">{{ $item->next_maintenance_date?->format('d M Y') ?? '-' }}</td>
                            <td class="py-3.5 px-5 text-slate-600">{{ $item->technician->username ?? '-' }}</td>
                            <td class="py-3.5 px-5">@include('partials.pm-badge', ['pm' => $item])</td>
                            <td class="py-3.5 px-5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('maintenance.preventive.show', $item) }}"
                                       class="inline-flex items-center min-h-[36px] px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">Detail</a>

                                    @if($item->status !== 'in_progress')
                                        @can('generateWorkOrder', $item)
                                            <form method="POST" action="{{ route('maintenance.preventive.work-order', $item) }}"
                                                  onsubmit="return confirm('Buat Work Order dari jadwal ini?')">
                                                @csrf
                                                <button class="min-h-[36px] px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition whitespace-nowrap">Generate WO</button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-14 text-center">
                                <p class="text-sm font-semibold text-slate-600">Belum ada jadwal yang cocok</p>
                                <p class="text-xs text-slate-400 mt-1">Ubah filter pencarian atau tambahkan jadwal baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 border-t border-slate-100">
            @include('partials.pagination', ['paginator' => $preventives])
        </div>
    </div>
</div>

{{-- Modal tambah jadwal --}}
@can('create', \App\Models\PreventiveMaintenance::class)
    <div id="pmModal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/50 p-0 sm:p-4"
         onclick="if (event.target === this) this.classList.add('hidden')">
        <div class="bg-white w-full sm:max-w-2xl max-h-[92vh] overflow-y-auto rounded-t-2xl sm:rounded-2xl shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white">
                <h3 class="font-bold text-slate-800">Tambah Jadwal Preventive</h3>
                <button type="button" class="w-10 h-10 text-slate-400 hover:text-slate-700 text-xl" aria-label="Tutup"
                        onclick="document.getElementById('pmModal').classList.add('hidden')">&times;</button>
            </div>
            <form method="POST" action="{{ route('maintenance.preventive.store') }}" class="p-5 space-y-5">
                @csrf
                @include('preventive._fields')
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <button type="button" onclick="document.getElementById('pmModal').classList.add('hidden')"
                            class="min-h-[44px] px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl">Batal</button>
                    <button class="min-h-[44px] px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    @if($errors->any() && old('title') !== null)
        <script>document.getElementById('pmModal').classList.remove('hidden');</script>
    @endif
@endcan
@endsection
