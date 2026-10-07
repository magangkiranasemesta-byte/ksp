{{-- Badge status jadwal PM. Pemakaian: @include('partials.pm-badge', ['pm' => $item]) --}}
@php
    if ($pm->status === 'in_progress') {
        $label = 'Sedang dikerjakan'; $cls = 'bg-indigo-50 text-indigo-700 border-indigo-200';
    } else {
        [$label, $cls] = match ($pm->due_state) {
            'overdue'  => ['Overdue ' . abs($pm->days_until_due) . ' hari', 'bg-rose-50 text-rose-700 border-rose-200'],
            'due_soon' => [$pm->days_until_due === 0 ? 'Jatuh tempo hari ini' : 'Due soon (' . $pm->days_until_due . ' hari)', 'bg-amber-50 text-amber-700 border-amber-200'],
            default    => ['Aman', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        };
    }
@endphp
<span class="inline-block whitespace-nowrap px-2.5 py-1 text-[11px] font-semibold rounded-full border {{ $cls }}">{{ $label }}</span>
