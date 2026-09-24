<div>
    <h2 class="text-lg font-semibold mb-4">Edit Pembelian — {{ $purchase->nomor_faktur }}</h2>

    <form wire:submit="save" class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-600">Supplier</label>
                <select wire:model="supplier_id" class="mt-1 w-full rounded border-gray-300">
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->npwp }})</option>
                    @endforeach
                </select>
                @error('supplier_id') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="text-xs text-amber-600 mt-1">Mengganti supplier akan melepas (soft-delete) dokumen PDF FPM yang sedang terlampir.</p>
            </div>

            <div>
                <label class="block text-sm text-gray-600">Nomor Faktur</label>
                <input type="text" wire:model="nomor_faktur" class="mt-1 w-full rounded border-gray-300">
                @error('nomor_faktur') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-600">Tanggal Faktur</label>
                <input type="date" wire:model="tanggal_faktur" class="mt-1 w-full rounded border-gray-300">
            </div>

            <div>
                <label class="block text-sm text-gray-600">Termin</label>
                <input type="number" step="0.01" wire:model="termin" class="mt-1 w-full rounded border-gray-300">
            </div>

            <div>
                <label class="block text-sm text-gray-600">Potongan / Diskon</label>
                <input type="number" step="0.01" wire:model="potongan" class="mt-1 w-full rounded border-gray-300">
            </div>

            <div>
                <label class="block text-sm text-gray-600">Uang Muka (opsional)</label>
                <input type="number" step="0.01" wire:model="uang_muka" class="mt-1 w-full rounded border-gray-300">
            </div>

            <div>
                <label class="block text-sm text-gray-600">DPP</label>
                <input type="number" step="0.01" wire:model="dpp" class="mt-1 w-full rounded border-gray-300">
            </div>

            <div>
                <label class="block text-sm text-gray-600">PPN</label>
                <input type="number" step="0.01" wire:model="ppn" class="mt-1 w-full rounded border-gray-300">
            </div>
        </div>

        <hr>

        <div>
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-semibold">Detail Barang</h3>
                <button type="button" wire:click="addDetailRow" class="text-xs text-indigo-600">+ Tambah baris</button>
            </div>

            @foreach($details as $i => $row)
                <div class="grid grid-cols-12 gap-2 mb-2 items-start">
                    <input type="text" wire:model="details.{{ $i }}.nama_barang_snapshot" placeholder="Nama barang"
                           class="col-span-4 rounded border-gray-300 text-sm">
                    <input type="number" step="0.01" wire:model="details.{{ $i }}.harga_satuan" placeholder="Harga"
                           class="col-span-3 rounded border-gray-300 text-sm">
                    <input type="number" step="0.001" wire:model="details.{{ $i }}.quantity" placeholder="Qty"
                           class="col-span-2 rounded border-gray-300 text-sm">
                    <input type="text" wire:model="details.{{ $i }}.satuan" placeholder="Satuan"
                           class="col-span-2 rounded border-gray-300 text-sm">
                    <button type="button" wire:click="removeDetailRow({{ $i }})" class="col-span-1 text-red-500 text-sm">✕</button>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end gap-2 pt-4">
            <a href="{{ route('purchases.index') }}" wire:navigate class="px-4 py-2 text-sm text-gray-600">Batal</a>
            <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
