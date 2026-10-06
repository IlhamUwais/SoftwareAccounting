<div>
    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title">SPT</h1>
            <p class="page-subtitle">Surat Pemberitahuan Tahunan &amp; bukti pembayaran</p>
        </div>
        <div class="flex items-center gap-3 self-start">
            <label class="flex items-center gap-2 text-sm text-navy-500">
                <input type="checkbox" wire:model.live="showTrashed" class="rounded border-navy-300">
                Tampilkan yang dihapus
            </label>
            @can('create', \App\Models\SptDocument::class)
                <button wire:click="$toggle('showForm')" class="btn-primary text-navy-900">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Upload SPT
                </button>
            @endcan
        </div>
    </div>

    {{-- ── Upload modal ── --}}
    @if($showForm && ! $editingId)
        <x-modal title="Upload SPT Baru" close-wire-click="$toggle('showForm')">
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="form-label">Jenis SPT</label>
                    <select wire:model="jenis_spt" class="form-select">
                        @foreach(\App\Models\SptDocument::JENIS_OPTIONS as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Periode Mulai</label>
                        <input type="date" wire:model="period_start" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Periode Akhir</label>
                        <input type="date" wire:model="period_end" class="form-input">
                    </div>
                </div>
                <div>
                    <label class="form-label">File Dokumen</label>
                    <input type="file" wire:model="file"
                           class="block w-full text-sm text-navy-600
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-gold-500 file:text-navy-900
                                  hover:file:bg-gold-600 file:cursor-pointer file:transition-colors">
                    @error('file') <p class="form-error">{{ $message }}</p> @enderror
                    <p class="text-xs text-navy-400 mt-1.5">Format diizinkan: PDF, JPG, JPEG, PNG, DOC, DOCX, XLS, XLSX (maks 10MB)</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$toggle('showForm')" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary text-navy-900">Upload SPT</button>
                </div>
            </form>
        </x-modal>
    @endif

    {{-- ── Edit modal ── --}}
    @if($editingId)
        <x-modal title="Edit SPT" close-wire-click="cancelEdit">
            <form wire:submit="updateSpt" class="space-y-4">
                <div>
                    <label class="form-label">Jenis SPT</label>
                    <select wire:model="jenis_spt" class="form-select">
                        @foreach(\App\Models\SptDocument::JENIS_OPTIONS as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Periode Mulai</label>
                        <input type="date" wire:model="period_start" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Periode Akhir</label>
                        <input type="date" wire:model="period_end" class="form-input">
                    </div>
                </div>
                <p class="text-xs text-navy-400">File dokumen tidak bisa diganti di sini &mdash; hapus dan upload ulang jika filenya perlu diganti.</p>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="cancelEdit" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary text-navy-900">Simpan Perubahan</button>
                </div>
            </form>
        </x-modal>
    @endif

    {{-- ── SPT document list ── --}}
    <div class="space-y-4">
        @forelse($sptDocuments as $spt)
            <div class="card {{ $spt->trashed() ? 'opacity-60' : '' }}">
                <div class="card-body">
                    {{-- Document info header --}}
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div>
                            <p class="font-semibold text-navy-900">
                                {{ \App\Models\SptDocument::JENIS_OPTIONS[$spt->jenis_spt] }}
                                @if($spt->trashed())
                                    <span class="badge badge-red ml-2">DIHAPUS</span>
                                @endif
                            </p>
                            <p class="text-xs text-navy-500 mt-0.5">
                                {{ $spt->period_start->format('d M Y') }} &mdash; {{ $spt->period_end->format('d M Y') }}
                            </p>
                            <a href="{{ route('files.spt', $spt) }}"
                               class="inline-flex items-center gap-1 text-xs text-gold-600 hover:text-gold-800 font-medium mt-1.5 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ $spt->original_filename }}
                            </a>
                        </div>
                        <div class="flex items-center gap-3 self-start">
                            @if($spt->trashed())
                                @can('restore', $spt)
                                    <button wire:click="restore({{ $spt->id }})"
                                            class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 transition-colors">
                                        Pulihkan
                                    </button>
                                @endcan
                            @else
                                @can('update', $spt)
                                    <button wire:click="edit({{ $spt->id }})"
                                            class="text-xs font-semibold text-navy-500 hover:text-navy-800 transition-colors">
                                        Edit
                                    </button>
                                @endcan
                                @can('delete', $spt)
                                    <button wire:click="delete({{ $spt->id }})"
                                            wire:confirm="Hapus SPT ini?"
                                            class="text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">
                                        Hapus
                                    </button>
                                @endcan
                            @endif
                        </div>
                    </div>

                    {{-- Payment proofs section --}}
                    <div class="mt-4 pt-3 border-t border-navy-50">
                        <p class="text-xs font-semibold text-navy-500 mb-2">Bukti Bayar</p>

                        @forelse($spt->paymentProofs as $proof)
                            <div class="flex items-center justify-between py-1.5 {{ $proof->trashed() ? 'opacity-60' : '' }}">
                                <a href="{{ route('files.spt-proof', $proof) }}"
                                   class="flex items-center gap-1.5 text-xs text-gold-600 hover:text-gold-800 font-medium transition-colors">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    {{ $proof->original_filename }}
                                    @if($proof->trashed())
                                        <span class="badge badge-red ml-1">DIHAPUS</span>
                                    @endif
                                </a>
                                @if($proof->trashed())
                                    @can('restore', $proof)
                                        <button wire:click="restoreProof({{ $proof->id }})"
                                                class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 transition-colors ml-3 flex-shrink-0">
                                            Pulihkan
                                        </button>
                                    @endcan
                                @else
                                    @can('delete', $proof)
                                        <button wire:click="deleteProof({{ $proof->id }})"
                                                wire:confirm="Hapus bukti bayar ini?"
                                                class="text-xs text-red-400 hover:text-red-600 transition-colors ml-3 flex-shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    @endcan
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-navy-300">Belum ada bukti bayar.</p>
                        @endforelse

                        @can('create', \App\Models\SptPaymentProof::class)
                            @if(! $spt->trashed())
                                @if($addProofToSptId === $spt->id)
                                    <div class="flex flex-wrap items-center gap-3 mt-3 pt-2 border-t border-navy-50">
                                        <input type="file" wire:model="proofFile"
                                               class="text-xs text-navy-600
                                                      file:mr-3 file:py-1.5 file:px-3
                                                      file:rounded-md file:border-0
                                                      file:text-xs file:font-semibold
                                                      file:bg-navy-100 file:text-navy-700
                                                      hover:file:bg-navy-200 file:cursor-pointer file:transition-colors">
                                        <button wire:click="addProof" class="btn-secondary text-xs px-3 py-1.5">Simpan</button>
                                        <button type="button" wire:click="cancelAddProof" class="text-xs text-navy-400 hover:text-navy-700">Batal</button>
                                    </div>
                                    @error('proofFile') <p class="form-error">{{ $message }}</p> @enderror
                                @else
                                    <button wire:click="showAddProofForm({{ $spt->id }})"
                                            class="mt-2 text-xs font-semibold text-navy-500 hover:text-navy-800 transition-colors flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Tambah bukti bayar
                                    </button>
                                @endif
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <p class="text-navy-400 text-sm text-center py-8">Tidak ada SPT.</p>
        @endforelse
    </div>

    <div class="mt-5">{{ $sptDocuments->links() }}</div>
</div>
