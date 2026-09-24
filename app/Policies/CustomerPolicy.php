<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    // Only SuperAdmin manages the list of customers (companies) at all.
    // A CUSTOMER user views their own profile through a dedicated
    // "my profile" route, not through this resource policy.
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->isSuperAdmin();
    }
}
