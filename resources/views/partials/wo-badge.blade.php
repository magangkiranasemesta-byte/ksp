{{--
    Badge status / prioritas Work Order.
    @include('partials.wo-badge', ['status'   => $wo->status])
    @include('partials.wo-badge', ['priority' => $wo->priority])
--}}
@php
    $statusStyles = [
        'OPEN'        => 'bg-slate-100 text-slate-600 border-slate-200',
        'ASSIGNED'    => 'bg-blue-50 text-blue-700 border-blue-200',
        'IN_PROGRESS' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'ON_HOLD'     => 'bg-amber-50 text-amber-700 border-amber-200',
        'COMPLETED'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'CANCELLED'   => 'bg-rose-50 text-rose-700 border-rose-200',
    ];

    $priorityStyles = [
        'LOW'      => 'bg-slate-100 text-slate-600 border-slate-200',
        'MEDIUM'   => 'bg-blue-50 text-blue-700 border-blue-200',
        'HIGH'     => 'bg-amber-50 text-amber-700 border-amber-200',
        'CRITICAL' => 'bg-red-100 text-red-700 border-red-200',
    ];
@endphp

@isset($status)
    <span class="inline-block whitespace-nowrap px-2.5 py-1 text-[11px] font-semibold rounded-full border {{ $statusStyles[$status] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
        {{ str_replace('_', ' ', $status) }}
    </span>
@endisset

@isset($priority)
    <span class="inline-block whitespace-nowrap px-2 py-0.5 text-[10px] font-bold rounded uppercase border {{ $priorityStyles[strtoupper($priority)] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
        {{ $priority }}
    </span>
@endisset
