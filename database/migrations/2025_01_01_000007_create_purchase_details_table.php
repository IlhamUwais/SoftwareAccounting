<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('master_item_id')->constrained()->restrictOnDelete();

            // Snapshot of item name at transaction time (master item name
            // could theoretically be renamed later - transaction history
            // must not depend on that).
            $table->string('nama_barang_snapshot');
            $table->decimal('harga_satuan', 18, 2);
            $table->decimal('quantity', 18, 3);
            $table->string('satuan')->nullable();

            $table->timestamps();
            // No soft delete here: edits use update-in-place per the
            // agreed decision - full history lives in the audit log (JSON).
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
    }
};
