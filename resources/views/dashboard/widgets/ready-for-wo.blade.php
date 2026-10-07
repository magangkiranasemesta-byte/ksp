@isset($d['readyForWo'])
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="font-bold text-slate-800">Request Siap Dibuat Work Order</h3>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $d['readyForWo']->count() ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-400' }}">{{ $d['readyForWo']->count() }}</span>
    </div>
    @forelse($d['readyForWo'] as $mr)
        <div class="flex flex-wrap items-center justify-between gap-2 py-3 border-b border-slate-50 last:border-0">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-800">#{{ $mr->id }} &middot; {{ $mr->equipment->name ?? '-' }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $mr->engineer->username ?? '-' }} &middot; disetujui {{ $mr->updated_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center gap-2">
                @include('partials.wo-badge', ['priority' => $mr->priority])
                @can('create', \App\Models\WorkOrder::class)
                    <a href="{{ route('work-orders.create', ['maintenance_request_id' => $mr->id]) }}" class="inline-flex items-center min-h-[36px] px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg whitespace-nowrap">Buat WO</a>
                @endcan
            </div>
        </div>
    @empty
        <p class="text-sm text-slate-400 text-center py-10">Semua request yang disetujui sudah memiliki Work Order.</p>
    @endforelse
</div>
@endisset
