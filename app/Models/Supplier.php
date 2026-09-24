<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = ['customer_id', 'nama', 'npwp', 'alamat'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['nama', 'npwp', 'alamat'])->logOnlyDirty();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Find an existing active supplier by NPWP for a given customer, or
     * create a new one automatically (no admin approval required), per
     * the FPM upload matching rule.
     */
    public static function findOrCreateForCustomer(int $customerId, string $nama, string $npwp, ?string $alamat): self
    {
        return static::withoutTrashed()
            ->where('customer_id', $customerId)
            ->where('npwp', $npwp)
            ->first() ?? static::create([
                'customer_id' => $customerId,
                'nama' => $nama,
                'npwp' => $npwp,
                'alamat' => $alamat,
            ]);
    }
}
