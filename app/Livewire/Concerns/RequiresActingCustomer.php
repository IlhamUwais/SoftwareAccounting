<?php

namespace App\Livewire\Concerns;

use App\Support\TenantContext;

/**
 * Guards a Livewire component against a SuperAdmin browsing customer-scoped
 * data before picking an "acting customer" from the customer list screen.
 */
trait RequiresActingCustomer
{
    public function ensureCustomerSelected(): void
    {
        if (TenantContext::currentCustomerId() === null) {
            session()->flash('error', 'Pilih customer aktif terlebih dahulu.');
            $this->redirectRoute('customers.index');
        }
    }
}
