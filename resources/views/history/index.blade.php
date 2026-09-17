@extends('layouts.app')

@section('title', 'Maintenance History')

@section('content')

<div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">

```
{{-- ============================================================
    HEADER
============================================================= --}}

<div class="mb-6">

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

        <div>

            <div class="mb-2 flex items-center gap-2">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>

                <span class="text-sm font-semibold uppercase tracking-wider text-slate-500">
                    Archive
                </span>

            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Maintenance History
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Riwayat seluruh aktivitas maintenance equipment.
            </p>

        </div>

        {{-- Total data --}}

        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0118 8v11a2 2 0 01-2 2z"
                        />
                    </svg>

                </div>

                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Data Ditampilkan
                    </p>

                    <p class="text-lg font-bold text-slate-900">
                        {{ $history->total() }}
                        <span class="text-sm font-medium text-slate-400">
                            record
                        </span>
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ============================================================
    FLASH MESSAGE
============================================================= --}}

@if(session('success'))

    <div
        id="success-alert"
        class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm"
    >

        <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-emerald-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M5 13l4 4L19 7"
                />
            </svg>

        </div>

        <div class="flex-1">

            <p class="font-semibold">
                Berhasil
            </p>

            <p class="mt-0.5 text-sm">
                {{ session('success') }}
            </p>

        </div>

        <button
            type="button"
            onclick="document.getElementById('success-alert').remove()"
            class="text-emerald-500 transition hover:text-emerald-700"
        >
            ✕
        </button>

    </div>

@endif


@if(session('error'))

    <div
        id="error-alert"
        class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm"
    >

        <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-red-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 9v4m0 4h.01M10.29 3.86l-8.82 15a2 2 0 001.71 2.14h17.64a2 2 0 001.71-2.14l-8.82-15a2 2 0 00-1.71-.86 2 2 0 00-1.71.86z"
                />
            </svg>

        </div>

        <div class="flex-1">

            <p class="font-semibold">
                Terjadi Kesalahan
            </p>

            <p class="mt-0.5 text-sm">
                {{ session('error') }}
            </p>

        </div>

        <button
            type="button"
            onclick="document.getElementById('error-alert').remove()"
            class="text-red-500 transition hover:text-red-700"
        >
            ✕
        </button>

    </div>

@endif


{{-- ============================================================
    FILTER & EXPORT
============================================================= --}}

<div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

    {{-- Filter Header --}}

    <div class="border-b border-slate-100 bg-slate-50 px-5 py-4 sm:px-6">

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex items-center gap-3">

                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-slate-700 shadow-sm">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 11.414V19a1 1 0 01-.553.894l-4 2A1 1 0 019 21v-9.586L3.293 6.707A1 1 0 013 6V4z"
                        />
                    </svg>

                </div>

                <div>

                    <h2 class="font-semibold text-slate-900">
                        Filter History
                    </h2>

                    <p class="text-xs text-slate-500">
                        Gunakan filter untuk mencari data tertentu.
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- Filter Body --}}

    <div class="p-5 sm:p-6">

        <form
            method="GET"
            action="{{ route('history') }}"
        >

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">

                {{-- Tanggal Mulai --}}

                <div>

                    <label
                        for="start_date"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Tanggal Mulai
                    </label>

                    <input
                        id="start_date"
                        type="date"
                        name="start_date"
                        value="{{ request('start_date') }}"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >

                </div>


                {{-- Tanggal Akhir --}}

                <div>

                    <label
                        for="end_date"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Tanggal Akhir
                    </label>

                    <input
                        id="end_date"
                        type="date"
                        name="end_date"
                        value="{{ request('end_date') }}"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >

                </div>


                {{-- Status --}}

                <div>

                    <label
                        for="status"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >

                        <option value="ALL">
                            Semua Status
                        </option>

                        <option
                            value="COMPLETED"
                            @selected(request('status') === 'COMPLETED')
                        >
                            Completed
                        </option>

                        <option
                            value="IN_PROGRESS"
                            @selected(request('status') === 'IN_PROGRESS')
                        >
                            In Progress
                        </option>

                        <option
                            value="APPROVED"
                            @selected(request('status') === 'APPROVED')
                        >
                            Approved
                        </option>

                        <option
                            value="REJECTED"
                            @selected(request('status') === 'REJECTED')
                        >
                            Rejected
                        </option>

                    </select>

                </div>


                {{-- Tombol Filter --}}

                <div class="flex items-end">

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 active:scale-[0.98]"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18.15 7.5 7.5 0 0016.65 16.65z"
                            />
                        </svg>

                        Terapkan Filter

                    </button>

                </div>

            </div>


            {{-- Export Area --}}

            <div class="mt-5 flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:flex-wrap sm:items-center">

                {{-- Export PDF --}}

                <a
                    href="{{ route('history.export.pdf', request()->query()) }}"
                    target="_blank"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 active:scale-[0.98]"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M7 21h10a2 2 0 002-2V9.414a2 2 0 00-.586-1.414l-4.414-4.414A2 2 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M13 3v6h6"
                        />

                    </svg>

                    Export PDF

                </a>


                {{-- Export Excel --}}

                <a
                    href="{{ route('history.export.excel', request()->query()) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 active:scale-[0.98]"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 5a2 2 0 012-2h8l6 6v10a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8 13l2 3 2-3m-4 0h4"
                        />

                    </svg>

                    Export Excel

                </a>


                {{-- Reset --}}

                @if(request()->hasAny([
                    'start_date',
                    'end_date',
                    'status'
                ]))

                    <a
                        href="{{ route('history') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-[0.98]"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9M4.582 9H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2M19.419 15H15"
                            />
                        </svg>

                        Reset Filter

                    </a>

                @endif

            </div>

        </form>

    </div>

</div>


{{-- ============================================================
    TABLE
============================================================= --}}

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

    {{-- Table Header --}}

    <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">

        <div>

            <h2 class="font-semibold text-slate-900">
                Riwayat Maintenance
            </h2>

            <p class="mt-0.5 text-xs text-slate-500">
                Data maintenance yang sudah diproses.
            </p>

        </div>

        <div class="text-sm text-slate-500">

            @if($history->total() > 0)

                Menampilkan
                <span class="font-semibold text-slate-800">
                    {{ $history->firstItem() }}
                </span>

                -
                <span class="font-semibold text-slate-800">
                    {{ $history->lastItem() }}
                </span>

                dari
                <span class="font-semibold text-slate-800">
                    {{ $history->total() }}
                </span>

            @else

                Tidak ada data

            @endif

        </div>

    </div>


    {{-- Responsive Table Wrapper --}}

    <div class="overflow-x-auto">

        <table class="min-w-full divide-y divide-slate-200">

            <thead class="bg-slate-50">

                <tr>

                    <th
                        scope="col"
                        class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500 sm:px-6"
                    >
                        ID
                    </th>

                    <th
                        scope="col"
                        class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                    >
                        Equipment
                    </th>

                    <th
                        scope="col"
                        class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                    >
                        Engineer
                    </th>

                    <th
                        scope="col"
                        class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                    >
                        Priority
                    </th>

                    <th
                        scope="col"
                        class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                    >
                        Status
                    </th>

                    <th
                        scope="col"
                        class="min-w-[280px] px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                    >
                        Description
                    </th>

                    <th
                        scope="col"
                        class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                    >
                        Created
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-slate-100 bg-white">

                @forelse($history as $item)

                    <tr class="transition hover:bg-slate-50">

                        {{-- ID --}}

                        <td class="whitespace-nowrap px-5 py-4 sm:px-6">

                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">

                                #{{ $item->id }}

                            </span>

                        </td>


                        {{-- Equipment --}}

                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m18 0h-2M5.636 5.636L4.222 4.222m15.556 15.556l-1.414-1.414M5.636 18.364l-1.414 1.414M19.778 4.222l-1.414 1.414M9 5h6a2 2 0 012 2v10a2 2 0 01-2 2H9a2 2 0 01-2-2V7a2 2 0 012-2z"
                                        />
                                    </svg>

                                </div>

                                <div class="min-w-0">

                                    <p class="truncate text-sm font-semibold text-slate-800">

                                        {{ $item->equipment->name ?? 'Equipment tidak ditemukan' }}

                                    </p>

                                    @if(isset($item->equipment->id))

                                        <p class="text-xs text-slate-400">
                                            Equipment #{{ $item->equipment->id }}
                                        </p>

                                    @endif

                                </div>

                            </div>

                        </td>


                        {{-- Engineer --}}

                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">

                                    @php
                                        $engineerName = $item->engineer->username ?? 'N/A';
                                        $initial = strtoupper(substr($engineerName, 0, 1));
                                    @endphp

                                    {{ $initial }}

                                </div>

                                <div>

                                    <p class="whitespace-nowrap text-sm font-medium text-slate-700">

                                        {{ $engineerName }}

                                    </p>

                                    <p class="text-xs text-slate-400">
                                        Engineer
                                    </p>

                                </div>

                            </div>

                        </td>


                        {{-- Priority --}}

                        <td class="whitespace-nowrap px-5 py-4">

                            @php

                                $priority = strtoupper($item->priority ?? 'LOW');

                                $priorityClasses = match($priority) {

                                    'CRITICAL' =>
                                        'bg-red-100 text-red-700 ring-red-200',

                                    'HIGH' =>
                                        'bg-orange-100 text-orange-700 ring-orange-200',

                                    'MEDIUM' =>
                                        'bg-yellow-100 text-yellow-700 ring-yellow-200',

                                    default =>
                                        'bg-emerald-100 text-emerald-700 ring-emerald-200',

                                };

                            @endphp

                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $priorityClasses }}"
                            >

                                {{ $priority }}

                            </span>

                        </td>


                        {{-- Status --}}

                        <td class="whitespace-nowrap px-5 py-4">

                            @php

                                $status = strtoupper($item->status ?? 'UNKNOWN');

                                $statusClasses = match($status) {

                                    'COMPLETED' =>
                                        'bg-emerald-100 text-emerald-700 ring-emerald-200',

                                    'IN_PROGRESS' =>
                                        'bg-blue-100 text-blue-700 ring-blue-200',

                                    'APPROVED' =>
                                        'bg-indigo-100 text-indigo-700 ring-indigo-200',

                                    'REJECTED' =>
                                        'bg-red-100 text-red-700 ring-red-200',

                                    default =>
                                        'bg-slate-100 text-slate-700 ring-slate-200',

                                };

                                $statusLabel = match($status) {

                                    'COMPLETED' => 'Completed',

                                    'IN_PROGRESS' => 'In Progress',

                                    'APPROVED' => 'Approved',

                                    'REJECTED' => 'Rejected',

                                    default => $status,

                                };

                            @endphp

                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $statusClasses }}"
                            >

                                @if($status === 'COMPLETED')

                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                @elseif($status === 'IN_PROGRESS')

                                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-500"></span>

                                @elseif($status === 'APPROVED')

                                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>

                                @elseif($status === 'REJECTED')

                                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>

                                @else

                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-500"></span>

                                @endif

                                {{ $statusLabel }}

                            </span>

                        </td>


                        {{-- Description --}}

                        <td class="px-5 py-4">

                            <div
                                class="max-w-xl text-sm leading-6 text-slate-600"
                                title="{{ $item->description }}"
                            >

                                {{ $item->description ?: 'Tidak ada deskripsi.' }}

                            </div>

                        </td>


                        {{-- Created --}}

                        <td class="whitespace-nowrap px-5 py-4">

                            @if($item->created_at)

                                <p class="text-sm font-medium text-slate-700">

                                    {{ $item->created_at->format('d M Y') }}

                                </p>

                                <p class="text-xs text-slate-400">

                                    {{ $item->created_at->format('H:i') }}

                                </p>

                            @else

                                <span class="text-sm text-slate-400">
                                    -
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    {{-- Empty State --}}

                    <tr>

                        <td
                            colspan="7"
                            class="px-6 py-16 text-center"
                        >

                            <div class="mx-auto flex max-w-md flex-col items-center">

                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-8 w-8 text-slate-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0118 8v11a2 2 0 01-2 2z"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M13 3v5h5"
                                        />

                                    </svg>

                                </div>

                                <h3 class="text-base font-bold text-slate-800">
                                    Tidak Ada History
                                </h3>

                                <p class="mt-1 text-sm text-slate-500">
                                    Belum ada data maintenance yang sesuai dengan filter yang dipilih.
                                </p>

                                @if(request()->hasAny([
                                    'start_date',
                                    'end_date',
                                    'status'
                                ]))

                                    <a
                                        href="{{ route('history') }}"
                                        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                                    >

                                        Reset Filter

                                    </a>

                                @endif

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- ========================================================
        PAGINATION
    ========================================================= --}}

    @if($history->hasPages())

        <div class="border-t border-slate-100 px-5 py-4 sm:px-6">

            {{ $history->onEachSide(1)->links() }}

        </div>

    @endif

</div>


{{-- ============================================================
    FOOTER INFORMATION
============================================================= --}}

<div class="mt-5 flex flex-col gap-2 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between">

    <p>
        Maintenance Management System
    </p>

    <p>
        Last updated:
        {{ now()->format('d M Y, H:i') }}
    </p>

</div>
```

</div>

{{-- ================================================================
AUTO HIDE FLASH MESSAGE
================================================================= --}}

<script>

    document.addEventListener('DOMContentLoaded', function () {

        const successAlert = document.getElementById('success-alert');
        const errorAlert = document.getElementById('error-alert');

        if (successAlert) {

            setTimeout(() => {

                successAlert.style.transition = 'opacity 0.4s ease';

                successAlert.style.opacity = '0';

                setTimeout(() => {

                    successAlert.remove();

                }, 400);

            }, 5000);

        }

        if (errorAlert) {

            setTimeout(() => {

                errorAlert.style.transition = 'opacity 0.4s ease';

                errorAlert.style.opacity = '0';

                setTimeout(() => {

                    errorAlert.remove();

                }, 400);

            }, 7000);

        }

    });

</script>

@endsection