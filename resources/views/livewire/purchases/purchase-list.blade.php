<div>
    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title">Daftar Pembelian</h1>
            <p class="page-subtitle">Riwayat faktur pajak masukan</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <label class="flex items-center gap-2 text-sm text-navy-600 cursor-pointer select-none">
                <input type="checkbox" wire:model.live="showTrashed"
                       class="rounded border-navy-300 text-gold-500 focus:ring-gold-400">
                Tampilkan yang dihapus
            </label>
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('purchases.upload') }}" wire:navigate class="btn-primary text-navy-900">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Upload FPM
                </a>
            @endif
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('status'))
        <div class="alert-success mb-4">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error mb-4">{{ session('error') }}</div>
    @endif

    {{-- ── Table card ── --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nomor Faktur</th>
                        <th>Supplier</th>
                        <th class="text-right">Termin</th>
                        <th class="text-right">PPN</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases as $p)
                        <tr>
                            <td class="whitespace-nowrap text-navy-600 text-xs">{{ $p->tanggal_faktur->format('d M Y') }}</td>
                            <td class="font-mono text-xs font-medium text-navy-800">{{ $p->nomor_faktur }}</td>
                            <td class="text-navy-700">{{ $p->supplier_nama_snapshot }}</td>
                            <td class="text-right tabular font-medium text-navy-900">Rp {{ number_format($p->termin, 0, ',', '.') }}</td>
                            <td class="text-right tabular text-navy-700">Rp {{ number_format($p->ppn, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-3">
                                    @if(auth()->user()->isSuperAdmin() && $p->purchase_document_id)
                                        <a href="{{ route('files.purchase-document', $p->purchase_document_id) }}"
                                           target="_blank"
                                           class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 transition-colors">
                                            PDF
                                        </a>
                                    @endif
                                    @if($showTrashed)
                                        <button wire:click="restore({{ $p->id }})"
                                                wire:confirm="Restore transaksi ini?"
                                                class="text-xs font-semibold text-gold-600 hover:text-gold-800 transition-colors">
                                            Restore
                                        </button>
                                    @else
                                        <a href="{{ route('purchases.edit', $p) }}" wire:navigate
                                           class="text-xs font-semibold text-navy-600 hover:text-navy-900 transition-colors">
                                            Edit
                                        </a>
                                        <button wire:click="delete({{ $p->id }})"
                                                wire:confirm="Hapus (soft delete) transaksi ini?"
                                                class="text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $purchases->links() }}</div>
</div>
