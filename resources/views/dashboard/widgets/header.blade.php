@php $h = $d['header']; @endphp
<div class="{{ $h['bg'] }} text-white rounded-2xl p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div class="min-w-0">
        <span class="inline-block text-[10px] font-bold uppercase tracking-widest bg-white/15 px-2.5 py-1 rounded-full">{{ $h['role_label'] }}</span>
        <h2 class="text-xl sm:text-2xl font-bold mt-2">{{ $h['title'] }}</h2>
        <p class="text-sm text-white/80 mt-1">{{ $h['subtitle'] }}</p>
        <p class="text-[11px] text-white/60 mt-2 break-words">Hak akses Anda: {{ $h['modules'] }} &middot; {{ now()->translatedFormat('d F Y, H:i') }}</p>
    </div>

    @if(!empty($h['actions']))
        <div class="flex flex-wrap gap-2 shrink-0">
            @foreach($h['actions'] as $action)
                <a href="{{ $action['href'] }}"
                   class="inline-flex items-center justify-center min-h-[44px] px-4 py-2 bg-white text-slate-800 hover:bg-slate-100 text-sm font-semibold rounded-xl transition">
                    {{ $action['label'] }}
                </a>
            @endforeach
        </div>
    @endif
</div>
