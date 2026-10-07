@extends('layouts.app')

@section('title', 'Edit Equipment - ' . $equipment->equipment_code)
@section('page_title', 'Edit Equipment')

@section('content')
@php
    $input = 'w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500';
    $label = 'block text-xs font-semibold text-slate-600 mb-1.5';
@endphp

<div class="max-w-2xl space-y-5">
    <a href="{{ route('equipment.show', $equipment) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Kembali ke detail</a>

    @if($hasActiveWork)
        <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-sm">
            Equipment sedang dikerjakan melalui Work Order, status dikunci pada <strong>MAINTENANCE</strong> sampai Work Order selesai atau dibatalkan.
        </div>
    @endif

    <form method="POST" action="{{ route('equipment.update', $equipment) }}"
          class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="{{ $label }}" for="equipment_code">Kode Equipment</label>
            <input id="equipment_code" name="equipment_code" required maxlength="100" value="{{ old('equipment_code', $equipment->equipment_code) }}" class="{{ $input }} font-mono">
            @error('equipment_code')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="{{ $label }}" for="name">Nama</label>
            <input id="name" name="name" required maxlength="255" value="{{ old('name', $equipment->name) }}" class="{{ $input }}">
            @error('name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="{{ $label }}" for="location">Lokasi</label>
            <input id="location" name="location" required maxlength="255" value="{{ old('location', $equipment->location) }}" class="{{ $input }}">
            @error('location')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="{{ $label }}" for="status">Status</label>
            <select id="status" name="status" required class="{{ $input }}">
                @foreach(['ACTIVE', 'MAINTENANCE', 'INACTIVE'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $equipment->status) === $status)>{{ $status }}</option>
                @endforeach
            </select>
            @error('status')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="{{ $label }}" for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="3" class="{{ $input }}">{{ old('description', $equipment->description) }}</textarea>
            @error('description')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2">
            <a href="{{ route('equipment.show', $equipment) }}"
               class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl">Batal</a>
            <button class="min-h-[44px] px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
