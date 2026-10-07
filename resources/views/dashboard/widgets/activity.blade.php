@isset($d['activities'])
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-1">
        <h3 class="font-bold text-slate-800">{{ $d['activityTitle'] ?? 'Aktivitas Terbaru' }}</h3>
        @if(auth()->user()->hasPermission('activity_logs'))
            <a href="{{ route('activity-logs.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat semua</a>
        @endif
    </div>
    @forelse($d['activities'] as $act)
        <div class="flex items-start gap-3 py-3 border-b border-slate-50 last:border-0">
            <span class="mt-1.5 w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
            <div class="min-w-0 flex-1">
                <p class="text-sm text-slate-700 break-words"><span class="font-semibold text-slate-900">{{ $act->causer->username ?? 'Sistem' }}</span> &middot; {{ $act->description }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ str_replace('_', ' ', $act->log_name ?? 'system') }}@if($act->event) &middot; {{ $act->event }}@endif &middot; {{ $act->created_at->diffForHumans() }}</p>
            </div>
        </div>
    @empty
        <p class="text-sm text-slate-400 text-center py-10">Belum ada aktivitas tercatat.</p>
    @endforelse
</div>
@endisset
