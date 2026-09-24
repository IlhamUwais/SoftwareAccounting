<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    /**
     * Updates a Purchase's editable fields. If the supplier is changed:
     *  - the supplier snapshot fields are refreshed to the NEW supplier
     *  - the old attached PDF document is DETACHED and soft-deleted,
     *    because a PDF for supplier A can never be left attached to a
     *    transaction that now points to supplier B.
     * All of this is captured by the model's own activity log (before/after).
     */
    public function update(Purchase $purchase, array $data): Purchase
    {
        return DB::transaction(function () use ($purchase, $data) {
            $supplierChanged = isset($data['supplier_id']) && $data['supplier_id'] !== $purchase->supplier_id;

            if ($supplierChanged) {
                $newSupplier = Supplier::where('customer_id', $purchase->customer_id)
                    ->findOrFail($data['supplier_id']);

                $data['supplier_nama_snapshot'] = $newSupplier->nama;
                $data['supplier_npwp_snapshot'] = $newSupplier->npwp;
                $data['supplier_alamat_snapshot'] = $newSupplier->alamat;

                if ($purchase->document) {
                    $purchase->document->delete(); // soft delete
                }
                $data['purchase_document_id'] = null;
            }

            $purchase->update($data);

            return $purchase->fresh();
        });
    }

    /**
     * Restore is blocked ("posting ulang ditolak") if an active purchase
     * already exists with the same nomor_faktur for this customer.
     *
     * @throws ValidationException
     */
    public function restore(Purchase $purchase): Purchase
    {
        $conflict = Purchase::query()
            ->where('customer_id', $purchase->customer_id)
            ->where('nomor_faktur', $purchase->nomor_faktur)
            ->where('id', '!=', $purchase->id)
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'nomor_faktur' => 'Restore tidak dapat dilakukan: sudah ada transaksi aktif dengan nomor faktur yang sama.',
            ]);
        }

        $purchase->restore();

        return $purchase;
    }
}
