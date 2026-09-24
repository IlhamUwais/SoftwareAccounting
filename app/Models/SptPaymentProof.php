<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SptPaymentProof extends Model
{
    use SoftDeletes;

    // Independent soft-delete lifecycle from its parent SPT, per the
    // agreed decision - can be removed on its own.
    protected $fillable = ['spt_document_id', 'file_reference', 'original_filename', 'uploaded_by'];

    public function sptDocument(): BelongsTo
    {
        return $this->belongsTo(SptDocument::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
