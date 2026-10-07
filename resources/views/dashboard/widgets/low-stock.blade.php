@isset($d['lowStock'])
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="font-bold text-slate-800">Stok Menipis</h3>
        <a href="{{ route('spareparts.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Sparepart</a>
    </div>
    @forelse($d['lowStock'] as $sp)
        <div class="flex items-center justify-between gap-3 py-2.5 border-b border-slate-50 last:border-0 text-sm">
            <span class="font-medium text-slate-700 truncate">{{ $sp->name }}</span>
            <span class="font-bold {{ $sp->stock <= 0 ? 'text-rose-600' : 'text-orange-600' }} whitespace-nowrap">{{ $sp->stock }} <span class="text-xs font-normal text-slate-400">/ min {{ $sp->min_stock }}</span></span>
        </div>
    @empty
        <p class="text-sm text-slate-400 text-center py-8">Semua stok aman.</p>
    @endforelse
</div>
@endisset
