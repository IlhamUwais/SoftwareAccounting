<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    public string $username = '';
    public string $password = '';

    public function submit()
    {
        $this->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $throttleKey = 'login:'.strtolower($this->username).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('username', "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.");
            return;
        }

        if (! Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
            RateLimiter::hit($throttleKey, 60);
            $this->addError('username', 'Username atau password salah.');
            return;
        }

        RateLimiter::clear($throttleKey);
        request()->session()->regenerate();

        $user = Auth::user();

        if (! $user->canAccessApp()) {
            Auth::logout();
            $this->addError('username', 'Akun perusahaan Anda saat ini tidak aktif.');
            return;
        }

        $redirect = $user->isSuperAdmin() ? route('customers.index') : route('dashboard');
        $this->redirect($redirect, navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
