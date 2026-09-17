@extends('layouts.app')

@section('title', 'Catat Pemakaian Sparepart')

@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Header --}}
    <div class="mb-6">

        <a
            href="{{ route('sparepart-usages.index') }}"
            class="inline-flex items-center gap-2 text-sm
                   text-slate-500 hover:text-blue-600 mb-4"
        >
            ← Kembali ke Riwayat
        </a>

        <h1 class="text-2xl font-bold text-slate-800">
            Catat Pemakaian Sparepart
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Stok sparepart akan otomatis berkurang setelah pemakaian disimpan.
        </p>

    </div>


    {{-- Validation Errors --}}
    @if($errors->any())

        <div class="mb-6 rounded-xl border border-red-200 bg-red-50
                    px-4 py-3 text-red-700">

            <div class="font-semibold mb-2">
                Terdapat kesalahan:
            </div>

            <ul class="list-disc list-inside text-sm space-y-1">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">

        <form
            method="POST"
            action="{{ route('sparepart-usages.store') }}"
            class="space-y-6"
        >

            @csrf


            {{-- Sparepart --}}
            <div>

                <label
                    for="sparepart_id"
                    class="block text-sm font-semibold text-slate-700 mb-2"
                >
                    Sparepart
                    <span class="text-red-500">*</span>
                </label>

                <select
                    id="sparepart_id"
                    name="sparepart_id"
                    required
                    class="w-full rounded-xl border-slate-300
                           focus:border-blue-500 focus:ring-blue-500"
                >

                    <option value="">
                        -- Pilih Sparepart --
                    </option>

                    @foreach($spareparts as $sparepart)

                        <option
                            value="{{ $sparepart->id }}"
                            data-stock="{{ $sparepart->stock }}"
                            data-unit="{{ $sparepart->unit }}"
                            @selected(old('sparepart_id') == $sparepart->id)
                        >

                            {{ $sparepart->code }}
                            -
                            {{ $sparepart->name }}
                            | Stok: {{ $sparepart->stock }}
                            {{ $sparepart->unit }}

                        </option>

                    @endforeach

                </select>

                <div
                    id="stockInfo"
                    class="hidden mt-2 rounded-xl bg-blue-50
                           border border-blue-100 px-4 py-3
                           text-sm text-blue-700"
                >
                </div>

            </div>


            {{-- Ticket --}}
            <div>

                <label
                    for="maintenance_ticket_id"
                    class="block text-sm font-semibold text-slate-700 mb-2"
                >
                    Maintenance Ticket
                </label>

                <select
                    id="maintenance_ticket_id"
                    name="maintenance_ticket_id"
                    class="w-full rounded-xl border-slate-300
                           focus:border-blue-500 focus:ring-blue-500"
                >

                    <option value="">
                        -- Tidak terkait ticket --
                    </option>

                    @foreach($tickets as $ticket)

                        <option
                            value="{{ $ticket->id }}"
                            @selected(old('maintenance_ticket_id') == $ticket->id)
                        >
                            Ticket #{{ $ticket->id }}
                        </option>

                    @endforeach

                </select>

                <p class="text-xs text-slate-400 mt-1">
                    Pilih ticket jika sparepart digunakan untuk maintenance ticket tertentu.
                </p>

            </div>


            {{-- Quantity --}}
            <div>

                <label
                    for="quantity"
                    class="block text-sm font-semibold text-slate-700 mb-2"
                >
                    Jumlah Pemakaian
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="number"
                    id="quantity"
                    name="quantity"
                    value="{{ old('quantity', 1) }}"
                    min="1"
                    required
                    class="w-full rounded-xl border-slate-300
                           focus:border-blue-500 focus:ring-blue-500"
                >

                <div
                    id="quantityWarning"
                    class="hidden mt-2 rounded-xl bg-red-50
                           border border-red-200 px-4 py-3
                           text-sm text-red-700"
                >
                </div>

            </div>


            {{-- Used At --}}
            <div>

                <label
                    for="used_at"
                    class="block text-sm font-semibold text-slate-700 mb-2"
                >
                    Tanggal & Waktu Pemakaian
                </label>

                <input
                    type="datetime-local"
                    id="used_at"
                    name="used_at"
                    value="{{ old('used_at', now()->format('Y-m-d\TH:i')) }}"
                    class="w-full rounded-xl border-slate-300
                           focus:border-blue-500 focus:ring-blue-500"
                >

            </div>


            {{-- Notes --}}
            <div>

                <label
                    for="notes"
                    class="block text-sm font-semibold text-slate-700 mb-2"
                >
                    Catatan
                </label>

                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    maxlength="5000"
                    placeholder="Contoh: Sparepart digunakan untuk mengganti komponen rusak..."
                    class="w-full rounded-xl border-slate-300
                           focus:border-blue-500 focus:ring-blue-500"
                >{{ old('notes') }}</textarea>

            </div>


            {{-- Info --}}
            <div
                class="rounded-xl bg-amber-50 border border-amber-200
                       px-4 py-4"
            >

                <div class="flex gap-3">

                    <div class="text-xl">
                        ⚠️
                    </div>

                    <div>

                        <div class="font-semibold text-amber-800">
                            Perhatian
                        </div>

                        <div class="text-sm text-amber-700 mt-1">
                            Setelah data disimpan, stok sparepart akan otomatis
                            berkurang sesuai jumlah pemakaian.
                        </div>

                    </div>

                </div>

            </div>


            {{-- Buttons --}}
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">

                <a
                    href="{{ route('sparepart-usages.index') }}"
                    class="inline-flex items-center justify-center
                           px-5 py-2.5 rounded-xl
                           bg-slate-100 text-slate-700
                           font-semibold hover:bg-slate-200 transition"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    id="submitButton"
                    class="inline-flex items-center justify-center
                           px-5 py-2.5 rounded-xl
                           bg-blue-600 text-white
                           font-semibold hover:bg-blue-700
                           transition shadow-sm"
                >
                    Simpan Pemakaian
                </button>

            </div>

        </form>

    </div>

</div>


{{-- JavaScript --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const sparepartSelect = document.getElementById('sparepart_id');
    const quantityInput = document.getElementById('quantity');
    const stockInfo = document.getElementById('stockInfo');
    const quantityWarning = document.getElementById('quantityWarning');
    const submitButton = document.getElementById('submitButton');


    function checkStock() {

        const selectedOption =
            sparepartSelect.options[sparepartSelect.selectedIndex];

        if (!selectedOption || !selectedOption.value) {

            stockInfo.classList.add('hidden');
            quantityWarning.classList.add('hidden');
            submitButton.disabled = false;

            return;
        }


        const stock =
            parseInt(selectedOption.dataset.stock || 0);

        const unit =
            selectedOption.dataset.unit || '';

        const quantity =
            parseInt(quantityInput.value || 0);


        /*
        |--------------------------------------------------------------------------
        | Stock Information
        |--------------------------------------------------------------------------
        */

        stockInfo.innerHTML =
            `Stok tersedia: <strong>${stock} ${unit}</strong>`;

        stockInfo.classList.remove('hidden');


        /*
        |--------------------------------------------------------------------------
        | Quantity Validation
        |--------------------------------------------------------------------------
        */

        if (quantity > stock) {

            quantityWarning.innerHTML =
                `Jumlah pemakaian melebihi stok. ` +
                `Stok tersedia hanya <strong>${stock} ${unit}</strong>.`;

            quantityWarning.classList.remove('hidden');

            submitButton.disabled = true;

            submitButton.classList.add(
                'opacity-50',
                'cursor-not-allowed'
            );

        } else {

            quantityWarning.classList.add('hidden');

            submitButton.disabled = false;

            submitButton.classList.remove(
                'opacity-50',
                'cursor-not-allowed'
            );
        }
    }


    sparepartSelect.addEventListener(
        'change',
        checkStock
    );


    quantityInput.addEventListener(
        'input',
        checkStock
    );


    checkStock();

});

</script>

@endsection