<div class="max-w-2xl mx-auto">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <h1 class="page-title">Profil Pengguna</h1>
        <p class="page-subtitle">Informasi akun dan pengaturan kata sandi Anda</p>
    </div>

    {{-- ── Account info ── --}}
    <div class="card mb-5">
        <div class="card-header">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gold-500 flex items-center justify-center flex-shrink-0">
                    <span class="text-navy-900 font-bold text-sm uppercase">{{ substr($user->username, 0, 1) }}</span>
                </div>
                <h2 class="text-sm font-semibold text-navy-800">Informasi Akun</h2>
            </div>
        </div>
        <div class="card-body">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <dt class="text-xs font-semibold text-navy-500 uppercase tracking-wide mb-1">Username</dt>
                    <dd class="text-sm font-medium text-navy-900">{{ $user->username }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-navy-500 uppercase tracking-wide mb-1">Role</dt>
                    <dd>
                        <span class="badge {{ $user->role === 'SUPERADMIN' ? 'badge-yellow' : 'badge-blue' }}">
                            {{ $user->role }}
                        </span>
                    </dd>
                </div>
                @if($user->customer)
                    <div>
                        <dt class="text-xs font-semibold text-navy-500 uppercase tracking-wide mb-1">Perusahaan</dt>
                        <dd class="text-sm font-medium text-navy-900">{{ $user->customer->nama_perusahaan }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-navy-500 uppercase tracking-wide mb-1">NPWP</dt>
                        <dd class="text-sm font-medium text-navy-900 font-mono">{{ $user->customer->npwp ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-navy-500 uppercase tracking-wide mb-1">Status Akun</dt>
                        <dd>
                            <span @class([
                                'badge',
                                'badge-green' => $user->customer->status === 'ACTIVE',
                                'badge-gray'  => $user->customer->status !== 'ACTIVE',
                            ])>{{ $user->customer->status }}</span>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    {{-- ── Change password ── --}}
    <div class="card">
        <div class="card-header">
            <h2 class="text-sm font-semibold text-navy-800">Ubah Password</h2>
        </div>
        <div class="card-body">
            <form wire:submit="updatePassword" class="space-y-4">
                <div>
                    <label class="form-label">Password Saat Ini</label>
                    <input type="password" wire:model="current_password"
                           class="form-input @error('current_password') border-red-400 @enderror"
                           autocomplete="current-password">
                    @error('current_password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Password Baru</label>
                    <input type="password" wire:model="new_password"
                           class="form-input @error('new_password') border-red-400 @enderror"
                           autocomplete="new-password">
                    @error('new_password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" wire:model="new_password_confirmation"
                           class="form-input"
                           autocomplete="new-password">
                </div>
                <div class="pt-2">
                    <button type="submit" class="btn-primary text-navy-900"
                            wire:loading.attr="disabled">
                        <svg wire:loading wire:target="updatePassword" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
