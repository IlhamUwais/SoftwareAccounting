<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

/**
 * Resolves "which customer's data am I currently looking at".
 * - CUSTOMER users: always their own customer_id, never overridable.
 * - SUPERADMIN users: whichever customer they picked from the customer
 *   list screen, stored in session (per-browser-session, not persisted).
 */
class TenantContext
{
    public const SESSION_KEY = 'acting_customer_id';

    public static function currentCustomerId(): ?int
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        if ($user->isCustomer()) {
            return $user->customer_id;
        }

        // SUPERADMIN
        return session(self::SESSION_KEY);
    }

    public static function setActingCustomer(int $customerId): void
    {
        session([self::SESSION_KEY => $customerId]);
    }

    public static function clearActingCustomer(): void
    {
        session()->forget(self::SESSION_KEY);
    }
}
