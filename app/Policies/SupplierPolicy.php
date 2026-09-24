<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    public function view(User $user, Supplier $supplier): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->customer_id === $supplier->customer_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return $user->isSuperAdmin();
    }
}
