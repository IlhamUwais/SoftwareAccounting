<?php

namespace App\Policies;

use App\Models\MasterItem;
use App\Models\User;

class MasterItemPolicy
{
    public function view(User $user, MasterItem $item): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->customer_id === $item->customer_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, MasterItem $item): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, MasterItem $item): bool
    {
        return $user->isSuperAdmin();
    }
}
