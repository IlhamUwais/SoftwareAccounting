<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Master Supplier</h2>
        <button wire:click="$toggle('showForm')" class="text-sm bg-indigo-600 text-white rounded px-3 py-1.5">+ Tambah</button>
    </div>

    @if(session('status')) <div class="mb-3 text-sm text-green-700 bg-green-50 rounded p-2">{{ session('status') }}</div> @endif

    @if($showForm)
        <form wire:submit="save" class="bg-white rounded-lg shadow p-4 mb-4 grid grid-cols-3 gap-3">
            <input type="text" wire:model="nama" placeholder="Nama supplier" class="rounded border-gray-300 text-sm">
            <input type="text" wire:model="npwp" placeholder="NPWP" class="rounded border-gray-300 text-sm">
            <input type="text" wire:model="alamat" placeholder="Alamat (opsional)" class="rounded border-gray-300 text-sm">
            <button type="submit" class="col-span-3 bg-indigo-600 text-white rounded py-1.5 text-sm">Simpan</button>
        </form>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b bg-gray-50">
                <th class="py-2 px-3">Nama</th><th class="py-2 px-3">NPWP</th><th class="py-2 px-3">Alamat</th><th class="py-2 px-3">Aksi</th>
            </tr></thead>
            <tbody>
                @foreach($suppliers as $s)
                    <tr class="border-b">
                        <td class="py-2 px-3">{{ $s->nama }}</td>
                        <td class="py-2 px-3">{{ $s->npwp }}</td>
                        <td class="py-2 px-3">{{ $s->alamat }}</td>
                        <td class="py-2 px-3">
                            <button wire:click="delete({{ $s->id }})" wire:confirm="Hapus supplier ini?" class="text-red-600 hover:underline">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $suppliers->links() }}</div>
</div>
