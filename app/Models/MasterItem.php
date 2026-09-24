<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MasterItem extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = ['customer_id', 'nama_barang'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['nama_barang'])->logOnlyDirty();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    /**
     * Item identity = name only (not price, not quantity, not item code).
     * Auto-create if it doesn't exist yet for this customer.
     */
    public static function findOrCreateForCustomer(int $customerId, string $namaBarang): self
    {
        return static::withoutTrashed()
            ->where('customer_id', $customerId)
            ->where('nama_barang', $namaBarang)
            ->first() ?? static::create([
                'customer_id' => $customerId,
                'nama_barang' => $namaBarang,
            ]);
    }
}
