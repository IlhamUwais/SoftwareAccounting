<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Customer extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = ['nama_perusahaan', 'status'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_perusahaan', 'status'])
            ->logOnlyDirty();
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function masterItems(): HasMany
    {
        return $this->hasMany(MasterItem::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function salesEntries(): HasMany
    {
        return $this->hasMany(SalesEntry::class);
    }

    public function sptDocuments(): HasMany
    {
        return $this->hasMany(SptDocument::class);
    }

    public function importBatches(): HasMany
    {
        return $this->hasMany(ImportBatch::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'ACTIVE';
    }
}
