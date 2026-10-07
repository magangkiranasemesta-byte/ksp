@isset($d['unassignedWo'])
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="font-bold text-slate-800">Work Order Belum Di-assign</h3>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $d['unassignedWo']->count() ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-400' }}">{{ $d['unassignedWo']->count() }}</span>
    </div>
    @forelse($d['unassignedWo'] as $wo)
        <div class="flex flex-wrap items-center justify-between gap-2 py-3 border-b border-slate-50 last:border-0">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-800">{{ $wo->wo_number }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $wo->equipment->name ?? '-' }} &middot; {{ $wo->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center gap-2">
                @include('partials.wo-badge', ['priority' => $wo->priority])
                @can('assign', $wo)
                    <a href="{{ route('work-orders.show', $wo) }}" class="inline-flex items-center min-h-[36px] px-3 py-1 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg whitespace-nowrap">Assign</a>
                @endcan
            </div>
        </div>
    @empty
        <p class="text-sm text-slate-400 text-center py-10">Semua Work Order sudah memiliki technician.</p>
    @endforelse
</div>
@endisset
