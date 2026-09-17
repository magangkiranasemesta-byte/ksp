@extends('layouts.app')

@section('title', 'Tickets - Maintenance X')
@section('page_title', 'Ticket Management')

@section('content')

<div class="space-y-6">

```
{{-- HEADER --}}
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
            <span class="text-xs font-bold uppercase tracking-widest text-blue-600">
                Maintenance Management
            </span>
        </div>

        <h2 class="text-2xl font-bold text-slate-900">
            Ticket Management
        </h2>

        <p class="text-sm text-slate-500 mt-1">
            Kelola, pantau, dan tindak lanjuti seluruh laporan maintenance.
        </p>
    </div>

    <a
        href="{{ route('tickets.create') }}"
        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl
               bg-blue-600 text-white text-sm font-semibold
               hover:bg-blue-700 transition shadow-lg shadow-blue-600/20"
    >
        <span class="text-lg leading-none">+</span>
        Buat Tiket Baru
    </a>

</div>


{{-- STATISTICS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Total Tiket
                </p>

                <p class="text-3xl font-extrabold text-slate-900 mt-2">
                    {{ $totalTickets }}
                </p>
            </div>

            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                🎫
            </div>
        </div>

        <p class="text-xs text-slate-400 mt-3">
            Seluruh laporan maintenance
        </p>
    </div>


    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center justify-between">

            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-amber-500">
                    Open
                </p>

                <p class="text-3xl font-extrabold text-amber-600 mt-2">
                    {{ $openTickets }}
                </p>
            </div>

            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                !
            </div>

        </div>

        <p class="text-xs text-slate-400 mt-3">
            Menunggu penanganan
        </p>
    </div>


    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center justify-between">

            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-blue-500">
                    In Progress
                </p>

                <p class="text-3xl font-extrabold text-blue-600 mt-2">
                    {{ $inProgressTickets }}
                </p>
            </div>

            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                ↻
            </div>

        </div>

        <p class="text-xs text-slate-400 mt-3">
            Sedang dikerjakan
        </p>
    </div>


    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center justify-between">

            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-500">
                    Completed
                </p>

                <p class="text-3xl font-extrabold text-emerald-600 mt-2">
                    {{ $completedTickets }}
                </p>
            </div>

            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                ✓
            </div>

        </div>

        <p class="text-xs text-slate-400 mt-3">
            Tiket telah selesai
        </p>
    </div>

</div>


{{-- SEARCH & FILTER --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">

    <form
        method="GET"
        action="{{ route('tickets.index') }}"
        class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3"
    >

        {{-- Search --}}
        <div class="xl:col-span-2">

            <label class="block text-xs font-bold text-slate-500 mb-1.5">
                Cari Tiket
            </label>

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Nomor tiket, judul, perangkat, asset..."
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200
                       text-sm outline-none transition
                       focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >

        </div>


        {{-- Status --}}
        <div>

            <label class="block text-xs font-bold text-slate-500 mb-1.5">
                Status
            </label>

            <select
                name="status"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200
                       text-sm bg-white outline-none
                       focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >

                <option value="">Semua Status</option>

                <option value="open"
                    {{ request('status') === 'open' ? 'selected' : '' }}>
                    Open
                </option>

                <option value="in_progress"
                    {{ request('status') === 'in_progress' ? 'selected' : '' }}>
                    In Progress
                </option>

                <option value="pending_sparepart"
                    {{ request('status') === 'pending_sparepart' ? 'selected' : '' }}>
                    Pending Sparepart
                </option>

                <option value="resolved"
                    {{ request('status') === 'resolved' ? 'selected' : '' }}>
                    Resolved
                </option>

                <option value="closed"
                    {{ request('status') === 'closed' ? 'selected' : '' }}>
                    Closed
                </option>

                <option value="cancelled"
                    {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                    Cancelled
                </option>

            </select>

        </div>


        {{-- Priority --}}
        <div>

            <label class="block text-xs font-bold text-slate-500 mb-1.5">
                Prioritas
            </label>

            <select
                name="priority"
                class="w-full px-4 py-2.5 rounded-xl border border-slate-200
                       text-sm bg-white outline-none
                       focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >

                <option value="">Semua Prioritas</option>

                <option value="urgent"
                    {{ request('priority') === 'urgent' ? 'selected' : '' }}>
                    Urgent
                </option>

                <option value="high"
                    {{ request('priority') === 'high' ? 'selected' : '' }}>
                    High
                </option>

                <option value="medium"
                    {{ request('priority') === 'medium' ? 'selected' : '' }}>
                    Medium
                </option>

                <option value="low"
                    {{ request('priority') === 'low' ? 'selected' : '' }}>
                    Low
                </option>

            </select>

        </div>


        {{-- Buttons --}}
        <div class="flex items-end gap-2">

            <button
                type="submit"
                class="flex-1 px-4 py-2.5 rounded-xl bg-slate-900 text-white
                       text-sm font-semibold hover:bg-slate-800 transition"
            >
                Cari
            </button>

            <a
                href="{{ route('tickets.index') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200
                       text-slate-600 text-sm font-semibold
                       hover:bg-slate-50 transition"
            >
                Reset
            </a>

        </div>

    </form>

</div>


{{-- TABLE --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

        <div>
            <h3 class="font-bold text-slate-900">
                Daftar Tiket
            </h3>

            <p class="text-xs text-slate-400 mt-1">
                Menampilkan {{ $tickets->count() }} dari {{ $tickets->total() }} tiket
            </p>
        </div>

        @if(request()->hasAny(['search', 'status', 'priority']))
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">
                Filter aktif
            </span>
        @endif

    </div>


    <div class="overflow-x-auto">

        <table class="w-full text-left text-sm">

            <thead class="bg-slate-50 border-b border-slate-200">

                <tr class="text-xs uppercase tracking-wider text-slate-500">

                    <th class="px-6 py-4 font-bold">
                        Ticket
                    </th>

                    <th class="px-6 py-4 font-bold">
                        Perangkat
                    </th>

                    <th class="px-6 py-4 font-bold">
                        Pelapor
                    </th>

                    <th class="px-6 py-4 font-bold">
                        Prioritas
                    </th>

                    <th class="px-6 py-4 font-bold">
                        Status
                    </th>

                    <th class="px-6 py-4 font-bold text-right">
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-slate-100">

                @forelse($tickets as $ticket)

                    <tr class="hover:bg-slate-50 transition">

                        {{-- Ticket --}}
                        <td class="px-6 py-4">

                            <a
                                href="{{ route('tickets.show', $ticket->id) }}"
                                class="font-bold text-blue-600 hover:text-blue-700 hover:underline"
                            >
                                {{ $ticket->ticket_number }}
                            </a>

                            <p class="font-semibold text-slate-800 mt-1 max-w-xs truncate">
                                {{ $ticket->title }}
                            </p>

                            <p class="text-xs text-slate-400 mt-1">
                                {{ $ticket->created_at->format('d M Y, H:i') }}
                            </p>

                        </td>


                        {{-- Device --}}
                        <td class="px-6 py-4">

                            <p class="font-semibold text-slate-800">
                                {{ $ticket->device->name ?? 'Perangkat Dihapus' }}
                            </p>

                            <p class="text-xs text-slate-400 mt-1">
                                {{ $ticket->device->asset_number ?? '-' }}
                            </p>

                        </td>


                        {{-- Reporter --}}
                        <td class="px-6 py-4">

                            <p class="font-medium text-slate-700">
                                {{ $ticket->reporter->name ?? $ticket->reporter->username ?? '-' }}
                            </p>

                        </td>


                        {{-- Priority --}}
                        <td class="px-6 py-4">

                            @php

                                $priorityClass = match($ticket->priority) {

                                    'urgent' =>
                                        'bg-red-50 text-red-700 border-red-100',

                                    'high' =>
                                        'bg-orange-50 text-orange-700 border-orange-100',

                                    'medium' =>
                                        'bg-amber-50 text-amber-700 border-amber-100',

                                    default =>
                                        'bg-slate-50 text-slate-600 border-slate-200',

                                };

                            @endphp

                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-lg
                                       border text-[11px] font-bold uppercase {{ $priorityClass }}"
                            >
                                {{ $ticket->priority }}
                            </span>

                        </td>


                        {{-- Status --}}
                        <td class="px-6 py-4">

                            @php

                                $statusClass = match($ticket->status) {

                                    'open' =>
                                        'bg-amber-50 text-amber-700 border-amber-100',

                                    'in_progress' =>
                                        'bg-blue-50 text-blue-700 border-blue-100',

                                    'pending_sparepart' =>
                                        'bg-purple-50 text-purple-700 border-purple-100',

                                    'resolved', 'closed' =>
                                        'bg-emerald-50 text-emerald-700 border-emerald-100',

                                    'cancelled' =>
                                        'bg-red-50 text-red-700 border-red-100',

                                    default =>
                                        'bg-slate-50 text-slate-600 border-slate-200',

                                };

                            @endphp

                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-lg
                                       border text-[11px] font-bold uppercase {{ $statusClass }}"
                            >
                                {{ str_replace('_', ' ', $ticket->status) }}
                            </span>

                        </td>


                        {{-- Action --}}
                        <td class="px-6 py-4 text-right">

                            <a
                                href="{{ route('tickets.show', $ticket->id) }}"
                                class="inline-flex items-center px-3 py-2 rounded-lg
                                       bg-slate-100 text-slate-700
                                       text-xs font-semibold
                                       hover:bg-blue-50 hover:text-blue-700 transition"
                            >
                                Detail
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="px-6 py-14 text-center"
                        >

                            <div class="text-4xl mb-3">
                                🎫
                            </div>

                            <p class="font-semibold text-slate-700">
                                Tiket tidak ditemukan
                            </p>

                            <p class="text-sm text-slate-400 mt-1">
                                Coba ubah kata pencarian atau filter.
                            </p>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- Pagination --}}
    @if($tickets->hasPages())

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">

            {{ $tickets->links() }}

        </div>

    @endif

</div>
```

</div>

@endsection
