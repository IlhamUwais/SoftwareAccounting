<div>
    <h2 class="text-lg font-semibold mb-4">Upload FPM (Faktur Pajak Masukan)</h2>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <input type="file" wire:model="files" multiple accept="application/pdf" class="mb-2">
        @error('files.*') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

        <div wire:loading wire:target="files" class="text-sm text-gray-400">Mengunggah...</div>

        <button wire:click="upload" wire:loading.attr="disabled"
                class="mt-2 bg-indigo-600 text-white rounded px-4 py-2 text-sm hover:bg-indigo-700">
            Proses Upload
        </button>
    </div>

    @if($batch)
        <div class="bg-white rounded-lg shadow p-4" wire:poll.2s="$refresh">
            <p class="text-sm mb-2">
                Status: <span class="font-medium">{{ $batch->status }}</span> —
                {{ $batch->success_count }} berhasil, {{ $batch->failed_count }} gagal dari {{ $batch->total_files }} file
            </p>

            <table class="w-full text-sm mt-3">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-1">File</th>
                        <th class="py-1">Status</th>
                        <th class="py-1">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batch->files as $f)
                        <tr class="border-b">
                            <td class="py-1">{{ $f->filename }}</td>
                            <td class="py-1">
                                <span @class([
                                    'text-green-600' => $f->status === 'SUCCESS',
                                    'text-red-600' => $f->status === 'FAILED',
                                    'text-gray-400' => $f->status === 'PENDING',
                                ])>{{ $f->status }}</span>
                            </td>
                            <td class="py-1 text-gray-500">{{ $f->failure_reason }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
