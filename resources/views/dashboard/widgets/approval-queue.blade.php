@isset($d['approvalQueue'])
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="font-bold text-slate-800">{{ $d['approvalQueueTitle'] ?? 'Menunggu Approval' }}</h3>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $d['approvalQueue']->count() ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-400' }}">{{ $d['approvalQueue']->count() }}</span>
    </div>
    @forelse($d['approvalQueue'] as $mr)
        <a href="{{ route('maintenance.show', $mr) }}" class="flex flex-wrap items-center justify-between gap-2 py-3 border-b border-slate-50 last:border-0 hover:bg-slate-50 rounded-lg px-1">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-800">#{{ $mr->id }} &middot; {{ $mr->equipment->name ?? '-' }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $mr->engineer->username ?? '-' }} &middot; {{ $mr->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center gap-2">
                @include('partials.wo-badge', ['priority' => $mr->priority])
                <span class="text-xs font-semibold text-blue-600">Review &rarr;</span>
            </div>
        </a>
    @empty
        <p class="text-sm text-slate-400 text-center py-10">Tidak ada request yang menunggu keputusan Anda.</p>
    @endforelse
</div>
@endisset
