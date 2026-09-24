<div>
    <h2 class="text-lg font-semibold mb-4">Master Barang</h2>

    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama barang..."
           class="mb-4 w-full max-w-sm rounded border-gray-300 text-sm">

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b bg-gray-50">
                <th class="py-2 px-3">Nama Barang</th><th class="py-2 px-3">Jumlah Transaksi</th>
            </tr></thead>
            <tbody>
                @foreach($items as $item)
                    <tr class="border-b">
                        <td class="py-2 px-3">{{ $item->nama_barang }}</td>
                        <td class="py-2 px-3">{{ $item->purchase_details_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
</div>
