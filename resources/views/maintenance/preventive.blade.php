@extends('layouts.app')

@section('title', 'Preventive Maintenance - Maintenance X')
@section('page_title', 'Preventive Maintenance')

@section('content')
<div class="space-y-6">

    {{-- =========================================================
        HEADER ACTIONS
    ========================================================== --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        <div>
            <h2 class="text-xl font-bold text-slate-900">
                Jadwal Perawatan Berkala
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Kelola aktivitas pemeliharaan pencegahan rutin
                (<em>preventive maintenance</em>).
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">

            {{-- Back --}}
            <a
                href="{{ route('maintenance.index') }}"
                class="inline-flex items-center justify-center px-4 py-2.5
                       bg-white border border-slate-200 text-slate-700
                       hover:bg-slate-50 rounded-xl text-sm font-semibold
                       transition-all min-h-[42px]"
            >
                ← Maintenance Request
            </a>

            {{-- Add --}}
            <button
                type="button"
                onclick="toggleModal('modalPreventive', true)"
                class="inline-flex items-center justify-center px-4 py-2.5
                       bg-blue-600 hover:bg-blue-700 text-white
                       rounded-xl text-sm font-semibold
                       shadow-lg shadow-blue-600/20
                       transition-all min-h-[42px]"
            >
                + Tambah Jadwal Rutin
            </button>

        </div>
    </div>


    {{-- =========================================================
        SUCCESS MESSAGE
    ========================================================== --}}
    @if(session('success'))
        <div
            class="flex items-start gap-3 p-4 bg-emerald-50
                   border border-emerald-200 rounded-xl text-emerald-700"
        >
            <span class="text-lg leading-none">✓</span>

            <p class="text-sm font-medium">
                {{ session('success') }}
            </p>
        </div>
    @endif


    {{-- =========================================================
        VALIDATION ERROR
    ========================================================== --}}
    @if($errors->any())
        <div
            class="p-4 bg-red-50 border border-red-200
                   rounded-xl text-red-700"
        >
            <p class="font-semibold text-sm mb-2">
                Terdapat kesalahan pada input:
            </p>

            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- =========================================================
        TABLE
    ========================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Internal horizontal scroll --}}
        <div class="overflow-x-auto">

            <table class="w-full min-w-[900px] text-left border-collapse text-sm">

                {{-- TABLE HEADER --}}
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">

                        <th class="p-4 whitespace-nowrap">
                            Equipment / Mesin
                        </th>

                        <th class="p-4 whitespace-nowrap">
                            Aktivitas Perawatan
                        </th>

                        <th class="p-4 whitespace-nowrap">
                            Frekuensi
                        </th>

                        <th class="p-4 whitespace-nowrap">
                            Jadwal Berikutnya
                        </th>

                        <th class="p-4 whitespace-nowrap">
                            Teknisi / Engineer
                        </th>

                        <th class="p-4 text-center whitespace-nowrap">
                            Aksi
                        </th>

                    </tr>
                </thead>


                {{-- TABLE BODY --}}
                <tbody class="divide-y divide-slate-100 text-slate-700">

                    @forelse($preventives as $item)

                        <tr class="hover:bg-slate-50/60 transition">

                            {{-- Equipment --}}
                            <td class="p-4 font-bold text-slate-900">

                                {{ $item->equipment->name ?? '-' }}

                                <span class="block text-xs font-normal text-slate-400 mt-0.5">
                                    {{ $item->equipment->code ?? '' }}
                                </span>

                            </td>


                            {{-- Activity --}}
                            <td class="p-4 font-medium text-slate-800">

                                {{ $item->title }}

                                @if($item->notes)
                                    <span class="block text-xs text-slate-400 mt-0.5">
                                        {{ Str::limit($item->notes, 40) }}
                                    </span>
                                @endif

                            </td>


                            {{-- Frequency --}}
                            <td class="p-4">

                                <span
                                    class="inline-flex items-center px-2.5 py-1
                                           text-xs font-bold
                                           bg-blue-50 text-blue-700
                                           rounded-md border border-blue-100
                                           capitalize"
                                >
                                    {{ $item->frequency }}
                                </span>

                            </td>


                            {{-- Next Maintenance --}}
                            <td class="p-4 font-semibold text-slate-800 whitespace-nowrap">

                                {{ \Carbon\Carbon::parse($item->next_maintenance_date)->format('d M Y') }}

                            </td>


                            {{-- Technician --}}
                            <td class="p-4 whitespace-nowrap">

                                @if($item->technician)
                                    <span class="font-medium text-slate-700">
                                        {{ $item->technician->username }}
                                    </span>
                                @else
                                    <span class="text-slate-400">
                                        Belum Ditunjuk
                                    </span>
                                @endif

                            </td>


                            {{-- Action --}}
                            <td class="p-4">

                                <div class="flex items-center justify-center">

                                    <form
                                        action="{{ route('maintenance.preventive.complete', $item->id) }}"
                                        method="POST"
                                        class="w-full sm:w-auto"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            onclick="return confirm('Apakah pekerjaan perawatan ini sudah selesai?')"
                                            class="w-full sm:w-auto
                                                   inline-flex items-center justify-center
                                                   min-h-[40px]
                                                   px-3 py-2
                                                   bg-emerald-50
                                                   hover:bg-emerald-100
                                                   text-emerald-700
                                                   border border-emerald-200
                                                   font-bold text-xs
                                                   rounded-xl
                                                   transition-all
                                                   whitespace-nowrap"
                                        >
                                            ✓ Selesai
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="p-10 text-center text-slate-400"
                            >
                                <div class="flex flex-col items-center justify-center">

                                    <div
                                        class="w-12 h-12 rounded-full
                                               bg-slate-100 flex items-center
                                               justify-center mb-3"
                                    >
                                        <span class="text-xl">📅</span>
                                    </div>

                                    <p class="font-medium text-slate-500">
                                        Belum ada jadwal pemeliharaan rutin.
                                    </p>

                                    <p class="text-xs text-slate-400 mt-1">
                                        Tambahkan jadwal preventive maintenance
                                        untuk mulai mengelola perawatan rutin.
                                    </p>

                                </div>
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>


    {{-- =========================================================
        PAGINATION
    ========================================================== --}}
    @if($preventives->hasPages())

        <div
            class="bg-white rounded-2xl border border-slate-200
                   shadow-sm px-4 sm:px-5 py-4"
        >

            <div
                class="flex flex-col md:flex-row
                       md:items-center md:justify-between gap-4"
            >

                {{-- Result Information --}}
                <div class="text-sm text-slate-500">

                    Showing

                    <span class="font-semibold text-slate-700">
                        {{ $preventives->firstItem() ?? 0 }}
                    </span>

                    to

                    <span class="font-semibold text-slate-700">
                        {{ $preventives->lastItem() ?? 0 }}
                    </span>

                    of

                    <span class="font-semibold text-slate-700">
                        {{ $preventives->total() }}
                    </span>

                    results

                </div>


                {{-- Pagination --}}
                <div class="overflow-x-auto max-w-full pb-1">

                    <div class="inline-flex items-center border border-slate-200 rounded-xl overflow-hidden">

                        {{-- Previous --}}
                        @if($preventives->onFirstPage())

                            <span
                                class="px-3 py-2 text-slate-300
                                       bg-white border-r border-slate-200"
                            >
                                ‹
                            </span>

                        @else

                            <a
                                href="{{ $preventives->previousPageUrl() }}"
                                class="px-3 py-2 text-slate-600
                                       hover:bg-slate-50
                                       border-r border-slate-200
                                       transition"
                                aria-label="Previous page"
                            >
                                ‹
                            </a>

                        @endif


                        {{-- Page Numbers --}}
                        @foreach(
                            $preventives->getUrlRange(
                                max(1, $preventives->currentPage() - 2),
                                min($preventives->lastPage(), $preventives->currentPage() + 2)
                            )
                            as $page => $url
                        )

                            @if($page == $preventives->currentPage())

                                <span
                                    class="min-w-[42px] px-3 py-2
                                           text-sm font-semibold
                                           text-slate-700 bg-slate-100
                                           border-r border-slate-200
                                           text-center"
                                >
                                    {{ $page }}
                                </span>

                            @else

                                <a
                                    href="{{ $url }}"
                                    class="min-w-[42px] px-3 py-2
                                           text-sm text-slate-600
                                           hover:bg-slate-50
                                           border-r border-slate-200
                                           transition text-center"
                                >
                                    {{ $page }}
                                </a>

                            @endif

                        @endforeach


                        {{-- Next --}}
                        @if($preventives->hasMorePages())

                            <a
                                href="{{ $preventives->nextPageUrl() }}"
                                class="px-3 py-2 text-slate-600
                                       hover:bg-slate-50
                                       transition"
                                aria-label="Next page"
                            >
                                ›
                            </a>

                        @else

                            <span
                                class="px-3 py-2 text-slate-300 bg-white"
                            >
                                ›
                            </span>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    @endif

</div>



{{-- =============================================================
    MODAL FORM TAMBAH JADWAL
============================================================= --}}
<div
    id="modalPreventive"
    class="fixed inset-0 z-50 bg-slate-900/40
           backdrop-blur-sm flex items-center justify-center
           hidden p-4"
>

    <div
        class="bg-white rounded-2xl border border-slate-200
               shadow-2xl w-full max-w-lg
               max-h-[90vh] overflow-y-auto"
    >

        <div class="p-5 sm:p-6">

            {{-- Modal Header --}}
            <div
                class="flex justify-between items-center
                       mb-5 pb-3 border-b border-slate-100"
            >

                <h3 class="text-lg font-bold text-slate-900">
                    Tambah Jadwal Perawatan Rutin
                </h3>

                <button
                    type="button"
                    onclick="toggleModal('modalPreventive', false)"
                    class="w-10 h-10 flex items-center justify-center
                           rounded-lg text-slate-400
                           hover:text-slate-600
                           hover:bg-slate-100
                           text-xl font-bold transition"
                    aria-label="Tutup modal"
                >
                    &times;
                </button>

            </div>


            {{-- Form --}}
            <form
                action="{{ route('maintenance.preventive.store') }}"
                method="POST"
                class="space-y-4"
            >

                @csrf


                {{-- Equipment --}}
                <div>

                    <label
                        class="block text-xs font-bold text-slate-600
                               uppercase tracking-wider mb-1"
                    >
                        Equipment / Mesin
                    </label>

                    <select
                        name="equipment_id"
                        required
                        class="w-full border border-slate-200
                               rounded-xl px-3.5 py-2.5
                               text-sm focus:ring-2
                               focus:ring-blue-500/20
                               focus:border-blue-600
                               outline-none transition"
                    >

                        <option value="">
                            -- Pilih Equipment --
                        </option>

                        @foreach($equipments as $eq)

                            <option
                                value="{{ $eq->id }}"
                                {{ old('equipment_id') == $eq->id ? 'selected' : '' }}
                            >
                                {{ $eq->name }} ({{ $eq->code }})
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Title --}}
                <div>

                    <label
                        class="block text-xs font-bold text-slate-600
                               uppercase tracking-wider mb-1"
                    >
                        Aktivitas / Judul Perawatan
                    </label>

                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        placeholder="Contoh: Pembersihan Filter & Pengecekan Oli"
                        class="w-full border border-slate-200
                               rounded-xl px-3.5 py-2.5
                               text-sm focus:ring-2
                               focus:ring-blue-500/20
                               focus:border-blue-600
                               outline-none transition"
                    >

                </div>


                {{-- Frequency + Date --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>

                        <label
                            class="block text-xs font-bold text-slate-600
                                   uppercase tracking-wider mb-1"
                        >
                            Frekuensi
                        </label>

                        <select
                            name="frequency"
                            required
                            class="w-full border border-slate-200
                                   rounded-xl px-3.5 py-2.5
                                   text-sm focus:ring-2
                                   focus:ring-blue-500/20
                                   focus:border-blue-600
                                   outline-none transition"
                        >

                            <option value="daily" {{ old('frequency') == 'daily' ? 'selected' : '' }}>
                                Harian (Daily)
                            </option>

                            <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>
                                Mingguan (Weekly)
                            </option>

                            <option value="monthly" {{ old('frequency', 'monthly') == 'monthly' ? 'selected' : '' }}>
                                Bulanan (Monthly)
                            </option>

                            <option value="yearly" {{ old('frequency') == 'yearly' ? 'selected' : '' }}>
                                Tahunan (Yearly)
                            </option>

                        </select>

                    </div>


                    <div>

                        <label
                            class="block text-xs font-bold text-slate-600
                                   uppercase tracking-wider mb-1"
                        >
                            Tanggal Rutin
                        </label>

                        <input
                            type="date"
                            name="next_maintenance_date"
                            value="{{ old('next_maintenance_date') }}"
                            required
                            class="w-full border border-slate-200
                                   rounded-xl px-3.5 py-2.5
                                   text-sm focus:ring-2
                                   focus:ring-blue-500/20
                                   focus:border-blue-600
                                   outline-none transition"
                        >

                    </div>

                </div>


                {{-- Technician --}}
                <div>

                    <label
                        class="block text-xs font-bold text-slate-600
                               uppercase tracking-wider mb-1"
                    >
                        Teknisi Penanggung Jawab
                    </label>

                    <select
                        name="assigned_to"
                        class="w-full border border-slate-200
                               rounded-xl px-3.5 py-2.5
                               text-sm focus:ring-2
                               focus:ring-blue-500/20
                               focus:border-blue-600
                               outline-none transition"
                    >

                        <option value="">
                            -- Pilih Teknisi (Opsional) --
                        </option>

                        @foreach($engineers as $eng)

                            <option
                                value="{{ $eng->id }}"
                                {{ old('assigned_to') == $eng->id ? 'selected' : '' }}
                            >
                                {{ $eng->username }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Notes --}}
                <div>

                    <label
                        class="block text-xs font-bold text-slate-600
                               uppercase tracking-wider mb-1"
                    >
                        Catatan Tambahan
                    </label>

                    <textarea
                        name="notes"
                        rows="3"
                        placeholder="Detail instruksi kerja..."
                        class="w-full border border-slate-200
                               rounded-xl px-3.5 py-2.5
                               text-sm focus:ring-2
                               focus:ring-blue-500/20
                               focus:border-blue-600
                               outline-none transition"
                    >{{ old('notes') }}</textarea>

                </div>


                {{-- Modal Actions --}}
                <div
                    class="flex flex-col-reverse sm:flex-row
                           justify-end gap-3 pt-3
                           border-t border-slate-100"
                >

                    <button
                        type="button"
                        onclick="toggleModal('modalPreventive', false)"
                        class="w-full sm:w-auto
                               px-4 py-2.5
                               border border-slate-200
                               text-slate-600
                               rounded-xl text-sm font-semibold
                               hover:bg-slate-50 transition
                               min-h-[42px]"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="w-full sm:w-auto
                               px-4 py-2.5
                               bg-blue-600 hover:bg-blue-700
                               text-white rounded-xl
                               text-sm font-semibold
                               shadow-lg shadow-blue-600/20
                               transition min-h-[42px]"
                    >
                        Simpan Jadwal
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>



{{-- =============================================================
    MODAL SCRIPT
============================================================= --}}
<script>
    function toggleModal(id, show) {
        const modal = document.getElementById(id);

        if (!modal) {
            return;
        }

        if (show) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    // Tutup modal ketika klik area luar modal
    document.addEventListener('click', function (event) {
        const modal = document.getElementById('modalPreventive');

        if (!modal || modal.classList.contains('hidden')) {
            return;
        }

        if (event.target === modal) {
            toggleModal('modalPreventive', false);
        }
    });

    // Tutup modal dengan tombol Escape
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            toggleModal('modalPreventive', false);
        }
    });
</script>

@endsection