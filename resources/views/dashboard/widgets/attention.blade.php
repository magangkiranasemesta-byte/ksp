@isset($d['attention'])
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-5">
        @foreach($d['attention'] as [$label, $value, $color, $href])
            @php $tag = $href ? 'a' : 'div'; @endphp
            <{{ $tag }} @if($href) href="{{ $href }}" @endif
                class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 flex items-center justify-between gap-2 {{ $href ? 'hover:border-slate-300 transition' : '' }}">
                <span class="text-xs font-semibold text-slate-500">{{ $label }}</span>
                <span class="text-2xl font-bold {{ $value > 0 ? $color : 'text-slate-300' }}">{{ $value }}</span>
            </{{ $tag }}>
        @endforeach
    </div>
@endisset
