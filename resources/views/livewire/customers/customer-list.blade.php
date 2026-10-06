<div>
    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title">Daftar Customer</h1>
            <p class="page-subtitle">Manajemen perusahaan klien dan akun loginnya</p>
        </div>
        <div class="flex items-center gap-3 self-start">
            <label class="flex items-center gap-2 text-sm text-navy-500">
                <input type="checkbox" wire:model.live="showTrashed" class="rounded border-navy-300">
                Tampilkan yang dihapus
            </label>
            <button wire:click="$toggle('showForm')" class="btn-primary text-navy-900">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Customer Baru
            </button>
        </div>
    </div>

    {{-- ── Create modal ── --}}
    @if($showForm)
        <x-modal title="Tambah Customer Baru" close-wire-click="$toggle('showForm')">
            <form wire:submit="create" class="grid grid-cols-1 gap-4">
                <div>
                    <label class="form-label">Nama Perusahaan</label>
                    <input type="text" wire:model="nama_perusahaan" placeholder="Nama perusahaan" class="form-input">
                    @error('nama_perusahaan') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">NPWP</label>
                    <input type="text" wire:model="npwp" placeholder="NPWP" class="form-input">
                    @error('npwp') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Username Login</label>
                    <input type="text" wire:model="username" placeholder="Username login" class="form-input" autocomplete="off">
                    @error('username') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Password</label>
                    <input type="password" wire:model="password" placeholder="Min. 10 karakter, huruf besar/kecil, angka" class="form-input" autocomplete="new-password">
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$toggle('showForm')" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary text-navy-900">Buat Customer</button>
                </div>
            </form>
        </x-modal>
    @endif

    {{-- ── Edit modal ── --}}
    @if($editingCustomerId)
        <x-modal title="Edit Customer &amp; Akun Login" close-wire-click="cancelEdit">
            <form wire:submit="updateCustomer" class="grid grid-cols-1 gap-4">
                <div>
                    <label class="form-label">Nama Perusahaan</label>
                    <input type="text" wire:model="edit_nama_perusahaan" placeholder="Nama perusahaan" class="form-input">
                    @error('edit_nama_perusahaan') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">NPWP</label>
                    <input type="text" wire:model="edit_npwp" placeholder="NPWP" class="form-input">
                    @error('edit_npwp') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Username Login</label>
                    <input type="text" wire:model="edit_username" placeholder="Username login" class="form-input" autocomplete="off">
                    @error('edit_username') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Password Baru <span class="font-normal normal-case text-navy-400">(opsional)</span></label>
                    <input type="password" wire:model="edit_password" placeholder="Kosongkan jika tidak diubah" class="form-input" autocomplete="new-password">
                    @error('edit_password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" wire:click="cancelEdit" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary text-navy-900">Simpan Perubahan</button>
                </div>
            </form>
        </x-modal>
    @endif

    {{-- ── Customer cards grid ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($customers as $c)
            <div class="card p-5 flex flex-col gap-3 {{ $c->trashed() ? 'opacity-60' : '' }}">
                {{-- Top: name + status --}}
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-navy-900 truncate">{{ $c->nama_perusahaan }}</p>
                        <p class="text-xs text-navy-400 mt-0.5">
                            {{ $c->user ? '@'.$c->user->username : 'Belum ada akun' }}
                        </p>
                    </div>
                    @if($c->trashed())
                        <span class="badge badge-red flex-shrink-0">DIHAPUS</span>
                    @else
                        <span @class([
                            'badge flex-shrink-0',
                            'badge-green' => $c->status === 'ACTIVE',
                            'badge-gray'  => $c->status === 'INACTIVE',
                        ])>{{ $c->status }}</span>
                    @endif
                </div>

                {{-- Divider --}}
                <div class="border-t border-navy-50"></div>

                {{-- Actions --}}
                @if($c->trashed())
                    <div class="flex items-center gap-4">
                        <button wire:click="restore({{ $c->id }})"
                                class="text-sm font-semibold text-emerald-600 hover:text-emerald-800 transition-colors">
                            Pulihkan
                        </button>
                    </div>
                @else
                    <div class="flex items-center gap-4 flex-wrap">
                        <button wire:click="select({{ $c->id }})"
                                class="text-sm font-semibold text-gold-600 hover:text-gold-800 transition-colors">
                            Buka
                        </button>
                        <button wire:click="edit({{ $c->id }})"
                                class="text-sm font-medium text-navy-500 hover:text-navy-800 transition-colors">
                            Edit
                        </button>
                        <button wire:click="toggleStatus({{ $c->id }})"
                                class="text-sm font-medium text-navy-400 hover:text-navy-700 transition-colors">
                            {{ $c->status === 'ACTIVE' ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                        <button wire:click="delete({{ $c->id }})"
                                wire:confirm="Hapus customer ini? Data tidak hilang permanen, bisa dipulihkan kembali."
                                class="text-sm font-medium text-red-500 hover:text-red-700 transition-colors ml-auto">
                            Hapus
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-navy-400 text-sm col-span-full text-center py-8">Tidak ada customer.</p>
        @endforelse
    </div>
</div>
