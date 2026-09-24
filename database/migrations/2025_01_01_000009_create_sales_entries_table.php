<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('periode_bulan'); // 1-12
            $table->unsignedSmallInteger('periode_tahun');
            $table->decimal('nominal', 18, 2);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'periode_tahun', 'periode_bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_entries');
    }
};
