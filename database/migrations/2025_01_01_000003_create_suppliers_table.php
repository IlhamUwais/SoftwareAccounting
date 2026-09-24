<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->string('npwp');
            $table->text('alamat')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Unique NPWP per customer, but ONLY among active (non soft-deleted)
        // suppliers. PostgreSQL partial unique index.
        DB::statement(
            'CREATE UNIQUE INDEX suppliers_customer_npwp_active_unique
             ON suppliers (customer_id, npwp) WHERE deleted_at IS NULL'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
