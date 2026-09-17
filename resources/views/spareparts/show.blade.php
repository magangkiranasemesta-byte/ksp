<!-- Component Sparepart Used -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Penggunaan Sparepart & Suku Cadang</h3>

    <!-- Form Tambah Pemakaian Sparepart -->
    <form action="{{ route('tickets.spareparts.store', $ticket->id) }}" method="POST" class="flex gap-3 mb-6">
        @csrf
        <div class="flex-1">
            <select name="sparepart_id" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-blue-500">
                <option value="">-- Pilih Sparepart --</option>
                @foreach($availableSpareparts as $item)
                    <option value="{{ $item->id }}" {{ $item->stock == 0 ? 'disabled' : '' }}>
                        {{ $item->code }} - {{ $item->name }} (Stok: {{ $item->stock }} {{ $item->unit }}) - Rp {{ number_format($item->price, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="w-28">
            <input type="number" name="quantity" min="1" value="1" required placeholder="Jumlah" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-blue-500">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium transition">
            + Tambahkan
        </button>
    </form>

    <!-- Tabel Rekapan Pemakaian Sparepart di Tiket Ini -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-gray-600 font-semibold uppercase text-xs">
                <tr>
                    <th class="px-4 py-2 text-left">Kode & Nama Barang</th>
                    <th class="px-4 py-2 text-center">Jumlah</th>
                    <th class="px-4 py-2 text-right">Harga Satuan</th>
                    <th class="px-4 py-2 text-right">Subtotal</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($ticket->spareparts as $sp)
                <tr>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $sp->code }} - {{ $sp->name }}</td>
                    <td class="px-4 py-3 text-center font-semibold">{{ $sp->pivot->quantity }} {{ $sp->unit }}</td>
                    <td class="px-4 py-3 text-right">Rp {{ number_format($sp->pivot->unit_price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-800">Rp {{ number_format($sp->pivot->total_price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-center">
                        <form action="{{ route('tickets.spareparts.destroy', [$ticket->id, $sp->id]) }}" method="POST" onsubmit="return confirm('Hapus dan kembalikan stok sparepart ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-gray-400">Belum ada sparepart yang digunakan pada tiket ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 font-bold border-t border-gray-200">
                <tr>
                    <td colspan="3" class="px-4 py-3 text-right text-gray-700">Total Biaya Sparepart:</td>
                    <td class="px-4 py-3 text-right text-blue-600 text-base">
                        Rp {{ number_format($ticket->spareparts->sum('pivot.total_price'), 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>