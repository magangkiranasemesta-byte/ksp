@isset($d['downtimes'])
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h3 class="font-bold text-slate-800">Equipment Downtime</h3>
        <a href="{{ route('downtime.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[520px]">
            <thead>
                <tr class="text-left text-xs text-slate-400 bg-slate-50/70 border-b border-slate-100">
                    <th class="py-3 px-4 font-semibold">Equipment</th><th class="py-3 px-4 font-semibold">Mulai</th>
                    <th class="py-3 px-4 font-semibold">Durasi</th><th class="py-3 px-4 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($d['downtimes'] as $dt)
                    <tr class="hover:bg-slate-50/60">
                        <td class="py-3 px-4">
                            <a href="{{ route('downtime.show', $dt) }}" class="font-semibold text-slate-800 hover:text-blue-600">{{ $dt->equipment->name ?? '-' }}</a>
                            <p class="text-xs text-slate-400 truncate max-w-[200px]">{{ $dt->reason }}</p>
                        </td>
                        <td class="py-3 px-4 text-slate-600 whitespace-nowrap">{{ $dt->started_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4 text-slate-600 whitespace-nowrap">{{ $dt->duration }}</td>
                        <td class="py-3 px-4">
                            <span class="inline-block px-2.5 py-1 text-[11px] font-semibold rounded-full border {{ $dt->status === 'ONGOING' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">{{ $dt->status === 'ONGOING' ? 'Berjalan' : 'Selesai' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-12 text-center text-sm text-slate-400">Belum ada data downtime.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endisset
