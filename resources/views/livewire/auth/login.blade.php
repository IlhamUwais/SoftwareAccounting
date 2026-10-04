<div class="w-full max-w-sm mx-auto px-4">

    {{-- Logo / brand mark --}}
    <div class="flex flex-col items-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-navy-900 flex items-center justify-center shadow-card-md mb-4">
            <div class="w-9 h-9 rounded-lg bg-gold-500 flex items-center justify-center">
                <svg class="w-5 h-5 text-navy-900" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6zm1 2h6v1.5H7V6zm0 3h6v1.5H7V9zm0 3h4v1.5H7V12z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
        <h1 class="text-2xl font-bold text-navy-900">{{ config('app.name') }}</h1>
        <p class="text-sm text-navy-400 mt-1">Masuk ke akun Anda untuk melanjutkan</p>
    </div>

    {{-- Login card --}}
    <div class="card p-6 shadow-card-md">
        <form wire:submit="submit" class="space-y-5" novalidate>

            <div>
                <label for="login_username" class="form-label">Username</label>
                <input id="login_username"
                       type="text"
                       wire:model="username"
                       autofocus
                       autocomplete="username"
                       placeholder="Masukkan username Anda"
                       class="form-input @error('username') border-red-400 focus:ring-red-300 focus:border-red-400 @enderror">
                @error('username')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="login_password" class="form-label">Password</label>
                <input id="login_password"
                       type="password"
                       wire:model="password"
                       autocomplete="current-password"
                       placeholder="Masukkan password Anda"
                       class="form-input">
            </div>

            <button type="submit"
                    class="btn-primary w-full justify-center py-2.5 text-navy-900"
                    wire:loading.attr="disabled">
                <svg wire:loading wire:target="submit" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span wire:loading.remove wire:target="submit">Masuk</span>
                <span wire:loading wire:target="submit">Memproses...</span>
            </button>

        </form>
    </div>

    <p class="text-center text-xs text-navy-400 mt-6">
        &copy; {{ date('Y') }} {{ config('app.name') }}. Sistem Pencatatan Pajak.
    </p>
</div>
