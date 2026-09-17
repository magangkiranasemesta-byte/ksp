@extends('layouts.app')

@section('title', 'Riwayat Pemakaian Sparepart')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Riwayat Pemakaian Sparepart
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Catat dan lihat riwayat penggunaan sparepart pada proses maintenance.
            </p>
        </div>

        <a
            href="{{ route('sparepart-usages.create') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5
                   rounded-xl bg-blue-600 text-white font-semibold
                   hover:bg-blue-700 transition shadow-sm"
        >
            <span class="text-lg">+</span>
            Catat Pemakaian
        </a>

    </div>


    {{-- Flash Success --}}
    @if(session('success'))

        <div class="mb-5 rounded-xl border border-green-200 bg-green-50
                    px-4 py-3 text-green-700">

            <div class="flex items-center gap-2">
                <span class="font-bold">✓</span>

                <span>
                    {{ session('success') }}
                </span>
            </div>

        </div>

    @endif


    {{-- Error --}}
    @if($errors->any())

        <div class="mb-5 rounded-xl border border-red-200 bg-red-50
                    px-4 py-3 text-red-700">

            <ul class="list-disc list-inside text-sm space-y-1">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Filter --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-6">

        <form
            method="GET"
            action="{{ route('sparepart-usages.index') }}"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4"
        >

            {{-- Search --}}
            <div class="lg:col-span-2">

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Cari
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nama sparepart, kode, user..."
                    class="w-full rounded-xl border-slate-300
                           focus:border-blue-500 focus:ring-blue-500"
                >

            </div>


            {{-- Sparepart --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Sparepart
                </label>

                <select
                    name="sparepart_id"
                    class="w-full rounded-xl border-slate-300
                           focus:border-blue-500 focus:ring-blue-500"
                >

                    <option value="">
                        Semua Sparepart
                    </option>

                    @foreach($spareparts as $sparepart)

                        <option
                            value="{{ $sparepart->id }}"
                            @selected(request('sparepart_id') == $sparepart->id)
                        >
                            {{ $sparepart->code }} - {{ $sparepart->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Start Date --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Dari
                </label>

                <input
                    type="date"
                    name="start_date"
                    value="{{ request('start_date') }}"
                    class="w-full rounded-xl border-slate-300
                           focus:border-blue-500 focus:ring-blue-500"
                >

            </div>


            {{-- End Date --}}
            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Sampai
                </label>

                <input
                    type="date"
                    name="end_date"
                    value="{{ request('end_date') }}"
                    class="w-full rounded-xl border-slate-300
                           focus:border-blue-500 focus:ring-blue-500"
                >

            </div>


            {{-- Buttons --}}
            <div class="lg:col-span-5 flex gap-2">

                <button
                    type="submit"
                    class="px-4 py-2.5 rounded-xl bg-slate-800
                           text-white font-semibold hover:bg-slate-900 transition"
                >
                    Filter
                </button>

                <a
                    href="{{ route('sparepart-usages.index') }}"
                    class="px-4 py-2.5 rounded-xl bg-slate-100
                           text-slate-700 font-semibold hover:bg-slate-200 transition"
                >
                    Reset
                </a>

            </div>

        </form>

    </div>


    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 border-b border-slate-200">

                    <tr>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            No
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            Tanggal
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            Sparepart
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            Ticket
                        </th>

                        <th class="px-5 py-4 text-center font-semibold text-slate-600">
                            Jumlah
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            Digunakan Oleh
                        </th>

                        <th class="px-5 py-4 text-left font-semibold text-slate-600">
                            Catatan
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($usages as $usage)

                        <tr class="hover:bg-slate-50 transition">

                            {{-- No --}}
                            <td class="px-5 py-4 text-slate-500">

                                {{ $usages->firstItem() + $loop->index }}

                            </td>


                            {{-- Date --}}
                            <td class="px-5 py-4 whitespace-nowrap">

                                <div class="font-medium text-slate-800">

                                    {{ $usage->used_at?->format('d/m/Y') ?? '-' }}

                                </div>

                                <div class="text-xs text-slate-400">

                                    {{ $usage->used_at?->format('H:i') ?? '-' }}

                                </div>

                            </td>


                            {{-- Sparepart --}}
                            <td class="px-5 py-4">

                                @if($usage->sparepart)

                                    <div class="font-semibold text-slate-800">

                                        {{ $usage->sparepart->name }}

                                    </div>

                                    <div class="text-xs text-slate-500">

                                        {{ $usage->sparepart->code }}

                                    </div>

                                @else

                                    <span class="text-slate-400">
                                        Sparepart dihapus
                                    </span>

                                @endif

                            </td>


                            {{-- Ticket --}}
                            <td class="px-5 py-4">

                                @if($usage->maintenance_ticket_id)

                                    <span class="inline-flex items-center px-2.5 py-1
                                                 rounded-lg bg-blue-50 text-blue-700
                                                 text-xs font-semibold">

                                        Ticket #{{ $usage->maintenance_ticket_id }}

                                    </span>

                                @else

                                    <span class="text-slate-400">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- Quantity --}}
                            <td class="px-5 py-4 text-center">

                                <span class="inline-flex items-center justify-center
                                             min-w-10 px-3 py-1 rounded-lg
                                             bg-orange-50 text-orange-700
                                             font-bold">

                                    {{ $usage->quantity }}

                                </span>

                                @if($usage->sparepart)

                                    <div class="text-xs text-slate-400 mt-1">

                                        {{ $usage->sparepart->unit }}

                                    </div>

                                @endif

                            </td>


                            {{-- User --}}
                            <td class="px-5 py-4">

                                @if($usage->user)

                                    <div class="font-medium text-slate-800">

                                        {{ $usage->user->name ?? $usage->user->username }}

                                    </div>

                                    @if($usage->user->username)

                                        <div class="text-xs text-slate-400">

                                            {{ '@' . $usage->user->username }}

                                        </div>

                                    @endif

                                @else

                                    <span class="text-slate-400">
                                        User tidak tersedia
                                    </span>

                                @endif

                            </td>


                            {{-- Notes --}}
                            <td class="px-5 py-4 max-w-xs">

                                @if($usage->notes)

                                    <span
                                        class="text-slate-600"
                                        title="{{ $usage->notes }}"
                                    >
                                        {{ \Illuminate\Support\Str::limit($usage->notes, 60) }}
                                    </span>

                                @else

                                    <span class="text-slate-400">
                                        -
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-5 py-12 text-center"
                            >

                                <div class="text-4xl mb-3">
                                    📦
                                </div>

                                <div class="font-semibold text-slate-700">
                                    Belum ada riwayat pemakaian
                                </div>

                                <div class="text-sm text-slate-400 mt-1">
                                    Silakan catat pemakaian sparepart terlebih dahulu.
                                </div>

                                <a
                                    href="{{ route('sparepart-usages.create') }}"
                                    class="inline-flex mt-4 px-4 py-2
                                           rounded-xl bg-blue-600 text-white
                                           font-semibold hover:bg-blue-700 transition"
                                >
                                    Catat Pemakaian
                                </a>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if($usages->hasPages())

            <div class="px-5 py-4 border-t border-slate-200">

                {{ $usages->links() }}

            </div>

        @endif

    </div>

</div>

@endsection