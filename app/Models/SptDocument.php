<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class SptDocument extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public const JENIS_OPTIONS = [
        'SPT_TAHUNAN' => 'SPT Tahunan',
        'SPT_MASA_UNIFIKASI' => 'SPT Masa Unifikasi',
        'SPT_MASA_PPH_21' => 'SPT Masa PPh 21',
        'SPT_MASA_PPN' => 'SPT Masa PPN',
        'SPT_MASA_PPH_PASAL_4' => 'SPT Masa PPh Pasal 4',
    ];

    protected $fillable = [
        'customer_id', 'jenis_spt', 'period_start', 'period_end',
        'file_reference', 'original_filename', 'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['jenis_spt', 'period_start', 'period_end'])->logOnlyDirty();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function paymentProofs(): HasMany
    {
        return $this->hasMany(SptPaymentProof::class);
    }

    /**
     * Overlap filter used by the dashboard:
     * spt.period_start <= dashboard.period_end AND spt.period_end >= dashboard.period_start
     */
    public function scopeOverlapsPeriod($query, $dashboardStart, $dashboardEnd)
    {
        return $query->where('period_start', '<=', $dashboardEnd)
            ->where('period_end', '>=', $dashboardStart);
    }
}
