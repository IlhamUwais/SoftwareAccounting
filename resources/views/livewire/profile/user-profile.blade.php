<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <h2 class="text-lg font-semibold">Profil Pengguna</h2>
        <p class="text-xs text-gray-500">Informasi akun dan pengaturan kata sandi Anda</p>
    </div>

    @if(session('status'))
        <div class="text-sm text-green-700 bg-green-50 rounded-lg p-3 border border-green-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-5">
        <h3 class="text-sm font-semibold text-gray-700 border-b pb-2 mb-4">Informasi Akun</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500 text-xs">Username</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $user->username }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs">Role</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ $user->role }}</dd>
            </div>
            @if($user->customer)
                <div>
                    <dt class="text-gray-500 text-xs">Perusahaan</dt>
                    <dd class="font-medium text-gray-900 mt-0.5">{{ $user->customer->nama_perusahaan }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs">Status Akun</dt>
                    <dd class="mt-0.5">
                        <span @class([
                            'text-xs px-2 py-0.5 rounded font-semibold',
                            'bg-green-100 text-green-700' => $user->customer->status === 'ACTIVE',
                            'bg-gray-100 text-gray-600' => $user->customer->status !== 'ACTIVE',
                        ])>{{ $user->customer->status }}</span>
                    </dd>
                </div>
            @endif
        </dl>
    </div>

    <div class="bg-white rounded-lg shadow p-5">
        <h3 class="text-sm font-semibold text-gray-700 border-b pb-2 mb-4">Ubah Password</h3>
        <form wire:submit="updatePassword" class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Password Saat Ini</label>
                <input type="password" wire:model="current_password" class="w-full rounded border-gray-300 text-sm">
                @error('current_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" wire:model="new_password" class="w-full rounded border-gray-300 text-sm">
                @error('new_password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" wire:model="new_password_confirmation" class="w-full rounded border-gray-300 text-sm">
            </div>

            <div class="pt-2">
                <button type="submit" class="bg-indigo-600 text-white rounded px-4 py-2 text-sm font-medium hover:bg-indigo-700">
                    Simpan Password Baru
                </button>
            </div>
        </form>
    </div>
</div>
