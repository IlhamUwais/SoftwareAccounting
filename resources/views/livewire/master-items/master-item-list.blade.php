<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Master Barang</h2>
        <label class="text-sm flex items-center gap-2">
            <input type="checkbox" wire:model.live="showTrashed"> Tampilkan yang dihapus
        </label>
    </div>

    @if(session('status')) <div class="mb-3 text-sm text-green-700 bg-green-50 rounded p-2">{{ session('status') }}</div> @endif

    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama barang..."
           class="mb-4 w-full max-w-sm rounded border-gray-300 text-sm">

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b bg-gray-50">
                <th class="py-2 px-3">Nama Barang</th>
                <th class="py-2 px-3">Jumlah Transaksi</th>
                <th class="py-2 px-3">Aksi</th>
            </tr></thead>
            <tbody>
                @foreach($items as $item)
                    <tr class="border-b">
                        <td class="py-2 px-3">{{ $item->nama_barang }}</td>
                        <td class="py-2 px-3">{{ $item->purchase_details_count }}</td>
                        <td class="py-2 px-3">
                            @if($showTrashed)
                                <button wire:click="restore({{ $item->id }})" wire:confirm="Restore barang ini?" class="text-indigo-600 hover:underline">Restore</button>
                            @else
                                <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus barang ini?" class="text-red-600 hover:underline">Hapus</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
</div>
