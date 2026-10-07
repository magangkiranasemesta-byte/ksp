@isset($d['chart'])
@php $chart = $d['chart']; @endphp
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
    <h3 class="font-bold text-slate-800 mb-4">Status Work Order</h3>
    <div class="flex flex-col items-center gap-5">
        <div class="relative w-40 h-40 rounded-full shrink-0" style="background: {{ $chart['gradient'] }}" role="img"
             aria-label="Grafik status Work Order, total {{ $chart['total'] }}">
            <div class="absolute inset-[22px] rounded-full bg-white flex flex-col items-center justify-center">
                <span class="text-3xl font-extrabold text-slate-800 leading-none">{{ $chart['total'] }}</span>
                <span class="text-[11px] text-slate-400 mt-1">Work Order</span>
            </div>
        </div>
        <ul class="w-full space-y-2">
            @foreach($chart['segments'] as $seg)
                <li class="flex items-center justify-between gap-3 text-sm">
                    <span class="flex items-center gap-2 text-slate-600 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background: {{ $seg['color'] }}"></span>
                        <span class="truncate">{{ $seg['label'] }}</span>
                    </span>
                    <span class="font-semibold text-slate-800 whitespace-nowrap">{{ $seg['count'] }} <span class="text-xs font-normal text-slate-400">({{ $seg['percent'] }}%)</span></span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endisset
