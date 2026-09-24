<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">SPT</h2>
        @can('create', \App\Models\SptDocument::class)
            <button wire:click="$toggle('showForm')" class="text-sm bg-indigo-600 text-white rounded px-3 py-1.5">+ Upload SPT</button>
        @endcan
    </div>

    @if(session('status')) <div class="mb-3 text-sm text-green-700 bg-green-50 rounded p-2">{{ session('status') }}</div> @endif

    @if($showForm)
        <form wire:submit="save" class="bg-white rounded-lg shadow p-4 mb-4 space-y-3">
            <select wire:model="jenis_spt" class="w-full rounded border-gray-300 text-sm">
                @foreach(\App\Models\SptDocument::JENIS_OPTIONS as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
            </select>
            <div class="grid grid-cols-2 gap-3">
                <input type="date" wire:model="period_start" class="rounded border-gray-300 text-sm">
                <input type="date" wire:model="period_end" class="rounded border-gray-300 text-sm">
            </div>
            <input type="file" wire:model="file" class="text-sm">
            @error('file') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            <p class="text-xs text-gray-400">Format diizinkan: PDF, JPG, JPEG, PNG, DOC, DOCX, XLS, XLSX (maks 10MB)</p>
            <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-1.5 text-sm">Upload</button>
        </form>
    @endif

    <div class="space-y-3">
        @foreach($sptDocuments as $spt)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium text-sm">{{ \App\Models\SptDocument::JENIS_OPTIONS[$spt->jenis_spt] }}</p>
                        <p class="text-xs text-gray-500">{{ $spt->period_start->format('d M Y') }} — {{ $spt->period_end->format('d M Y') }}</p>
                        <a href="{{ route('files.spt', $spt) }}" class="text-xs text-indigo-600">{{ $spt->original_filename }} (download)</a>
                    </div>
                    @can('delete', $spt)
                        <button wire:click="delete({{ $spt->id }})" wire:confirm="Hapus SPT ini?" class="text-xs text-red-600">Hapus</button>
                    @endcan
                </div>

                <div class="mt-3 pl-2 border-l-2 border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Bukti Bayar:</p>
                    @forelse($spt->paymentProofs as $proof)
                        <div class="flex justify-between items-center text-xs mb-1">
                            <a href="{{ route('files.spt-proof', $proof) }}" class="text-indigo-600">{{ $proof->original_filename }}</a>
                            @can('delete', $proof)
                                <button wire:click="deleteProof({{ $proof->id }})" wire:confirm="Hapus bukti bayar ini?" class="text-red-500">✕</button>
                            @endcan
                        </div>
                    @empty
                        <p class="text-xs text-gray-300">Belum ada bukti bayar.</p>
                    @endforelse

                    @can('create', \App\Models\SptPaymentProof::class)
                        @if($addProofToSptId === $spt->id)
                            <div class="flex items-center gap-2 mt-2">
                                <input type="file" wire:model="proofFile" class="text-xs">
                                <button wire:click="addProof" class="text-xs text-indigo-600">Simpan</button>
                            </div>
                            @error('proofFile') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        @else
                            <button wire:click="$set('addProofToSptId', {{ $spt->id }})" class="text-xs text-indigo-600 mt-1">+ Tambah bukti bayar</button>
                        @endif
                    @endcan
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $sptDocuments->links() }}</div>
</div>
