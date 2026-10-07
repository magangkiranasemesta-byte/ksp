@extends('layouts.app')

@section('title', 'Edit Jadwal Preventive')
@section('page_title', 'Edit Jadwal Preventive')

@section('content')
<div class="max-w-3xl space-y-5">
    <a href="{{ route('maintenance.preventive.show', $preventive) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Kembali ke detail</a>

    <form method="POST" action="{{ route('maintenance.preventive.update', $preventive) }}"
          class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
        @csrf
        @method('PUT')
        @include('preventive._fields')
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
            <a href="{{ route('maintenance.preventive.show', $preventive) }}"
               class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl">Batal</a>
            <button class="min-h-[44px] px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
