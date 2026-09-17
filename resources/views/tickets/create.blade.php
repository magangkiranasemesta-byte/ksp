@extends('layouts.app') {{-- Sesuaikan dengan nama file layout utama Anda --}}

@section('title', 'Buat Tiket Baru - Maintenance X')
@section('page_title', 'Tickets')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <!-- Header Form -->
        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <h2 class="text-xl font-bold text-slate-800">Lapor Kerusakan Perangkat</h2>
            <p class="text-sm text-slate-500 mt-1">Isi formulir di bawah ini untuk membuat tiket perbaikan baru.</p>
        </div>

        <!-- Form Tiket -->
        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- Pilih Perangkat -->
            <div>
                <label for="device_id" class="block text-sm font-semibold text-slate-700 mb-2">
                    Pilih Perangkat <span class="text-red-500">*</span>
                </label>
                <select name="device_id" id="device_id" required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                    <option value="">-- Pilih Perangkat --</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                            {{ $device->asset_number }} - {{ $device->name }} ({{ $device->location ?? 'Tidak ada lokasi' }})
                        </option>
                    @endforeach
                </select>
                @error('device_id') <span class="text-xs text-red-500 mt-1.5 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Judul Laporan & Prioritas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">
                        Judul Masalah/Keluhan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Contoh: Layar monitor bergaris dan mati sendiri" required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                    @error('title') <span class="text-xs text-red-500 mt-1.5 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-semibold text-slate-700 mb-2">
                        Prioritas <span class="text-red-500">*</span>
                    </label>
                    <select name="priority" id="priority" required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Rendah (Low)</option>
                        <option value="medium" {{ old('priority') == 'medium' || !old('priority') ? 'selected' : '' }}>Sedang (Medium)</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Tinggi (High)</option>
                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Darurat (Urgent)</option>
                    </select>
                    @error('priority') <span class="text-xs text-red-500 mt-1.5 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Deskripsi Kerusakan -->
            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">
                    Deskripsi Detail Kerusakan <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="4" placeholder="Jelaskan secara rinci kronologi atau gejala kerusakan yang terjadi..." required class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">{{ old('description') }}</textarea>
                @error('description') <span class="text-xs text-red-500 mt-1.5 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Upload Foto Bukti -->
            <div>
                <label for="image_proof" class="block text-sm font-semibold text-slate-700 mb-2">
                    Foto Bukti Kerusakan (Opsional)
                </label>
                <input type="file" name="image_proof" id="image_proof" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors cursor-pointer">
                <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG, JPEG. Maksimal 2MB.</p>
                @error('image_proof') <span class="text-xs text-red-500 mt-1.5 block font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('tickets.index') }}" class="px-4 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-500/20 focus:ring-2 focus:ring-blue-500">
                    Kirim Tiket Pelaporan
                </button>
            </div>
        </form>
        
    </div>
</div>
@endsection