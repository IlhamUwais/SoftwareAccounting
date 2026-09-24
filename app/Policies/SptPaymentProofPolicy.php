<?php

namespace App\Policies;

use App\Models\SptPaymentProof;
use App\Models\User;

class SptPaymentProofPolicy
{
    public function view(User $user, SptPaymentProof $proof): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->customer_id === $proof->sptDocument->customer_id;
    }

    // Bukti bayar can be added/replaced any time after the SPT already
    // exists, and deleted independently of the parent SPT - but only by
    // SuperAdmin, same access rule as the SPT file itself.
    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, SptPaymentProof $proof): bool
    {
        return $user->isSuperAdmin();
    }
}
