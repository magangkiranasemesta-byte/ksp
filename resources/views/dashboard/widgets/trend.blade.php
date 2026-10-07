@isset($d['trend'])
@php $t = $d['trend']; @endphp
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
        <h3 class="font-bold text-slate-800">Tren Work Order 6 Bulan</h3>
        <div class="flex items-center gap-4 text-[11px] font-semibold text-slate-500">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-blue-400"></span>Dibuat</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-emerald-500"></span>Selesai</span>
        </div>
    </div>
    <div class="flex items-end justify-between gap-2 sm:gap-4 h-52 mt-5" role="img" aria-label="Grafik batang jumlah Work Order dibuat dan selesai per bulan">
        @foreach($t['months'] as $m)
            <div class="flex-1 min-w-0 h-full flex flex-col justify-end items-center gap-1.5">
                <div class="w-full flex items-end justify-center gap-1 flex-1">
                    <div class="w-1/2 max-w-[22px] rounded-t bg-blue-400 relative" style="height: {{ max($m['created'] ? 4 : 0, round($m['created'] / $t['max'] * 100)) }}%" title="Dibuat: {{ $m['created'] }}">
                        @if($m['created'])<span class="absolute -top-4 left-1/2 -translate-x-1/2 text-[10px] font-semibold text-slate-500">{{ $m['created'] }}</span>@endif
                    </div>
                    <div class="w-1/2 max-w-[22px] rounded-t bg-emerald-500 relative" style="height: {{ max($m['completed'] ? 4 : 0, round($m['completed'] / $t['max'] * 100)) }}%" title="Selesai: {{ $m['completed'] }}">
                        @if($m['completed'])<span class="absolute -top-4 left-1/2 -translate-x-1/2 text-[10px] font-semibold text-slate-500">{{ $m['completed'] }}</span>@endif
                    </div>
                </div>
                <span class="text-[10px] sm:text-xs text-slate-400 whitespace-nowrap">{{ $m['label'] }}</span>
            </div>
        @endforeach
    </div>
</div>
@endisset
