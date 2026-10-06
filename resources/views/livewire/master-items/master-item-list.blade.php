<div>
    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title">Master Barang</h1>
            <p class="page-subtitle">Katalog item yang pernah tercatat dalam transaksi</p>
        </div>
        <label class="flex items-center gap-2 text-sm text-navy-600 cursor-pointer select-none self-start">
            <input type="checkbox" wire:model.live="showTrashed"
                   class="rounded border-navy-300 text-gold-500 focus:ring-gold-400">
            Tampilkan yang dihapus
        </label>
    </div>

    {{-- Search --}}
    <div class="mb-4 relative">
        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-navy-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input type="text"
               wire:model.live.debounce.300ms="search"
               placeholder="Cari nama barang..."
               class="form-input pl-9 max-w-sm">
    </div>

    {{-- ── Table ── --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th class="text-center">Jumlah Transaksi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td class="font-medium text-navy-800">{{ $item->nama_barang }}</td>
                            <td class="text-center">
                                <span class="badge badge-blue">{{ $item->purchase_details_count }}</span>
                            </td>
                            <td class="text-center">
                                @if($showTrashed)
                                    <button wire:click="restore({{ $item->id }})"
                                            wire:confirm="Restore barang ini?"
                                            class="text-xs font-semibold text-gold-600 hover:text-gold-800 transition-colors">
                                        Restore
                                    </button>
                                @else
                                    <button wire:click="delete({{ $item->id }})"
                                            wire:confirm="Hapus barang ini?"
                                            class="text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">
                                        Hapus
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $items->links() }}</div>
</div>
