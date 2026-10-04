<div>
    {{-- ── Header ── --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('purchases.index') }}" wire:navigate
           class="w-8 h-8 flex items-center justify-center rounded-lg border border-navy-200 text-navy-500 hover:bg-navy-100 hover:text-navy-800 transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="page-title">Edit Pembelian</h1>
            <p class="page-subtitle font-mono text-xs">{{ $purchase->nomor_faktur }}</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">

        {{-- ── Header fields ── --}}
        <div class="card">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-navy-800">Informasi Faktur</h2>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="form-label">Supplier</label>
                        <select wire:model="supplier_id" class="form-select">
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->npwp }})</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <p class="form-error">{{ $message }}</p> @enderror
                        <p class="text-xs text-amber-600 mt-1.5 flex items-start gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Mengganti supplier akan melepas (soft-delete) dokumen PDF FPM yang sedang terlampir.
                        </p>
                    </div>

                    <div>
                        <label class="form-label">Nomor Faktur</label>
                        <input type="text" wire:model="nomor_faktur" class="form-input">
                        @error('nomor_faktur') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Tanggal Faktur</label>
                        <input type="date" wire:model="tanggal_faktur" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">Termin</label>
                        <input type="number" step="0.01" wire:model="termin" class="form-input tabular">
                    </div>

                    <div>
                        <label class="form-label">Potongan / Diskon</label>
                        <input type="number" step="0.01" wire:model="potongan" class="form-input tabular">
                    </div>

                    <div>
                        <label class="form-label">Uang Muka <span class="font-normal normal-case text-navy-400">(opsional)</span></label>
                        <input type="number" step="0.01" wire:model="uang_muka" class="form-input tabular">
                    </div>

                    <div>
                        <label class="form-label">DPP</label>
                        <input type="number" step="0.01" wire:model="dpp" class="form-input tabular">
                    </div>

                    <div>
                        <label class="form-label">PPN</label>
                        <input type="number" step="0.01" wire:model="ppn" class="form-input tabular">
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Detail Barang ── --}}
        <div class="card">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-navy-800">Detail Barang</h2>
                <button type="button" wire:click="addDetailRow"
                        class="btn-secondary text-xs px-3 py-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Baris
                </button>
            </div>
            <div class="card-body space-y-3">
                @foreach($details as $i => $row)
                    <div class="grid grid-cols-12 gap-2 items-start p-3 rounded-lg bg-navy-50 border border-navy-100">
                        <input type="text"
                               wire:model="details.{{ $i }}.nama_barang_snapshot"
                               placeholder="Nama barang"
                               class="form-input col-span-12 sm:col-span-5 text-sm">
                        <input type="number" step="0.01"
                               wire:model="details.{{ $i }}.harga_satuan"
                               placeholder="Harga satuan"
                               class="form-input col-span-6 sm:col-span-3 text-sm tabular">
                        <input type="number" step="0.001"
                               wire:model="details.{{ $i }}.quantity"
                               placeholder="Qty"
                               class="form-input col-span-3 sm:col-span-2 text-sm tabular">
                        <input type="text"
                               wire:model="details.{{ $i }}.satuan"
                               placeholder="Satuan"
                               class="form-input col-span-2 sm:col-span-1 text-sm">
                        <button type="button" wire:click="removeDetailRow({{ $i }})"
                                class="col-span-1 flex items-center justify-center w-8 h-9 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors self-start mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endforeach

                @if(empty($details))
                    <p class="text-sm text-navy-400 text-center py-4">Belum ada detail barang. Klik "+ Tambah Baris" untuk menambahkan.</p>
                @endif
            </div>
        </div>

        {{-- ── Actions ── --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('purchases.index') }}" wire:navigate class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary text-navy-900"
                    wire:loading.attr="disabled">
                <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>
