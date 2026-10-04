<div>
    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title">Penjualan / Omzet</h1>
            <p class="page-subtitle">Pencatatan omzet penjualan per periode</p>
        </div>
        <button wire:click="$toggle('showForm')" class="btn-primary text-navy-900 self-start">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Input Omzet
        </button>
    </div>

    {{-- Flash --}}
    @if(session('status'))
        <div class="alert-success mb-4">{{ session('status') }}</div>
    @endif

    {{-- ── Add form ── --}}
    @if($showForm)
        <div class="card mb-6">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-navy-800">Input Omzet Baru</h2>
            </div>
            <div class="card-body">
                <form wire:submit="save" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
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
                    <div>
                        <button type="submit" class="btn-primary text-navy-900 w-full justify-center py-2.5">
                            Simpan
                        </button>
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
                        <th>Periode</th>
                        <th class="text-right">Nominal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $e)
                        <tr>
                            <td class="font-medium text-navy-800">
                                {{ \Carbon\Carbon::create($e->periode_tahun, $e->periode_bulan)->translatedFormat('F Y') }}
                            </td>
                            <td class="text-right tabular font-semibold text-navy-900">
                                Rp {{ number_format($e->nominal, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <button wire:click="delete({{ $e->id }})"
                                        wire:confirm="Hapus entry ini?"
                                        class="text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $entries->links() }}</div>
</div>
