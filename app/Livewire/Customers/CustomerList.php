<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CustomerList extends Component
{
    public string $nama_perusahaan = '';
    public string $npwp = '';
    public string $username = '';
    public string $password = '';
    public bool $showForm = false;
    public bool $showTrashed = false;

    public ?int $editingCustomerId = null;
    public string $edit_nama_perusahaan = '';
    public string $edit_npwp = '';
    public string $edit_username = '';
    public string $edit_password = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        TenantContext::clearActingCustomer();
    }

    public function create(): void
    {
        $this->authorize('create', Customer::class);

        $this->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:50|alpha_dash|unique:users,username',
            'password' => ['required', 'string', Password::min(10)->mixedCase()->numbers()],
            'npwp' => 'required|string|max:255',
        ]);

        $customer = Customer::create(['nama_perusahaan' => $this->nama_perusahaan, 'npwp' => $this->npwp]);

        User::create([
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'role' => 'CUSTOMER',
            'customer_id' => $customer->id,
        ]);

        $this->reset(['nama_perusahaan', 'username', 'password', 'npwp','showForm']);
        session()->flash('status', 'Customer baru berhasil dibuat.');
    }

    public function edit(int $customerId): void
    {
        $customer = Customer::with('user')->findOrFail($customerId);
        $this->authorize('update', $customer);
        $this->editingCustomerId = $customerId;
        $this->edit_nama_perusahaan = $customer->nama_perusahaan;
        $this->edit_username = $customer->user?->username ?? '';
        $this->edit_password = '';
        $this->showForm = false;
        $this->edit_npwp = $customer->npwp;
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingCustomerId', 'edit_nama_perusahaan', 'edit_username', 'edit_password','edit_npwp']);
    }

    public function updateCustomer(): void
    {
        $customer = Customer::with('user')->findOrFail($this->editingCustomerId);
        $this->authorize('update', $customer);

        $userId = $customer->user?->id;

        $this->validate([
            'edit_nama_perusahaan' => 'required|string|max:255',
            'edit_username' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($userId)],
            'edit_password' => ['nullable', 'string', Password::min(10)->mixedCase()->numbers()],
            'edit_npwp' => 'required|string|max:255',
        ]);

        $customer->update(['nama_perusahaan' => $this->edit_nama_perusahaan,'npwp' => $this->edit_npwp]);

        if ($customer->user) {
            $customer->user->username = $this->edit_username;
            if (!empty($this->edit_password)) {
                $customer->user->password = Hash::make($this->edit_password);
            }
            $customer->user->save();
        } else {
            User::create([
                'username' => $this->edit_username,
                'password' => Hash::make($this->edit_password ?: 'password123'),
                'role' => 'CUSTOMER',
                'customer_id' => $customer->id,
            ]);
        }

        $this->cancelEdit();
        session()->flash('status', 'Customer berhasil diperbarui.');
    }

    public function toggleStatus(int $customerId): void
    {
        $customer = Customer::findOrFail($customerId);
        $this->authorize('update', $customer);
        $customer->update(['status' => $customer->status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE']);
    }

    public function select(int $customerId)
    {
        TenantContext::setActingCustomer($customerId);
        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function delete(int $customerId): void
    {
        $customer = Customer::findOrFail($customerId);
        $this->authorize('delete', $customer);
        $customer->delete();
        session()->flash('status', 'Customer berhasil dihapus.');
    }

    public function restore(int $customerId): void
    {
        $customer = Customer::onlyTrashed()->findOrFail($customerId);
        $this->authorize('restore', $customer);

        $conflict = Customer::where('npwp', $customer->npwp)
            ->whereNull('deleted_at')
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'npwp' => 'NPWP ini sudah dipakai oleh customer aktif lain. Tidak bisa memulihkan.',
            ]);
        }

        $customer->restore();
        session()->flash('status', 'Customer berhasil dipulihkan.');
    }

    public function render()
    {
        return view('livewire.customers.customer-list', [
            'customers' => Customer::with('user')
                ->when($this->showTrashed, fn ($q) => $q->onlyTrashed())
                ->orderBy('nama_perusahaan')->get(),
        ]);
    }
}
