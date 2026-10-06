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

        // The parent SPT may have been soft-deleted; still resolve it so a
        // customer with a still-visible proof row doesn't hit a null-object
        // error instead of a clean "not authorized".
        $spt = $proof->sptDocument()->withTrashed()->first();

        if (! $spt) {
            return false;
        }

        return $user->customer_id === $spt->customer_id;
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

    public function restore(User $user, SptPaymentProof $proof): bool
    {
        return $user->isSuperAdmin();
    }
}
