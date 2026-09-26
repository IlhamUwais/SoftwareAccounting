<?php

namespace App\Livewire\Purchases;

use App\Jobs\ProcessFpmUpload;
use App\Models\ImportBatch;
use App\Models\ImportFile;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class UploadFpm extends Component
{
    use WithFileUploads, WithPagination;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile[] */
    public array $files = [];

    public ?int $currentBatchId = null;

    protected function rules(): array
    {
        return [
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:pdf|max:10240', // 10MB per file
        ];
    }

    public function prosessupload(): void
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);
        
        $this->validate();
        

        $customerId = TenantContext::currentCustomerId();
        abort_unless($customerId, 400, 'Pilih customer terlebih dahulu.');

        $batch = ImportBatch::create([
            'customer_id' => $customerId,
            'uploaded_by' => Auth::id(),
            'total_files' => count($this->files),
        ]);

        foreach ($this->files as $file) {
            $importFile = ImportFile::create([
                'import_batch_id' => $batch->id,
                'filename' => $file->getClientOriginalName(),
                'status' => 'PENDING',
            ]);

            // Move out of Livewire's temp upload area before dispatching,
            // since that temp file can be cleaned up before the queued
            // job runs.
            $persistedTmpPath = $file->store('fpm-uploads-tmp', 'local');
            $absolutePath = storage_path('app/private/'.$persistedTmpPath);

            ProcessFpmUpload::dispatch(
                $importFile->id,
                $customerId,
                Auth::id(),
                $absolutePath,
                $file->getClientOriginalName(),
            );
        }

        $this->currentBatchId = $batch->id;
        $this->files = [];
    }

    public function selectBatch(int $batchId): void
    {
        $this->currentBatchId = $batchId;
    }

    public function closeBatchDetail(): void
    {
        $this->currentBatchId = null;
    }

    public function getBatchProperty()
    {
        return $this->currentBatchId ? ImportBatch::with('files')->find($this->currentBatchId) : null;
    }

    public function render()
    {
        $customerId = TenantContext::currentCustomerId();
        $recentBatches = $customerId
            ? ImportBatch::where('customer_id', $customerId)->latest()->paginate(10)
            : null;

        return view('livewire.purchases.upload-fpm', [
            'batch' => $this->batch,
            'recentBatches' => $recentBatches,
        ]);
    }
}
