@extends('layouts.app')

@section('title', 'Edit Downtime Equipment')

@section('content')

<div class="min-h-screen bg-slate-100 p-6">

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="max-w-5xl mx-auto">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

            <div class="flex items-center gap-4">

                {{-- Back --}}
                <a
                    href="{{ route('downtime.show', $downtime->id) }}"
                    class="inline-flex items-center justify-center
                           w-10 h-10 rounded-xl
                           bg-white border border-slate-200
                           text-slate-600
                           hover:bg-slate-50 hover:text-slate-900
                           transition"
                    title="Kembali"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"
                        />
                    </svg>
                </a>

                <div>
                    <h1 class="text-2xl font-bold text-slate-800">
                        Edit Downtime Equipment
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Perbarui informasi downtime equipment.
                    </p>
                </div>

            </div>

            {{-- Status --}}
            @if($downtime->status === 'ONGOING')

                <span class="inline-flex items-center gap-2
                             px-4 py-2 rounded-full
                             text-sm font-semibold
                             bg-amber-100 text-amber-700">

                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>

                    Ongoing
                </span>

            @else

                <span class="inline-flex items-center gap-2
                             px-4 py-2 rounded-full
                             text-sm font-semibold
                             bg-emerald-100 text-emerald-700">

                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>

                    Completed
                </span>

            @endif

        </div>


        {{-- =====================================================
            VALIDATION ERROR
        ====================================================== --}}
        @if($errors->any())

            <div class="mb-6 rounded-xl border border-red-200
                        bg-red-50 p-4">

                <div class="flex gap-3">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v3.75m0 3h.008v.008H12V15.75ZM10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"
                        />
                    </svg>

                    <div>

                        <p class="font-semibold text-red-700">
                            Ada data yang perlu diperbaiki.
                        </p>

                        <ul class="mt-2 list-disc list-inside text-sm text-red-600 space-y-1">

                            @foreach($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        @endif


        {{-- =====================================================
            FORM
        ====================================================== --}}
        <form
            action="{{ route('downtime.update', $downtime->id) }}"
            method="POST"
        >

            @csrf
            @method('PUT')


            <div class="bg-white rounded-2xl
                        border border-slate-200
                        shadow-sm overflow-hidden">


                {{-- =================================================
                    FORM HEADER
                ================================================== --}}
                <div class="px-6 py-5 border-b border-slate-200">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Informasi Downtime
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Silakan ubah informasi yang diperlukan.
                    </p>

                </div>


                {{-- =================================================
                    FORM BODY
                ================================================== --}}
                <div class="p-6 space-y-6">


                    {{-- =============================================
                        EQUIPMENT
                    ============================================== --}}
                    <div>

                        <label
                            for="equipment_id"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            Equipment
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            name="equipment_id"
                            id="equipment_id"
                            required
                            class="w-full rounded-xl
                                   border border-slate-300
                                   bg-white
                                   px-4 py-3
                                   text-sm text-slate-700
                                   focus:border-blue-500
                                   focus:ring-2
                                   focus:ring-blue-500/20
                                   outline-none
                                   transition"
                        >

                            <option value="">
                                -- Pilih Equipment --
                            </option>

                            @foreach($equipment as $item)

                                <option
                                    value="{{ $item->id }}"
                                    {{ old('equipment_id', $downtime->equipment_id) == $item->id ? 'selected' : '' }}
                                >
                                    {{ $item->name }}

                                    @if(!empty($item->code))
                                        — {{ $item->code }}
                                    @endif
                                </option>

                            @endforeach

                        </select>

                        @error('equipment_id')

                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- =============================================
                        STARTED AT
                    ============================================== --}}
                    <div>

                        <label
                            for="started_at"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            Waktu Mulai Downtime
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="datetime-local"
                            name="started_at"
                            id="started_at"
                            required
                            value="{{ old('started_at', $downtime->started_at?->format('Y-m-d\TH:i')) }}"
                            class="w-full rounded-xl
                                   border border-slate-300
                                   bg-white
                                   px-4 py-3
                                   text-sm text-slate-700
                                   focus:border-blue-500
                                   focus:ring-2
                                   focus:ring-blue-500/20
                                   outline-none
                                   transition"
                        >

                        @error('started_at')

                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- =============================================
                        REASON
                    ============================================== --}}
                    <div>

                        <label
                            for="reason"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            Alasan Downtime
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="reason"
                            id="reason"
                            required
                            maxlength="255"
                            value="{{ old('reason', $downtime->reason) }}"
                            placeholder="Contoh: Mesin mengalami kerusakan"
                            class="w-full rounded-xl
                                   border border-slate-300
                                   bg-white
                                   px-4 py-3
                                   text-sm text-slate-700
                                   placeholder-slate-400
                                   focus:border-blue-500
                                   focus:ring-2
                                   focus:ring-blue-500/20
                                   outline-none
                                   transition"
                        >

                        @error('reason')

                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- =============================================
                        DESCRIPTION
                    ============================================== --}}
                    <div>

                        <label
                            for="description"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            Deskripsi
                        </label>

                        <textarea
                            name="description"
                            id="description"
                            rows="5"
                            placeholder="Tambahkan penjelasan mengenai downtime..."
                            class="w-full rounded-xl
                                   border border-slate-300
                                   bg-white
                                   px-4 py-3
                                   text-sm text-slate-700
                                   placeholder-slate-400
                                   focus:border-blue-500
                                   focus:ring-2
                                   focus:ring-blue-500/20
                                   outline-none
                                   transition resize-none"
                        >{{ old('description', $downtime->description) }}</textarea>

                        @error('description')

                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- =============================================
                        INFO
                    ============================================== --}}
                    <div class="rounded-xl bg-slate-50
                                border border-slate-200 p-4">

                        <div class="flex gap-3">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5 text-slate-500 flex-shrink-0 mt-0.5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M11.25 11.25h1.5v5.25h-1.5v-5.25Zm0-3h1.5v1.5h-1.5v-1.5ZM12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z"
                                />
                            </svg>

                            <div>

                                <p class="text-sm font-semibold text-slate-700">
                                    Status Downtime
                                </p>

                                <p class="text-sm text-slate-500 mt-1">
                                    Status saat ini:
                                    <strong>
                                        {{ $downtime->status }}
                                    </strong>
                                </p>

                                @if($downtime->status === 'COMPLETED')

                                    <p class="text-xs text-slate-500 mt-2">
                                        Downtime sudah selesai. Waktu selesai
                                        tidak diubah melalui halaman edit.
                                    </p>

                                @else

                                    <p class="text-xs text-slate-500 mt-2">
                                        Downtime masih berjalan. Gunakan tombol
                                        <strong>Complete</strong> pada halaman detail
                                        untuk menyelesaikannya.
                                    </p>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                    FOOTER
                ================================================== --}}
                <div class="px-6 py-5
                            border-t border-slate-200
                            bg-slate-50
                            flex flex-col-reverse
                            sm:flex-row
                            sm:justify-end
                            gap-3">

                    <a
                        href="{{ route('downtime.show', $downtime->id) }}"
                        class="inline-flex items-center justify-center
                               px-5 py-3
                               rounded-xl
                               bg-white
                               border border-slate-300
                               text-sm font-semibold
                               text-slate-700
                               hover:bg-slate-100
                               transition"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center
                               px-5 py-3
                               rounded-xl
                               bg-blue-600
                               text-white
                               text-sm font-semibold
                               hover:bg-blue-700
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-500/30
                               transition"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 mr-2"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 12.75 9.75 17.5 19 7.75"
                            />
                        </svg>

                        Simpan Perubahan

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

@endsection