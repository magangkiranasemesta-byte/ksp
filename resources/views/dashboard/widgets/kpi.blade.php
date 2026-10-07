@isset($d['kpi'])
    <div class="grid grid-cols-2 {{ count($d['kpi']) >= 4 ? 'xl:grid-cols-4' : 'xl:grid-cols-' . max(2, count($d['kpi'])) }} gap-3 sm:gap-5">
        @foreach($d['kpi'] as $k)
            @php $tag = !empty($k['href']) ? 'a' : 'div'; @endphp
            <{{ $tag }} @if(!empty($k['href'])) href="{{ $k['href'] }}" @endif
                class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 sm:p-5 block {{ !empty($k['href']) ? 'hover:border-slate-300 transition' : '' }}">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">{{ $k['label'] }}</span>
                <p class="text-2xl sm:text-3xl font-extrabold {{ $k['color'] }} mt-1 break-words">{{ $k['value'] }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $k['hint'] }}</p>
            </{{ $tag }}>
        @endforeach
    </div>
@endisset
