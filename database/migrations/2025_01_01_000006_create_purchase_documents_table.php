<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_documents', function (Blueprint $table) {
            $table->id();
            // Nullable + set null on delete because a Purchase can exist
            // without an attached original PDF (e.g. supplier was changed
            // and the old PDF got detached).
            $table->foreignId('purchase_id')->nullable()
                ->constrained('purchases')->nullOnDelete();
            $table->string('file_reference'); // storage disk path/key
            $table->string('original_filename');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_documents');
    }
};
