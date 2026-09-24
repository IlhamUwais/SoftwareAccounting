<?php

namespace App\Http\Controllers;

use App\Models\PurchaseDocument;
use App\Models\SptDocument;
use App\Models\SptPaymentProof;
use Illuminate\Support\Facades\Storage;

/**
 * All file downloads go through here so authorization is always enforced
 * server-side - files are never served via a public/guessable URL.
 */
class FileAccessController extends Controller
{
    public function purchaseDocument(PurchaseDocument $document)
    {
        // Customers must NEVER see the original FPM PDF - SuperAdmin only.
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        return Storage::disk(config('filesystems.default'))
            ->download($document->file_reference, $document->original_filename);
    }

    public function spt(SptDocument $spt)
    {
        $this->authorize('view', $spt);

        return Storage::disk(config('filesystems.default'))
            ->download($spt->file_reference, $spt->original_filename);
    }

    public function sptProof(SptPaymentProof $proof)
    {
        $this->authorize('view', $proof);

        return Storage::disk(config('filesystems.default'))
            ->download($proof->file_reference, $proof->original_filename);
    }
}
