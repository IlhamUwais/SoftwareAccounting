<div>
    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title">Upload FPM</h1>
            <p class="page-subtitle">Unggah file PDF faktur pajak masukan secara batch</p>
        </div>
        <a href="{{ route('purchases.index') }}" wire:navigate class="btn-secondary self-start">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Pembelian
        </a>
    </div>

    {{-- ── Upload card ── --}}
    <div class="card mb-6">
        <div class="card-header">
            <h2 class="text-sm font-semibold text-navy-800">Pilih File PDF</h2>
        </div>
        <div class="card-body">
            <input type="file" wire:model="files" multiple accept="application/pdf"
                   class="block w-full text-sm text-navy-600
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-lg file:border-0
                          file:text-sm file:font-semibold
                          file:bg-gold-500 file:text-navy-900
                          hover:file:bg-gold-600 file:cursor-pointer file:transition-colors">
            @error('files.*')
                <p class="form-error">{{ $message }}</p>
            @enderror

            {{-- Upload progress indicator --}}
            <div wire:loading wire:target="files" class="mt-3 flex items-center gap-2 text-sm text-navy-500">
                <svg class="w-4 h-4 animate-spin text-gold-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Mengunggah file ke server...
            </div>

            <div class="mt-5 flex items-center gap-3">
                <button wire:click="prosessupload"
                        wire:loading.attr="disabled"
                        class="btn-primary text-navy-900">
                    <svg wire:loading.remove wire:target="prosessupload" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <svg wire:loading wire:target="prosessupload" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Mulai Proses Ekstraksi
                </button>
                <p class="text-xs text-navy-400" wire:loading.remove wire:target="prosessupload">File akan diproses secara antrian di latar belakang.</p>
                <p class="text-xs text-navy-500" wire:loading wire:target="prosessupload">Memproses, harap tunggu...</p>
            </div>
        </div>
    </div>

    {{-- ── Active batch detail ── --}}
    @if($batch)
        <div class="card mb-6 border-l-4 border-gold-500" wire:poll.3s="$refresh">
            <div class="card-header">
                <div>
                    <h2 class="text-sm font-semibold text-navy-800">
                        Batch #{{ $batch->id }}
                        <span class="font-normal text-navy-400 ml-1 text-xs">{{ $batch->created_at->format('d/m/Y H:i:s') }}</span>
                    </h2>
                    <div class="flex flex-wrap items-center gap-3 mt-1.5 text-sm">
                        <span @class([
                            'badge',
                            'badge-green'  => $batch->status === 'COMPLETED',
                            'badge-yellow' => $batch->status === 'PROCESSING',
                            'badge-red'    => $batch->status === 'FAILED',
                            'badge-gray'   => $batch->status === 'PENDING',
                        ])>{{ $batch->status }}</span>
                        <span class="text-xs text-navy-500">
                            <span class="text-emerald-600 font-semibold">{{ $batch->success_count }} berhasil</span>,
                            <span class="text-red-500 font-semibold">{{ $batch->failed_count }} gagal</span>
                            dari <span class="font-semibold text-navy-700">{{ $batch->total_files }} file</span>
                        </span>
                    </div>
                </div>
                <button wire:click="closeBatchDetail"
                        class="text-xs font-medium text-navy-400 hover:text-navy-700 transition-colors px-2 py-1 rounded hover:bg-navy-100 self-start">
                    Tutup Detail
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama File</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batch->files as $f)
                            <tr>
                                <td class="font-mono text-xs text-navy-700">{{ $f->filename }}</td>
                                <td>
                                    <span @class([
                                        'badge',
                                        'badge-green'  => $f->status === 'SUCCESS',
                                        'badge-red'    => $f->status === 'FAILED',
                                        'badge-yellow' => $f->status === 'PENDING',
                                    ])>{{ $f->status }}</span>
                                </td>
                                <td class="text-xs text-navy-500">{{ $f->failure_reason ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ── Recent batches ── --}}
    @if($recentBatches && $recentBatches->count() > 0)
        <div class="card overflow-hidden">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-navy-800">Riwayat Batch Upload</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Waktu Upload</th>
                            <th class="text-center">Total File</th>
                            <th>Hasil</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBatches as $rb)
                            <tr>
                                <td class="font-mono text-xs text-navy-500">#{{ $rb->id }}</td>
                                <td class="text-xs text-navy-600 whitespace-nowrap">{{ $rb->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center font-medium text-navy-800">{{ $rb->total_files }}</td>
                                <td class="text-xs">
                                    <span class="text-emerald-600 font-semibold">{{ $rb->success_count }} sukses</span>,
                                    <span class="text-red-500 font-semibold">{{ $rb->failed_count }} gagal</span>
                                </td>
                                <td>
                                    <span @class([
                                        'badge',
                                        'badge-green'  => $rb->status === 'COMPLETED',
                                        'badge-yellow' => $rb->status === 'PROCESSING',
                                        'badge-red'    => $rb->status === 'FAILED',
                                        'badge-gray'   => $rb->status === 'PENDING',
                                    ])>{{ $rb->status }}</span>
                                </td>
                                <td class="text-center">
                                    <button wire:click="selectBatch({{ $rb->id }})"
                                            class="text-xs font-semibold text-navy-600 hover:text-navy-900 transition-colors">
                                        Lihat File
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-navy-50">{{ $recentBatches->links() }}</div>
        </div>
    @endif
</div>
