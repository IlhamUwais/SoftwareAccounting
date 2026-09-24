<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            // Live reference - can change if SuperAdmin edits the supplier
            // on this transaction.
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();

            // Snapshot of supplier identity AT THE TIME the invoice was
            // created / last edited. Keeps transaction history stable even
            // if the supplier master record changes later.
            $table->string('supplier_nama_snapshot');
            $table->string('supplier_npwp_snapshot');
            $table->text('supplier_alamat_snapshot')->nullable();

            $table->string('nomor_faktur');
            $table->date('tanggal_faktur');

            // Money fields - values are taken as-is from the FPM PDF,
            // never recalculated.
            $table->decimal('termin', 18, 2);
            $table->decimal('potongan', 18, 2)->default(0);
            $table->decimal('uang_muka', 18, 2)->nullable();
            $table->decimal('dpp', 18, 2);
            $table->decimal('ppn', 18, 2);

            // NOTE: no FK constraint here yet - purchase_documents table
            // doesn't exist until the next migration (circular reference:
            // a purchase has one document, a document belongs to a
            // purchase). The FK constraint is added in
            // 2025_01_01_000008_add_purchase_document_foreign_key.
            $table->unsignedBigInteger('purchase_document_id')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Nomor faktur unique per customer, only among active purchases.
        // Soft-deleted rows free up the nomor_faktur for reuse; restoring
        // a soft-deleted row is blocked in code if it would collide with
        // an active row (see PurchaseService).
        DB::statement(
            'CREATE UNIQUE INDEX purchases_customer_nomor_faktur_active_unique
             ON purchases (customer_id, nomor_faktur) WHERE deleted_at IS NULL'
        );

        Schema::table('purchases', function (Blueprint $table) {
            $table->index(['customer_id', 'tanggal_faktur']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
