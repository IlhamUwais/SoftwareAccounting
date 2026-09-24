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
            'customers' => Customer::orderBy('nama_perusahaan')->get(),
        ]);
    }
}
