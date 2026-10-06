<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class UserProfile extends Component
{
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function updatePassword(): void
    {
        $throttleKey = 'update-password:'.Auth::id();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            $this->addError('current_password', "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.");

            return;
        }

        RateLimiter::hit($throttleKey, 60);

        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', Password::min(10)->mixedCase()->numbers(), 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini salah.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        RateLimiter::clear($throttleKey);

        Auth::user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        // Password changed - kill any other active sessions for this
        // account so a stolen session doesn't survive the password reset.
        Auth::logoutOtherDevices($this->new_password);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('status', 'Password berhasil diperbarui. Sesi login lain telah diakhiri.');
    }

    public function render()
    {
        return view('livewire.profile.user-profile', [
            'user' => Auth::user()->load('customer'),
        ]);
    }
}
