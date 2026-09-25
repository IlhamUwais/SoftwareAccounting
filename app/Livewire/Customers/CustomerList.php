<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CustomerList extends Component
{
    public string $nama_perusahaan = '';
    public string $username = '';
    public string $password = '';
    public bool $showForm = false;

    public ?int $editingCustomerId = null;
    public string $edit_nama_perusahaan = '';
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
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8',
        ]);

        $customer = Customer::create(['nama_perusahaan' => $this->nama_perusahaan]);

        User::create([
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'role' => 'CUSTOMER',
            'customer_id' => $customer->id,
        ]);

        $this->reset(['nama_perusahaan', 'username', 'password', 'showForm']);
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
    }

    public function cancelEdit(): void
    {
        $this->reset(['editingCustomerId', 'edit_nama_perusahaan', 'edit_username', 'edit_password']);
    }

    public function updateCustomer(): void
    {
        $customer = Customer::with('user')->findOrFail($this->editingCustomerId);
        $this->authorize('update', $customer);

        $userId = $customer->user?->id;

        $this->validate([
            'edit_nama_perusahaan' => 'required|string|max:255',
            'edit_username' => 'required|string|max:255|unique:users,username,' . $userId,
            'edit_password' => 'nullable|string|min:8',
        ]);

        $customer->update(['nama_perusahaan' => $this->edit_nama_perusahaan]);

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

    public function render()
    {
        return view('livewire.customers.customer-list', [
            'customers' => Customer::with('user')->orderBy('nama_perusahaan')->get(),
        ]);
    }
}
