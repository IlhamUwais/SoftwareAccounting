<?php

namespace App\Policies;

use App\Models\SptDocument;
use App\Models\User;

class SptDocumentPolicy
{
    // Customer CAN view/download (but never upload/edit/delete) their own SPT.
    public function view(User $user, SptDocument $spt): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->customer_id === $spt->customer_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, SptDocument $spt): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, SptDocument $spt): bool
    {
        return $user->isSuperAdmin();
    }
}
