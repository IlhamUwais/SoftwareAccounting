<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Penjualan / Omzet</h2>
        <button wire:click="$toggle('showForm')" class="text-sm bg-indigo-600 text-white rounded px-3 py-1.5">+ Input Omzet</button>
    </div>

    @if(session('status')) <div class="mb-3 text-sm text-green-700 bg-green-50 rounded p-2">{{ session('status') }}</div> @endif

    @if($showForm)
        <form wire:submit="save" class="bg-white rounded-lg shadow p-4 mb-4 grid grid-cols-4 gap-3">
            <select wire:model="periode_bulan" class="rounded border-gray-300 text-sm">
                @foreach(range(1,12) as $m) <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option> @endforeach
            </select>
            <input type="number" wire:model="periode_tahun" class="rounded border-gray-300 text-sm">
            <input type="number" step="0.01" wire:model="nominal" placeholder="Nominal" class="rounded border-gray-300 text-sm">
            <button type="submit" class="bg-indigo-600 text-white rounded text-sm">Simpan</button>
        </form>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b bg-gray-50">
                <th class="py-2 px-3">Periode</th><th class="py-2 px-3">Nominal</th><th class="py-2 px-3">Aksi</th>
            </tr></thead>
            <tbody>
                @foreach($entries as $e)
                    <tr class="border-b">
                        <td class="py-2 px-3">{{ \Carbon\Carbon::create($e->periode_tahun, $e->periode_bulan)->translatedFormat('F Y') }}</td>
                        <td class="py-2 px-3">Rp {{ number_format($e->nominal, 0, ',', '.') }}</td>
                        <td class="py-2 px-3">
                            <button wire:click="delete({{ $e->id }})" wire:confirm="Hapus entry ini?" class="text-red-600 hover:underline">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $entries->links() }}</div>
</div>
