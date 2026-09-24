<?php

namespace App\Services;

use App\Models\MasterItem;
use App\Models\Purchase;
use App\Models\PurchaseDocument;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseUploadService
{
    public function __construct(private readonly FpmPdfParser $parser)
    {
    }

    /**
     * Handles ONE uploaded FPM PDF end to end: parse -> validate duplicate
     * -> auto-create supplier/master item -> store file -> create Purchase
     * + details + document, all inside a single DB transaction so a
     * failure never leaves a half-created transaction behind.
     *
     * @throws FpmParseException
     */
    public function handle(int $customerId, int $uploadedByUserId, string $absoluteTmpPath, string $originalFilename): Purchase
    {
        $parsed = $this->parser->parse($absoluteTmpPath);

        // Uniqueness check: nomor_faktur must be unique among ACTIVE
        // purchases for this customer (soft-deleted ones don't block reuse).
        $duplicate = Purchase::query()
            ->where('customer_id', $customerId)
            ->where('nomor_faktur', $parsed['nomor_faktur'])
            ->exists();

        if ($duplicate) {
            throw new FpmParseException("Nomor faktur {$parsed['nomor_faktur']} sudah ada (duplicate).");
        }

        return DB::transaction(function () use ($customerId, $uploadedByUserId, $absoluteTmpPath, $originalFilename, $parsed) {
            $supplier = Supplier::findOrCreateForCustomer(
                $customerId,
                $parsed['supplier']['nama'],
                $parsed['supplier']['npwp'],
                $parsed['supplier']['alamat'],
            );

            // Store the original PDF (private disk - only SuperAdmin can
            // ever be authorized to view/download it, see FileAccessController).
            $diskPath = "purchase-documents/{$customerId}/".uniqid().'.pdf';
            Storage::disk(config('filesystems.default'))->put($diskPath, file_get_contents($absoluteTmpPath));

            $document = PurchaseDocument::create([
                'file_reference' => $diskPath,
                'original_filename' => $originalFilename,
                'uploaded_by' => $uploadedByUserId,
            ]);

            $purchase = Purchase::create([
                'customer_id' => $customerId,
                'supplier_id' => $supplier->id,
                'supplier_nama_snapshot' => $supplier->nama,
                'supplier_npwp_snapshot' => $supplier->npwp,
                'supplier_alamat_snapshot' => $supplier->alamat,
                'nomor_faktur' => $parsed['nomor_faktur'],
                'tanggal_faktur' => $parsed['tanggal_faktur'],
                'termin' => $parsed['termin'] ?? '0',
                'potongan' => $parsed['potongan'] ?? '0',
                'uang_muka' => $parsed['uang_muka'], // nullable - only shown if present
                'dpp' => $parsed['dpp'] ?? '0',
                'ppn' => $parsed['ppn'] ?? '0',
                'purchase_document_id' => $document->id,
            ]);

            $document->update(['purchase_id' => $purchase->id]);

            foreach ($parsed['items'] as $item) {
                $masterItem = MasterItem::findOrCreateForCustomer($customerId, $item['nama_barang']);

                $purchase->details()->create([
                    'master_item_id' => $masterItem->id,
                    'nama_barang_snapshot' => $item['nama_barang'],
                    'harga_satuan' => $item['harga_satuan'],
                    'quantity' => $item['quantity'],
                    'satuan' => $item['satuan'],
                ]);
            }

            return $purchase;
        });
    }
}
