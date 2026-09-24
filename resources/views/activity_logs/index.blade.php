@extends('layouts.app')

@section('title', 'Activity Logs - Maintenance X')

@section('content')

<div class="space-y-6">

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                Activity & Audit Logs
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Rekam jejak seluruh aktivitas dan perubahan data dalam sistem
                untuk transparansi dan keamanan.
            </p>
        </div>

        {{-- Back --}}
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center justify-center gap-2
                  px-4 py-2.5 rounded-xl
                  bg-white border border-slate-300
                  text-slate-700 text-sm font-medium
                  hover:bg-slate-50 transition">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M15 19l-7-7 7-7" />

            </svg>

            Kembali

        </a>

    </div>


    {{-- =========================================================
        FILTER CARD
    ========================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">

        <form method="GET"
              action="{{ route('activity-logs.index') }}">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Search --}}
                <div class="lg:col-span-2">

                    <label for="search"
                           class="block text-sm font-medium text-slate-700 mb-2">

                        Cari Aktivitas

                    </label>

                    <div class="relative">

                        <div class="absolute inset-y-0 left-0 flex items-center pl-3
                                    pointer-events-none text-slate-400">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-5 h-5"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />

                            </svg>

                        </div>

                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari aktivitas, module, atau event..."
                            class="w-full rounded-xl
                                   border border-slate-300
                                   pl-10 pr-4 py-2.5
                                   text-sm text-slate-700
                                   placeholder:text-slate-400
                                   focus:ring-2 focus:ring-blue-500
                                   focus:border-blue-500
                                   outline-none transition"
                        >

                    </div>

                </div>


                {{-- Module --}}
                <div>

                    <label for="module"
                           class="block text-sm font-medium text-slate-700 mb-2">

                        Modul

                    </label>

                    <select
                        id="module"
                        name="module"
                        class="w-full bg-white
                               border border-slate-300
                               text-slate-700
                               text-sm rounded-xl
                               px-3.5 py-2.5
                               focus:ring-2 focus:ring-blue-500
                               focus:border-blue-500
                               outline-none transition">

                        <option value="">
                            Semua Modul
                        </option>

                        @foreach($modules as $module)

                            <option
                                value="{{ $module }}"
                                {{ request('module') === $module ? 'selected' : '' }}
                            >
                                {{ ucwords(str_replace('_', ' ', $module)) }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Event --}}
                <div>

                    <label for="event"
                           class="block text-sm font-medium text-slate-700 mb-2">

                        Event

                    </label>

                    <select
                        id="event"
                        name="event"
                        class="w-full bg-white
                               border border-slate-300
                               text-slate-700
                               text-sm rounded-xl
                               px-3.5 py-2.5
                               focus:ring-2 focus:ring-blue-500
                               focus:border-blue-500
                               outline-none transition">

                        <option value="">
                            Semua Event
                        </option>

                        @foreach($events as $event)

                            <option
                                value="{{ $event }}"
                                {{ request('event') === $event ? 'selected' : '' }}
                            >
                                {{ ucfirst($event) }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>


            {{-- Filter Buttons --}}
            <div class="flex flex-wrap items-center gap-2 mt-4">

                <button
                    type="submit"
                    class="inline-flex items-center gap-2
                           px-4 py-2.5 rounded-xl
                           bg-[#0B132B] text-white
                           text-sm font-medium
                           hover:bg-slate-800
                           transition">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-4 h-4"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />

                    </svg>

                    Terapkan Filter

                </button>


                @if(request()->hasAny(['search', 'module', 'event']))

                    <a
                        href="{{ route('activity-logs.index') }}"
                        class="inline-flex items-center gap-2
                               px-4 py-2.5 rounded-xl
                               bg-white border border-slate-300
                               text-slate-700 text-sm font-medium
                               hover:bg-slate-50 transition">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M6 18 18 6M6 6l12 12" />

                        </svg>

                        Reset

                    </a>

                @endif

            </div>

        </form>

    </div>


    {{-- =========================================================
        SUMMARY
    ========================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        {{-- Total Activity --}}
        <div class="bg-white rounded-2xl border border-slate-200
                    shadow-sm p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                        Total Activity
                    </p>

                    <p class="text-2xl font-bold text-slate-900 mt-1">
                        {{ number_format($logs->total()) }}
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl
                            bg-blue-50 text-blue-600
                            flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l4.414 4.414A1 1 0 0 1 18 8.414V19a2 2 0 0 1-2 2Z" />

                    </svg>

                </div>

            </div>

        </div>


        {{-- Module --}}
        <div class="bg-white rounded-2xl border border-slate-200
                    shadow-sm p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                        Modul Tercatat
                    </p>

                    <p class="text-2xl font-bold text-slate-900 mt-1">
                        {{ $modules->count() }}
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl
                            bg-indigo-50 text-indigo-600
                            flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M4 6h16M4 10h16M4 14h16M4 18h16" />

                    </svg>

                </div>

            </div>

        </div>


        {{-- Event --}}
        <div class="bg-white rounded-2xl border border-slate-200
                    shadow-sm p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                        Jenis Event
                    </p>

                    <p class="text-2xl font-bold text-slate-900 mt-1">
                        {{ $events->count() }}
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl
                            bg-emerald-50 text-emerald-600
                            flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M13 10V3L4 14h7v7l9-11h-7Z" />

                    </svg>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        TABLE
    ========================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200
                shadow-sm overflow-hidden">

        {{-- Table Header --}}
        <div class="px-5 py-4 border-b border-slate-200
                    flex flex-col sm:flex-row sm:items-center
                    sm:justify-between gap-2">

            <div>

                <h2 class="text-base font-semibold text-slate-900">
                    Riwayat Aktivitas
                </h2>

                <p class="text-xs text-slate-500 mt-1">
                    Menampilkan
                    {{ $logs->firstItem() ?? 0 }}
                    -
                    {{ $logs->lastItem() ?? 0 }}
                    dari
                    {{ $logs->total() }}
                    aktivitas.
                </p>

            </div>

            @if(request()->hasAny(['search', 'module', 'event']))

                <div class="text-xs text-blue-600 font-medium">
                    Filter sedang aktif
                </div>

            @endif

        </div>


        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse text-sm">

                <thead>

                    <tr class="bg-slate-50 border-b border-slate-200
                               text-slate-500 font-semibold
                               text-xs uppercase tracking-wider">

                        <th class="py-3.5 px-4 whitespace-nowrap">
                            Waktu
                        </th>

                        <th class="py-3.5 px-4 whitespace-nowrap">
                            Pengguna
                        </th>

                        <th class="py-3.5 px-4 whitespace-nowrap">
                            Modul
                        </th>

                        <th class="py-3.5 px-4 whitespace-nowrap">
                            Aktivitas
                        </th>

                        <th class="py-3.5 px-4 min-w-[350px]">
                            Detail Perubahan
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100 text-slate-700">

                    @forelse($logs as $log)

                        <tr class="hover:bg-slate-50/70 transition align-top">

                            {{-- =================================================
                                WAKTU
                            ================================================== --}}
                            <td class="py-4 px-4 whitespace-nowrap text-xs">

                                <span class="font-medium text-slate-900 block">

                                    {{ $log->created_at?->format('d M Y, H:i') }}

                                </span>

                                <span class="text-slate-400 text-[11px]">

                                    {{ $log->created_at?->diffForHumans() }}

                                </span>

                            </td>


                            {{-- =================================================
                                USER / CAUSER
                            ================================================== --}}
                            <td class="py-4 px-4 whitespace-nowrap">

                                <div class="flex items-center gap-2">

                                    <div class="w-8 h-8 rounded-full
                                                bg-slate-200
                                                flex items-center justify-center
                                                text-xs font-bold
                                                text-slate-700 uppercase">

                                        {{ strtoupper(
                                            substr(
                                                $log->causer->username
                                                ?? $log->causer->name
                                                ?? 'S',
                                                0,
                                                1
                                            )
                                        ) }}

                                    </div>

                                    <div>

                                        <span class="font-semibold
                                                     text-slate-900
                                                     block text-xs">

                                            {{ $log->causer->username
                                                ?? $log->causer->name
                                                ?? 'System / Anonymous' }}

                                        </span>

                                        <span class="text-slate-400 text-[10px]">

                                            {{ $log->causer->role ?? 'System' }}

                                        </span>

                                    </div>

                                </div>

                            </td>


                            {{-- =================================================
                                MODUL
                            ================================================== --}}
                            <td class="py-4 px-4 whitespace-nowrap">

                                @php

                                    $moduleName = $log->log_name
                                        ?? 'system';

                                @endphp

                                <span class="inline-flex items-center
                                             px-2.5 py-1
                                             rounded-full
                                             text-xs font-medium
                                             bg-blue-50 text-blue-700
                                             border border-blue-100
                                             uppercase tracking-wider">

                                    {{ str_replace('_', ' ', $moduleName) }}

                                </span>

                            </td>


                            {{-- =================================================
                                AKTIVITAS
                            ================================================== --}}
                            <td class="py-4 px-4 text-xs">

                                <div class="font-medium text-slate-800">
                                    {{ $log->description }}
                                </div>

                                @if($log->event)

                                    @php

                                        $eventClass = match(strtolower($log->event)) {

                                            'created' =>
                                                'bg-emerald-50 text-emerald-700 border-emerald-100',

                                            'updated' =>
                                                'bg-blue-50 text-blue-700 border-blue-100',

                                            'deleted' =>
                                                'bg-red-50 text-red-700 border-red-100',

                                            'restored' =>
                                                'bg-amber-50 text-amber-700 border-amber-100',

                                            default =>
                                                'bg-slate-50 text-slate-600 border-slate-200',

                                        };

                                    @endphp

                                    <span class="inline-flex items-center
                                                 mt-2 px-2 py-0.5
                                                 rounded-md border
                                                 text-[10px] font-semibold
                                                 uppercase
                                                 {{ $eventClass }}">

                                        {{ $log->event }}

                                    </span>

                                @endif

                                @if($log->subject)

                                    <div class="text-[10px] text-slate-400 mt-2">

                                        {{ class_basename($log->subject_type) }}

                                        #{{ $log->subject_id }}

                                    </div>

                                @endif

                            </td>


                            {{-- =================================================
                                DETAIL PERUBAHAN
                            ================================================== --}}
                            <td class="py-4 px-4 text-xs">

                                @php

                                    $properties = $log->properties;

                                    $oldData = $properties['old'] ?? null;

                                    $newData = $properties['attributes'] ?? null;

                                @endphp


                                @if($oldData || $newData)

                                    <div class="space-y-2 max-w-2xl">

                                        {{-- Before --}}
                                        @if($oldData)

                                            <div class="bg-red-50
                                                        border border-red-100
                                                        rounded-xl p-3">

                                                <div class="font-semibold
                                                            text-red-700 mb-1">

                                                    Sebelum

                                                </div>

                                                <pre class="whitespace-pre-wrap
                                                            break-words
                                                            font-mono
                                                            text-[10px]
                                                            text-red-600
                                                            overflow-x-auto">{{ json_encode($oldData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

                                            </div>

                                        @endif


                                        {{-- After --}}
                                        @if($newData)

                                            <div class="bg-emerald-50
                                                        border border-emerald-100
                                                        rounded-xl p-3">

                                                <div class="font-semibold
                                                            text-emerald-700 mb-1">

                                                    Sesudah

                                                </div>

                                                <pre class="whitespace-pre-wrap
                                                            break-words
                                                            font-mono
                                                            text-[10px]
                                                            text-emerald-600
                                                            overflow-x-auto">{{ json_encode($newData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

                                            </div>

                                        @endif

                                    </div>

                                @else

                                    <span class="text-slate-400 italic text-xs">
                                        Tidak ada detail perubahan data.
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        {{-- =================================================
                            EMPTY STATE
                        ================================================== --}}
                        <tr>

                            <td colspan="5"
                                class="py-16 px-6 text-center">

                                <div class="flex flex-col
                                            items-center justify-center">

                                    <div class="w-14 h-14 rounded-2xl
                                                bg-slate-100
                                                flex items-center justify-center
                                                text-slate-400 mb-4">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="w-7 h-7"
                                             fill="none"
                                             viewBox="0 0 24 24"
                                             stroke="currentColor"
                                             stroke-width="1.5">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l4.414 4.414A2 2 0 0 1 18 8.414V19a2 2 0 0 1-2 2Z" />

                                        </svg>

                                    </div>

                                    <h3 class="text-sm font-semibold
                                               text-slate-700">

                                        Belum ada riwayat aktivitas

                                    </h3>

                                    <p class="text-xs text-slate-400 mt-1">

                                        Belum ada aktivitas yang sesuai
                                        dengan filter yang dipilih.

                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- =========================================================
            PAGINATION
        ========================================================== --}}
        @if($logs->hasPages())

            <div class="p-4 border-t border-slate-100">

                {{ $logs->links() }}

            </div>

        @endif

    </div>

</div>

@endsection