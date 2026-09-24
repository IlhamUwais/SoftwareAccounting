<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Daftar Customer</h2>
        <button wire:click="$toggle('showForm')" class="text-sm bg-indigo-600 text-white rounded px-3 py-1.5">+ Customer Baru</button>
    </div>

    @if(session('status')) <div class="mb-3 text-sm text-green-700 bg-green-50 rounded p-2">{{ session('status') }}</div> @endif

    @if($showForm)
        <form wire:submit="create" class="bg-white rounded-lg shadow p-4 mb-4 grid grid-cols-3 gap-3">
            <input type="text" wire:model="nama_perusahaan" placeholder="Nama perusahaan" class="rounded border-gray-300 text-sm">
            <input type="text" wire:model="username" placeholder="Username login" class="rounded border-gray-300 text-sm">
            <input type="password" wire:model="password" placeholder="Password" class="rounded border-gray-300 text-sm">
            <button type="submit" class="col-span-3 bg-indigo-600 text-white rounded py-1.5 text-sm">Buat Customer</button>
            @error('username') <p class="text-xs text-red-600 col-span-3">{{ $message }}</p> @enderror
        </form>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($customers as $c)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex justify-between items-start">
                    <p class="font-medium">{{ $c->nama_perusahaan }}</p>
                    <span @class(['text-xs px-2 py-0.5 rounded', 'bg-green-100 text-green-700' => $c->status === 'ACTIVE', 'bg-gray-100 text-gray-500' => $c->status === 'INACTIVE'])>{{ $c->status }}</span>
                </div>
                <div class="flex gap-3 mt-3">
                    <button wire:click="select({{ $c->id }})" class="text-sm text-indigo-600">Buka</button>
                    <button wire:click="toggleStatus({{ $c->id }})" class="text-sm text-gray-500">
                        {{ $c->status === 'ACTIVE' ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
