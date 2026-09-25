<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Daftar Customer</h2>
        <button wire:click="$toggle('showForm')" class="text-sm bg-indigo-600 text-white rounded px-3 py-1.5">+ Customer Baru</button>
    </div>

    @if(session('status')) <div class="mb-3 text-sm text-green-700 bg-green-50 rounded p-2">{{ session('status') }}</div> @endif

    @if($showForm)
<form wire:submit="create" class="bg-white rounded-lg shadow p-4 mb-4 grid grid-cols-3 gap-3">

    <div>
        <input
            type="text"
            wire:model="nama_perusahaan"
            placeholder="Nama perusahaan"
            class="rounded border-gray-300 text-sm w-full"
        >

        @error('nama_perusahaan')
            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <input
            type="text"
            wire:model="username"
            placeholder="Username login"
            class="rounded border-gray-300 text-sm w-full"
        >

        @error('username')
            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <input
            type="password"
            wire:model="password"
            placeholder="Password"
            class="rounded border-gray-300 text-sm w-full"
        >

        @error('password')
            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button
        type="submit"
        class="col-span-3 bg-indigo-600 text-white rounded py-1.5 text-sm"
    >
        Buat Customer
    </button>

</form>
    @endif

    @if($editingCustomerId)
        <form wire:submit="updateCustomer" class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-4 grid grid-cols-3 gap-3">
            <div class="col-span-3 font-medium text-sm text-indigo-900">Edit Customer & Akun Login</div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Nama Perusahaan</label>
                <input type="text" wire:model="edit_nama_perusahaan" placeholder="Nama perusahaan" class="rounded border-gray-300 text-sm w-full">
                @error('edit_nama_perusahaan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Username Login</label>
                <input type="text" wire:model="edit_username" placeholder="Username login" class="rounded border-gray-300 text-sm w-full">
                @error('edit_username') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Password Baru (opsional)</label>
                <input type="password" wire:model="edit_password" placeholder="Kosongkan jika tidak diubah" class="rounded border-gray-300 text-sm w-full">
                @error('edit_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="col-span-3 flex gap-2 mt-2">
                <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-1.5 text-sm">Simpan Perubahan</button>
                <button type="button" wire:click="cancelEdit" class="bg-gray-200 text-gray-700 rounded px-4 py-1.5 text-sm">Batal</button>
            </div>
        </form>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($customers as $c)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium">{{ $c->nama_perusahaan }}</p>
                        <p class="text-xs text-gray-500">{{ $c->user ? '@'.$c->user->username : 'Belum ada akun' }}</p>
                    </div>
                    <span @class(['text-xs px-2 py-0.5 rounded', 'bg-green-100 text-green-700' => $c->status === 'ACTIVE', 'bg-gray-100 text-gray-500' => $c->status === 'INACTIVE'])>{{ $c->status }}</span>
                </div>
                <div class="flex gap-3 mt-3">
                    <button wire:click="select({{ $c->id }})" class="text-sm text-indigo-600 font-medium">Buka</button>
                    <button wire:click="edit({{ $c->id }})" class="text-sm text-blue-600">Edit</button>
                    <button wire:click="toggleStatus({{ $c->id }})" class="text-sm text-gray-500">
                        {{ $c->status === 'ACTIVE' ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
