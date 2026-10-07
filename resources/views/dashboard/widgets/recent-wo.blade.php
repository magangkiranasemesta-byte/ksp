@isset($d['recentWorkOrders'])
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h3 class="font-bold text-slate-800">Work Order Terbaru</h3>
        <a href="{{ route('work-orders.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[720px]">
            <thead>
                <tr class="text-left text-xs text-slate-400 bg-slate-50/70 border-b border-slate-100">
                    <th class="py-3 px-4 font-semibold">WO</th><th class="py-3 px-4 font-semibold">Equipment</th>
                    <th class="py-3 px-4 font-semibold">Tipe</th><th class="py-3 px-4 font-semibold">Prioritas</th>
                    <th class="py-3 px-4 font-semibold">Technician</th><th class="py-3 px-4 font-semibold">Status</th>
                    <th class="py-3 px-4 font-semibold">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($d['recentWorkOrders'] as $wo)
                    <tr class="hover:bg-slate-50/60">
                        <td class="py-3 px-4 font-semibold whitespace-nowrap"><a href="{{ route('work-orders.show', $wo) }}" class="text-blue-600 hover:underline">{{ $wo->wo_number }}</a></td>
                        <td class="py-3 px-4 text-slate-700">{{ $wo->equipment->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-slate-500 text-xs">{{ $wo->maintenance_type }}</td>
                        <td class="py-3 px-4">@include('partials.wo-badge', ['priority' => $wo->priority])</td>
                        <td class="py-3 px-4 text-slate-600">{{ $wo->technician->username ?? '-' }}</td>
                        <td class="py-3 px-4">@include('partials.wo-badge', ['status' => $wo->status])</td>
                        <td class="py-3 px-4 text-slate-500 whitespace-nowrap">{{ $wo->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-12 text-center text-sm text-slate-400">Belum ada Work Order.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endisset
