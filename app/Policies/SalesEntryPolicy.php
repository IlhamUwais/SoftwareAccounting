<?php

namespace App\Policies;

use App\Models\SalesEntry;
use App\Models\User;

class SalesEntryPolicy
{
    public function view(User $user, SalesEntry $entry): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->customer_id === $entry->customer_id;
    }

    public function create(User $user): bool
    {
        // Only SuperAdmin inputs omzet.
        return $user->isSuperAdmin();
    }

    public function update(User $user, SalesEntry $entry): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, SalesEntry $entry): bool
    {
        return $user->isSuperAdmin();
    }
}
