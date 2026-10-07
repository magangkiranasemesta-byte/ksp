@isset($d['activeWorkOrders'])
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
    <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Pekerjaan Sedang Berjalan</h3>
    @forelse($d['activeWorkOrders'] as $wo)
        <a href="{{ route('work-orders.show', $wo) }}" class="flex flex-wrap items-center justify-between gap-2 py-3 border-b border-slate-50 last:border-0 hover:bg-slate-50 rounded-lg px-1">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-800">{{ $wo->wo_number }} &middot; {{ $wo->equipment->name ?? '-' }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $wo->technician->username ?? '-' }} &middot; mulai {{ optional($wo->actual_start)->diffForHumans() ?? '-' }}</p>
            </div>
            <div class="flex items-center gap-2">
                @include('partials.wo-badge', ['priority' => $wo->priority])
                @include('partials.wo-badge', ['status' => $wo->status])
            </div>
        </a>
    @empty
        <p class="text-sm text-slate-400 text-center py-10">Tidak ada pekerjaan yang sedang berjalan.</p>
    @endforelse
</div>
@endisset
