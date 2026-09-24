<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Purchase extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'customer_id',
        'supplier_id',
        'supplier_nama_snapshot',
        'supplier_npwp_snapshot',
        'supplier_alamat_snapshot',
        'nomor_faktur',
        'tanggal_faktur',
        'termin',
        'potongan',
        'uang_muka',
        'dpp',
        'ppn',
        'purchase_document_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_faktur' => 'date',
            'termin' => 'decimal:2',
            'potongan' => 'decimal:2',
            'uang_muka' => 'decimal:2',
            'dpp' => 'decimal:2',
            'ppn' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'supplier_id', 'supplier_nama_snapshot', 'nomor_faktur',
                'tanggal_faktur', 'termin', 'potongan', 'uang_muka', 'dpp',
                'ppn', 'purchase_document_id',
            ])
            ->logOnlyDirty();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(PurchaseDocument::class, 'purchase_document_id');
    }

    /**
     * Dashboard card formula: Termin + PPN - Diskon (potongan).
     */
    public function getTotalAttribute(): string
    {
        return bcadd(bcsub((string) $this->termin, (string) $this->potongan, 2), (string) $this->ppn, 2);
    }
}
