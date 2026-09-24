<?php

namespace App\Jobs;

use App\Models\ImportFile;
use App\Services\FpmParseException;
use App\Services\PurchaseUploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * One job per uploaded PDF within a batch. Runs in the background so a
 * SuperAdmin uploading dozens of FPM PDFs at once doesn't have to wait for
 * every single one to be parsed synchronously.
 */
class ProcessFpmUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $importFileId,
        private readonly int $customerId,
        private readonly int $uploadedByUserId,
        private readonly string $tmpPath,
        private readonly string $originalFilename,
    ) {
    }

    public function handle(PurchaseUploadService $service): void
    {
        $importFile = ImportFile::findOrFail($this->importFileId);

        try {
            $purchase = $service->handle(
                $this->customerId,
                $this->uploadedByUserId,
                $this->tmpPath,
                $this->originalFilename,
            );

            $importFile->update([
                'status' => 'SUCCESS',
                'purchase_id' => $purchase->id,
            ]);

            $this->incrementBatchCounter($importFile->import_batch_id, success: true);
        } catch (FpmParseException $e) {
            $importFile->update([
                'status' => 'FAILED',
                'failure_reason' => $e->getMessage(),
            ]);

            $this->incrementBatchCounter($importFile->import_batch_id, success: false);
        } catch (\Throwable $e) {
            Log::error('Unexpected error processing FPM upload', ['error' => $e->getMessage()]);

            $importFile->update([
                'status' => 'FAILED',
                'failure_reason' => 'Terjadi kesalahan tak terduga saat memproses file.',
            ]);

            $this->incrementBatchCounter($importFile->import_batch_id, success: false);
        } finally {
            @unlink($this->tmpPath);
        }
    }

    private function incrementBatchCounter(int $batchId, bool $success): void
    {
        DB::table('import_batches')->where('id', $batchId)->increment(
            $success ? 'success_count' : 'failed_count'
        );

        $batch = DB::table('import_batches')->find($batchId);
        if ($batch && ($batch->success_count + $batch->failed_count) >= $batch->total_files) {
            DB::table('import_batches')->where('id', $batchId)->update(['status' => 'COMPLETED']);
        }
    }
}
