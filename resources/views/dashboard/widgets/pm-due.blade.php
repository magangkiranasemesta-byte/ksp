@isset($d['upcomingPm'])
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="flex flex-wrap items-center justify-between gap-2 px-5 py-4 border-b border-slate-100">
        <h3 class="font-bold text-slate-800">Maintenance Akan Jatuh Tempo</h3>
        <div class="flex items-center gap-3 text-[11px] font-semibold">
            <span class="flex items-center gap-1 text-emerald-700"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Aman {{ $d['pmSummary']['safe'] }}</span>
            <span class="flex items-center gap-1 text-amber-700"><span class="w-2 h-2 rounded-full bg-amber-400"></span>Segera {{ $d['pmSummary']['due_soon'] }}</span>
            <span class="flex items-center gap-1 text-rose-700"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Overdue {{ $d['pmSummary']['overdue'] }}</span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]">
            <thead>
                <tr class="text-left text-xs text-slate-400 bg-slate-50/70 border-b border-slate-100">
                    <th class="py-3 px-4 font-semibold">Equipment</th><th class="py-3 px-4 font-semibold">Berikutnya</th>
                    <th class="py-3 px-4 font-semibold">Technician</th><th class="py-3 px-4 font-semibold">Status</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($d['upcomingPm'] as $pm)
                    <tr class="hover:bg-slate-50/60">
                        <td class="py-3 px-4">
                            <a href="{{ route('maintenance.preventive.show', $pm) }}" class="font-semibold text-slate-800 hover:text-blue-600">{{ $pm->equipment->name ?? '-' }}</a>
                            <p class="text-xs text-slate-400 truncate max-w-[200px]">{{ $pm->title }}</p>
                        </td>
                        <td class="py-3 px-4 text-slate-600 whitespace-nowrap">{{ $pm->next_maintenance_date?->format('d M Y') ?? '-' }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $pm->technician->username ?? '-' }}</td>
                        <td class="py-3 px-4">@include('partials.pm-badge', ['pm' => $pm])</td>
                        <td class="py-3 px-4 text-right">
                            @if($pm->status !== 'in_progress')
                                @can('generateWorkOrder', $pm)
                                    <form method="POST" action="{{ route('maintenance.preventive.work-order', $pm) }}" onsubmit="return confirm('Buat Work Order dari jadwal ini?')">
                                        @csrf
                                        <button class="min-h-[36px] px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg whitespace-nowrap">Generate WO</button>
                                    </form>
                                @endcan
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-12 text-center text-sm text-slate-400">Belum ada jadwal Preventive Maintenance.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endisset
