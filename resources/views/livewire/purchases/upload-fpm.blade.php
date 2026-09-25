<div>
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-lg font-semibold">Upload FPM (Faktur Pajak Masukan)</h2>
            <p class="text-xs text-gray-500">Unggah file PDF faktur pajak masukan secara batch</p>
        </div>
        <a href="{{ route('purchases.index') }}" wire:navigate class="text-sm text-indigo-600 hover:underline">
            &larr; Kembali ke Pembelian
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File PDF</label>
        <input type="file" wire:model="files" multiple accept="application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        @error('files.*') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

        <div wire:loading wire:target="files" class="text-sm text-indigo-600 mt-2">Mengunggah file ke server...</div>

        <div class="mt-4">
            <button wire:click="upload" wire:loading.attr="disabled"
                    class="bg-indigo-600 text-white rounded px-5 py-2 text-sm font-medium hover:bg-indigo-700 disabled:opacity-50">
                Mulai Proses Ekstraksi
            </button>
        </div>
    </div>

    @if($batch)
        <div class="bg-white rounded-lg shadow p-5 mb-6 border-l-4 border-indigo-500" wire:poll.3s="$refresh">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-semibold text-sm text-gray-800">
                    Detail Batch #{{ $batch->id }} ({{ $batch->created_at->format('d/m/Y H:i:s') }})
                </h3>
                <button wire:click="closeBatchDetail" class="text-xs text-gray-500 hover:text-gray-800">Tutup Detail</button>
            </div>

            <p class="text-sm text-gray-600 mb-3">
                Status: <span @class([
                    'px-2 py-0.5 rounded text-xs font-semibold uppercase',
                    'bg-green-100 text-green-700' => $batch->status === 'COMPLETED',
                    'bg-yellow-100 text-yellow-700' => $batch->status === 'PROCESSING',
                    'bg-red-100 text-red-700' => $batch->status === 'FAILED',
                    'bg-gray-100 text-gray-600' => $batch->status === 'PENDING',
                ])>{{ $batch->status }}</span> —
                <span class="text-green-600 font-medium">{{ $batch->success_count }} berhasil</span>,
                <span class="text-red-600 font-medium">{{ $batch->failed_count }} gagal</span> dari
                <span class="font-medium">{{ $batch->total_files }} file</span>
            </p>

            <table class="w-full text-sm mt-3">
                <thead>
                    <tr class="text-left text-gray-500 border-b bg-gray-50">
                        <th class="py-2 px-3">Nama File</th>
                        <th class="py-2 px-3">Status</th>
                        <th class="py-2 px-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batch->files as $f)
                        <tr class="border-b">
                            <td class="py-2 px-3 font-mono text-xs">{{ $f->filename }}</td>
                            <td class="py-2 px-3">
                                <span @class([
                                    'text-xs font-semibold px-2 py-0.5 rounded',
                                    'bg-green-100 text-green-700' => $f->status === 'SUCCESS',
                                    'bg-red-100 text-red-700' => $f->status === 'FAILED',
                                    'bg-yellow-100 text-yellow-700' => $f->status === 'PENDING',
                                ])>{{ $f->status }}</span>
                            </td>
                            <td class="py-2 px-3 text-xs text-gray-600">{{ $f->failure_reason ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($recentBatches && $recentBatches->count() > 0)
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Riwayat Batch Upload Sebelumnya</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b bg-gray-50">
                        <th class="py-2 px-3">ID</th>
                        <th class="py-2 px-3">Waktu Upload</th>
                        <th class="py-2 px-3">Total File</th>
                        <th class="py-2 px-3">Hasil</th>
                        <th class="py-2 px-3">Status</th>
                        <th class="py-2 px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentBatches as $rb)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-3 text-gray-500 font-mono text-xs">#{{ $rb->id }}</td>
                            <td class="py-2 px-3 text-xs">{{ $rb->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2 px-3">{{ $rb->total_files }}</td>
                            <td class="py-2 px-3 text-xs">
                                <span class="text-green-600 font-medium">{{ $rb->success_count }} sukses</span>,
                                <span class="text-red-600 font-medium">{{ $rb->failed_count }} gagal</span>
                            </td>
                            <td class="py-2 px-3">
                                <span @class([
                                    'text-xs font-semibold px-2 py-0.5 rounded',
                                    'bg-green-100 text-green-700' => $rb->status === 'COMPLETED',
                                    'bg-yellow-100 text-yellow-700' => $rb->status === 'PROCESSING',
                                    'bg-red-100 text-red-700' => $rb->status === 'FAILED',
                                    'bg-gray-100 text-gray-600' => $rb->status === 'PENDING',
                                ])>{{ $rb->status }}</span>
                            </td>
                            <td class="py-2 px-3">
                                <button wire:click="selectBatch({{ $rb->id }})" class="text-xs text-indigo-600 hover:underline">
                                    Lihat File
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">{{ $recentBatches->links() }}</div>
        </div>
    @endif
</div>
