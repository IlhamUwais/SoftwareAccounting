<?php

namespace App\Policies;

use App\Models\Purchase;
use App\Models\User;

class PurchasePolicy
{
    /**
     * The core tenant-isolation rule for this whole app:
     * - SUPERADMIN can view/manage any purchase.
     * - CUSTOMER can only view/manage purchases belonging to THEIR OWN
     *   customer_id, taken from the authenticated session - never from
     *   any id/customer_id supplied by the client.
     */
    public function view(User $user, Purchase $purchase): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->customer_id === $purchase->customer_id;
    }

    public function create(User $user): bool
    {
        // Only SuperAdmin uploads/creates purchases.
        return $user->isSuperAdmin();
    }

    public function update(User $user, Purchase $purchase): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Purchase $purchase): bool
    {
        return $user->isSuperAdmin();
    }

    public function restore(User $user, Purchase $purchase): bool
    {
        return $user->isSuperAdmin();
    }
}
