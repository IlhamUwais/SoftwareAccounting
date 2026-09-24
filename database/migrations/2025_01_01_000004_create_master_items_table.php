<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('nama_barang');
            $table->timestamps();
            $table->softDeletes();
        });

        // Item identity = name only, unique per customer among active rows.
        DB::statement(
            'CREATE UNIQUE INDEX master_items_customer_nama_active_unique
             ON master_items (customer_id, nama_barang) WHERE deleted_at IS NULL'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('master_items');
    }
};
