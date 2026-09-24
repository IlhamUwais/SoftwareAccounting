<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spt_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            // Fixed enum - do not add new values without discussion.
            $table->enum('jenis_spt', [
                'SPT_TAHUNAN',
                'SPT_MASA_UNIFIKASI',
                'SPT_MASA_PPH_21',
                'SPT_MASA_PPN',
                'SPT_MASA_PPH_PASAL_4',
            ]);

            $table->date('period_start');
            $table->date('period_end');

            $table->string('file_reference');
            $table->string('original_filename');

            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Duplicate jenis+periode is intentionally ALLOWED (e.g. revisi
            // SPT) - no unique constraint here on purpose.
            $table->index(['customer_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spt_documents');
    }
};
