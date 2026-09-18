{{-- =========================================================
     MAINTENANCE EVIDENCE GALLERY
     ========================================================= --}}

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-slate-200">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

            <div>
                <h2 class="text-xl font-bold text-slate-800">
                    Evidence Maintenance
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Dokumentasi kondisi sebelum, proses perbaikan,
                    dan hasil setelah maintenance.
                </p>
            </div>

            <div class="text-sm text-slate-500">
                Total:
                <span class="font-bold text-slate-800">
                    {{ $ticket->evidences->count() }}
                </span>
                foto
            </div>

        </div>

    </div>


    {{-- Upload --}}
    @if(!in_array($ticket->status, ['closed', 'cancelled']))

        <div class="p-6 bg-slate-50 border-b border-slate-200">

            <form
                action="{{ route('tickets.evidence.store', $ticket->id) }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-5"
            >

                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                    {{-- Stage --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Tahap Evidence
                        </label>

                        <select
                            name="stage"
                            required
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                Pilih tahap
                            </option>

                            <option value="before">
                                Before — Sebelum
                            </option>

                            <option value="process">
                                Process — Proses
                            </option>

                            <option value="after">
                                After — Setelah
                            </option>
                        </select>
                    </div>


                    {{-- Image --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Foto
                        </label>

                        <input
                            type="file"
                            name="image"
                            required
                            accept="image/jpeg,image/png,image/webp"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm"
                        >

                        <p class="text-xs text-slate-500 mt-1">
                            JPG, PNG, WEBP • Maksimal 5 MB
                        </p>
                    </div>


                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Keterangan
                        </label>

                        <textarea
                            name="description"
                            rows="3"
                            maxlength="1000"
                            placeholder="Contoh: Bearing mesin mengalami kerusakan."
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                    </div>

                </div>


                <div class="flex justify-end">

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition"
                    >
                        <span>📷</span>
                        Tambahkan Evidence
                    </button>

                </div>

            </form>

        </div>

    @endif


    {{-- Gallery --}}
    <div class="p-6">

        @php
            $stages = [
                'before' => [
                    'title' => 'Before',
                    'subtitle' => 'Kondisi Sebelum',
                    'icon' => '🔴',
                ],

                'process' => [
                    'title' => 'Process',
                    'subtitle' => 'Proses Perbaikan',
                    'icon' => '🟡',
                ],

                'after' => [
                    'title' => 'After',
                    'subtitle' => 'Kondisi Setelah',
                    'icon' => '🟢',
                ],
            ];
        @endphp


        <div class="space-y-10">

            @foreach($stages as $stageKey => $stage)

                @php
                    $stageEvidence = $ticket->evidences
                        ->where('stage', $stageKey);
                @endphp


                <section>

                    {{-- Stage Header --}}
                    <div class="flex items-center justify-between mb-4">

                        <div class="flex items-center gap-3">

                            <div class="text-2xl">
                                {{ $stage['icon'] }}
                            </div>

                            <div>
                                <h3 class="text-lg font-bold text-slate-800">
                                    {{ $stage['title'] }}
                                </h3>

                                <p class="text-sm text-slate-500">
                                    {{ $stage['subtitle'] }}
                                </p>
                            </div>

                        </div>

                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                            {{ $stageEvidence->count() }} foto
                        </span>

                    </div>


                    @if($stageEvidence->count())

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">

                            @foreach($stageEvidence as $evidence)

                                <div class="group bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition">

                                    {{-- Image --}}
                                    <div class="relative aspect-video bg-slate-100 overflow-hidden">

                                        <a
                                            href="{{ $evidence->image_url }}"
                                            target="_blank"
                                        >
                                            <img
                                                src="{{ $evidence->image_url }}"
                                                alt="Evidence {{ $stage['title'] }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                            >
                                        </a>


                                        {{-- Stage badge --}}
                                        <div class="absolute top-3 left-3">

                                            <span class="px-2.5 py-1 rounded-lg bg-black/70 text-white text-xs font-semibold">
                                                {{ strtoupper($stageKey) }}
                                            </span>

                                        </div>

                                    </div>


                                    {{-- Content --}}
                                    <div class="p-4">

                                        @if($evidence->description)

                                            <p class="text-sm text-slate-700 leading-relaxed">
                                                {{ $evidence->description }}
                                            </p>

                                        @else

                                            <p class="text-sm text-slate-400 italic">
                                                Tidak ada keterangan.
                                            </p>

                                        @endif


                                        {{-- Uploader --}}
                                        <div class="mt-4 pt-3 border-t border-slate-100">

                                            <div class="text-xs text-slate-500">

                                                <div>
                                                    Upload oleh:
                                                    <span class="font-semibold text-slate-700">
                                                        {{ $evidence->uploader->name ?? 'User' }}
                                                    </span>
                                                </div>

                                                <div class="mt-1">
                                                    {{ $evidence->created_at?->format('d M Y, H:i') }}
                                                </div>

                                            </div>

                                        </div>


                                        {{-- Delete --}}
                                        @php
                                            $currentUser = auth()->user();

                                            $canDelete =
                                                $evidence->uploaded_by === $currentUser->id
                                                ||
                                                in_array(
                                                    strtoupper((string) $currentUser->role),
                                                    [
                                                        'SUPERADMIN',
                                                        'ADMIN',
                                                        'SUPERVISOR',
                                                        'MANAGER'
                                                    ]
                                                );
                                        @endphp


                                        @if($canDelete)

                                            <form
                                                action="{{ route(
                                                    'tickets.evidence.destroy',
                                                    [
                                                        'ticket' => $ticket->id,
                                                        'evidence' => $evidence->id
                                                    ]
                                                ) }}"
                                                method="POST"
                                                class="mt-3"
                                                onsubmit="return confirm('Hapus evidence ini? File foto juga akan dihapus.');"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="w-full px-3 py-2 rounded-lg bg-red-50 text-red-600 text-sm font-semibold hover:bg-red-100 transition"
                                                >
                                                    🗑 Hapus Evidence
                                                </button>

                                            </form>

                                        @endif

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center">

                            <div class="text-4xl mb-3">
                                📷
                            </div>

                            <p class="font-semibold text-slate-600">
                                Belum ada evidence
                            </p>

                            <p class="text-sm text-slate-400 mt-1">
                                Tambahkan foto untuk tahap
                                {{ strtolower($stage['title']) }}.
                            </p>

                        </div>

                    @endif

                </section>

            @endforeach

        </div>

    </div>

</div>