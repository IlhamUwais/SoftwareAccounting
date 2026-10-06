<div>
    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title">Penjualan / Omzet</h1>
            <p class="page-subtitle">Pencatatan omzet penjualan per periode</p>
        </div>
        <div class="flex items-center gap-3 self-start">
            <label class="flex items-center gap-2 text-sm text-navy-500">
                <input type="checkbox" wire:model.live="showTrashed" class="rounded border-navy-300">
                Tampilkan yang dihapus
            </label>
            <button wire:click="$toggle('showForm')" class="btn-primary text-navy-900">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Input Omzet
            </button>
        </div>
    </div>

    {{-- ── Add modal ── --}}
    @if($showForm && ! $editingId)
        <x-modal title="Input Omzet Baru" close-wire-click="$toggle('showForm')">
            <form wire:submit="save" class="grid grid-cols-1 gap-4">
                <div>
                    <label class="form-label">Bulan</label>
                    <select wire:model="periode_bulan" class="form-select">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Tahun</label>
                    <input type="number" wire:model="periode_tahun" placeholder="Tahun" class="form-input tabular">
                    @error('periode_tahun') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Nominal</label>
                    <input type="number" step="0.01" wire:model="nominal" placeholder="Nominal omzet" class="form-input tabular">
                    @error('nominal') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$toggle('showForm')" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary text-navy-900">Simpan</button>
                </div>
            </form>
        </x-modal>
    @endif

    {{-- ── Edit modal ── --}}
    @if($editingId)
        <x-modal title="Edit Omzet" close-wire-click="cancelEdit">
            <form wire:submit="updateEntry" class="grid grid-cols-1 gap-4">
                <div>
                    <label class="form-label">Bulan</label>
                    <select wire:model="periode_bulan" class="form-select">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Tahun</label>
                    <input type="number" wire:model="periode_tahun" placeholder="Tahun" class="form-input tabular">
                    @error('periode_tahun') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Nominal</label>
                    <input type="number" step="0.01" wire:model="nominal" placeholder="Nominal omzet" class="form-input tabular">
                    @error('nominal') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="cancelEdit" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary text-navy-900">Simpan Perubahan</button>
                </div>
            </form>
        </x-modal>
    @endif

    {{-- ── Table ── --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th class="text-right">Nominal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $e)
                        <tr class="{{ $e->trashed() ? 'opacity-60' : '' }}">
                            <td class="font-medium text-navy-800">
                                {{ \Carbon\Carbon::create($e->periode_tahun, $e->periode_bulan)->translatedFormat('F Y') }}
                                @if($e->trashed())
                                    <span class="badge badge-red ml-2">DIHAPUS</span>
                                @endif
                            </td>
                            <td class="text-right tabular font-semibold text-navy-900">
                                Rp {{ number_format($e->nominal, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if($e->trashed())
                                    <button wire:click="restore({{ $e->id }})"
                                            class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 transition-colors">
                                        Pulihkan
                                    </button>
                                @else
                                    <button wire:click="edit({{ $e->id }})"
                                            class="text-xs font-semibold text-navy-500 hover:text-navy-800 transition-colors mr-3">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $e->id }})"
                                            wire:confirm="Hapus entry ini?"
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

    <div class="mt-5">{{ $entries->links() }}</div>
</div>
