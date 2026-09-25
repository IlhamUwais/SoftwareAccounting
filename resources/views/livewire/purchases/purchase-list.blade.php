<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Daftar Pembelian</h2>
        <div class="flex items-center gap-4">
            <label class="text-sm flex items-center gap-2">
                <input type="checkbox" wire:model.live="showTrashed"> Tampilkan yang dihapus
            </label>
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('purchases.upload') }}" wire:navigate class="text-sm bg-indigo-600 text-white rounded px-3 py-1.5 hover:bg-indigo-700">
                    + Upload FPM
                </a>
            @endif
        </div>
    </div>

    @if(session('status')) <div class="mb-3 text-sm text-green-700 bg-green-50 rounded p-2">{{ session('status') }}</div> @endif
    @if(session('error')) <div class="mb-3 text-sm text-red-700 bg-red-50 rounded p-2">{{ session('error') }}</div> @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b bg-gray-50">
                    <th class="py-2 px-3">Tanggal</th>
                    <th class="py-2 px-3">Nomor Faktur</th>
                    <th class="py-2 px-3">Supplier</th>
                    <th class="py-2 px-3">Termin</th>
                    <th class="py-2 px-3">PPN</th>
                    <th class="py-2 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchases as $p)
                    <tr class="border-b">
                        <td class="py-2 px-3">{{ $p->tanggal_faktur->format('d M Y') }}</td>
                        <td class="py-2 px-3">{{ $p->nomor_faktur }}</td>
                        <td class="py-2 px-3">{{ $p->supplier_nama_snapshot }}</td>
                        <td class="py-2 px-3">Rp {{ number_format($p->termin, 0, ',', '.') }}</td>
                        <td class="py-2 px-3">Rp {{ number_format($p->ppn, 0, ',', '.') }}</td>
                        <td class="py-2 px-3">
                            @if(auth()->user()->isSuperAdmin() && $p->purchase_document_id)
                                <a href="{{ route('files.purchase-document', $p->purchase_document_id) }}" target="_blank" class="text-emerald-600 hover:underline mr-2">PDF</a>
                            @endif
                            @if($showTrashed)
                                <button wire:click="restore({{ $p->id }})" wire:confirm="Restore transaksi ini?"
                                        class="text-indigo-600 hover:underline">Restore</button>
                            @else
                                <a href="{{ route('purchases.edit', $p) }}" wire:navigate class="text-indigo-600 hover:underline mr-2">Edit</a>
                                <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus (soft delete) transaksi ini?"
                                        class="text-red-600 hover:underline">Hapus</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $purchases->links() }}</div>
</div>
