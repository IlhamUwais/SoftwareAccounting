<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spt_payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spt_document_id')->constrained('spt_documents')->cascadeOnDelete();
            $table->string('file_reference');
            $table->string('original_filename');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
            // Has its OWN deleted_at - can be removed independently of the
            // parent SPT, per the agreed decision.
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spt_payment_proofs');
    }
};
