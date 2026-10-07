@isset($d['workload'])
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
    <h3 class="font-bold text-slate-800 border-b border-slate-100 pb-3">Beban Kerja Technician</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[360px] mt-1">
            <thead>
                <tr class="text-left text-xs text-slate-400">
                    <th class="py-2 pr-3 font-semibold">Technician</th>
                    <th class="py-2 px-2 font-semibold text-center">Assigned</th>
                    <th class="py-2 px-2 font-semibold text-center">In progress</th>
                    <th class="py-2 pl-2 font-semibold text-center">On hold</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($d['workload'] as $row)
                    <tr>
                        <td class="py-2.5 pr-3 font-medium text-slate-700">{{ $row['name'] }}</td>
                        <td class="py-2.5 px-2 text-center {{ $row['assigned'] ? 'font-bold text-blue-600' : 'text-slate-300' }}">{{ $row['assigned'] }}</td>
                        <td class="py-2.5 px-2 text-center {{ $row['in_progress'] ? 'font-bold text-indigo-600' : 'text-slate-300' }}">{{ $row['in_progress'] }}</td>
                        <td class="py-2.5 pl-2 text-center {{ $row['on_hold'] ? 'font-bold text-amber-600' : 'text-slate-300' }}">{{ $row['on_hold'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-10 text-center text-sm text-slate-400">Belum ada technician.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endisset
