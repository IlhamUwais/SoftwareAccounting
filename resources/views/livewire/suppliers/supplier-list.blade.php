<div>
    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title">Master Supplier</h1>
            <p class="page-subtitle">Kelola data pemasok / vendor</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <label class="flex items-center gap-2 text-sm text-navy-600 cursor-pointer select-none">
                <input type="checkbox" wire:model.live="showTrashed"
                       class="rounded border-navy-300 text-gold-500 focus:ring-gold-400">
                Tampilkan yang dihapus
            </label>
            <button wire:click="$toggle('showForm')" class="btn-primary text-navy-900">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Supplier
            </button>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('status'))
        <div class="alert-success mb-4">{{ session('status') }}</div>
    @endif

    {{-- ── Add form ── --}}
    @if($showForm)
        <div class="card mb-6">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-navy-800">Tambah Supplier Baru</h2>
            </div>
            <div class="card-body">
                <form wire:submit="save" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">Nama Supplier</label>
                        <input type="text" wire:model="nama" placeholder="Nama supplier" class="form-input">
                        @error('nama') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">NPWP</label>
                        <input type="text" wire:model="npwp" placeholder="NPWP" class="form-input">
                        @error('npwp') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Alamat <span class="font-normal normal-case text-navy-400">(opsional)</span></label>
                        <input type="text" wire:model="alamat" placeholder="Alamat" class="form-input">
                    </div>
                    <div class="sm:col-span-3 flex justify-end">
                        <button type="submit" class="btn-primary text-navy-900">Simpan Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ── Table ── --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NPWP</th>
                        <th>Alamat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $s)
                        <tr>
                            <td class="font-medium text-navy-800">{{ $s->nama }}</td>
                            <td class="font-mono text-xs text-navy-600">{{ $s->npwp }}</td>
                            <td class="text-navy-600 text-sm">{{ $s->alamat }}</td>
                            <td class="text-center">
                                @if($showTrashed)
                                    <button wire:click="restore({{ $s->id }})"
                                            wire:confirm="Restore supplier ini?"
                                            class="text-xs font-semibold text-gold-600 hover:text-gold-800 transition-colors">
                                        Restore
                                    </button>
                                @else
                                    <button wire:click="delete({{ $s->id }})"
                                            wire:confirm="Hapus supplier ini?"
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

    <div class="mt-5">{{ $suppliers->links() }}</div>
</div>
