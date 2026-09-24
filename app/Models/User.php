<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'role',
        'customer_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'SUPERADMIN';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'CUSTOMER';
    }

    /**
     * A CUSTOMER user can only log in / act while their Customer is ACTIVE.
     * SuperAdmin has no such restriction (see design decision: Users have
     * no separate active flag of their own).
     */
    public function canAccessApp(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->customer && $this->customer->isActive() && ! $this->customer->trashed();
    }
}
