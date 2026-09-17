@extends('layouts.app')

@section('title', 'Manajemen Sparepart')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Sparepart & Suku Cadang</h1>
            <p class="text-gray-500 text-sm">Kelola ketersediaan stok komponen perbaikan.</p>
        </div>
        <button onclick="document.getElementById('addSparepartModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium text-sm transition">
            + Tambah Sparepart
        </button>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabel Data Sparepart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 font-semibold uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">Kode</th>
                    <th class="px-6 py-3">Nama Sparepart</th>
                    <th class="px-6 py-3">Kategori</th>
                    <th class="px-6 py-3">Stok</th>
                    <th class="px-6 py-3">Harga Satuan</th>
                    <th class="px-6 py-3">Status Stok</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($spareparts as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-mono text-blue-600 font-semibold">{{ $item->code }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $item->name }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $item->category ?? '-' }}</td>
                    <td class="px-6 py-4 font-bold text-gray-800">{{ $item->stock }} {{ $item->unit }}</td>
                    <td class="px-6 py-4">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        @if($item->isLowStock())
                            <span class="bg-red-100 text-red-700 text-xs px-2.5 py-1 rounded-full font-medium">Low Stock</span>
                        @else
                            <span class="bg-green-100 text-green-700 text-xs px-2.5 py-1 rounded-full font-medium">Aman</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <form action="{{ route('spareparts.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus sparepart ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-6 text-gray-400">Belum ada data sparepart.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $spareparts->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah Sparepart -->
<div id="addSparepartModal" class="fixed inset-0 bg-black/50 hidden flex justify-center items-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Tambah Sparepart Baru</h2>
        <form action="{{ route('spareparts.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="text-xs font-semibold text-gray-600">Kode Barang</label>
                <input type="text" name="code" required placeholder="SP-001" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-blue-500">
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Nama Sparepart</label>
                <input type="text" name="name" required placeholder="Filter Oli" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-blue-500">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-xs font-semibold text-gray-600">Stok Awal</label>
                    <input type="number" name="stock" required min="0" value="0" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Batas Min. Stok</label>
                    <input type="number" name="min_stock" required min="0" value="2" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-blue-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-xs font-semibold text-gray-600">Satuan</label>
                    <input type="text" name="unit" required placeholder="pcs / liter" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600">Harga Satuan (Rp)</label>
                    <input type="number" name="price" required min="0" value="0" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-blue-500">
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="document.getElementById('addSparepartModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endsection