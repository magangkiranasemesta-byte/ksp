{{-- Ringkasan samping: pengguna, equipment, ticket (tiap blok hanya tampil jika datanya ada) --}}
@isset($d['usersByRole'])
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="font-bold text-slate-800">Pengguna</h3>
        <span class="text-xs font-bold text-slate-500">{{ $d['usersByRole']->sum() }} total</span>
    </div>
    <dl class="mt-3 space-y-2 text-sm">
        @foreach($d['usersByRole'] as $roleName => $total)
            <div class="flex items-center justify-between"><dt class="text-slate-500">{{ $roleName }}</dt><dd class="font-bold text-slate-800">{{ $total }}</dd></div>
        @endforeach
    </dl>
    <a href="{{ route('users.index') }}" class="block mt-3 text-xs font-semibold text-blue-600 hover:underline">Kelola user &rarr;</a>
</div>
@endisset

@isset($d['equipmentSummary'])
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="font-bold text-slate-800">Kondisi Equipment</h3>
        <a href="{{ route('equipment.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Buka</a>
    </div>
    <dl class="mt-3 space-y-2 text-sm">
        @foreach(['ACTIVE' => 'text-emerald-600', 'MAINTENANCE' => 'text-amber-600', 'INACTIVE' => 'text-slate-500'] as $st => $color)
            <div class="flex items-center justify-between"><dt class="text-slate-500">{{ $st }}</dt><dd class="font-bold {{ $color }}">{{ (int) ($d['equipmentSummary'][$st] ?? 0) }}</dd></div>
        @endforeach
    </dl>
</div>
@endisset

@isset($d['ticketSummary'])
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="font-bold text-slate-800">Ticket (Incident)</h3>
        <a href="{{ route('tickets.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Buka</a>
    </div>
    <dl class="mt-3 space-y-2 text-sm">
        <div class="flex items-center justify-between"><dt class="text-slate-500">Baru</dt><dd class="font-bold text-slate-800">{{ $d['ticketSummary']['open'] }}</dd></div>
        <div class="flex items-center justify-between"><dt class="text-slate-500">Ditangani</dt><dd class="font-bold text-indigo-600">{{ $d['ticketSummary']['active'] }}</dd></div>
        <div class="flex items-center justify-between"><dt class="text-slate-500">Selesai</dt><dd class="font-bold text-emerald-600">{{ $d['ticketSummary']['done'] }}</dd></div>
    </dl>
</div>
@endisset
