<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseDetail extends Model
{
    protected $fillable = [
        'purchase_id',
        'master_item_id',
        'nama_barang_snapshot',
        'harga_satuan',
        'quantity',
        'satuan',
    ];

    protected function casts(): array
    {
        return [
            'harga_satuan' => 'decimal:2',
            'quantity' => 'decimal:3',
        ];
    }

    // No SoftDeletes / LogsActivity trait here on purpose - edits are
    // update-in-place, full history is recorded on the parent Purchase's
    // activity log entry (before/after JSON), not per detail row.

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function masterItem(): BelongsTo
    {
        return $this->belongsTo(MasterItem::class);
    }
}
