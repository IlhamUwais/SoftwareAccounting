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

#[Layout('layouts.app')]
class UploadFpm extends Component
{
    use WithFileUploads;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile[] */
    public array $files = [];

    public ?int $currentBatchId = null;

    protected function rules(): array
    {
        return [
            'files.*' => 'required|file|mimes:pdf|max:10240', // 10MB per file
        ];
    }

    public function upload(): void
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

    public function getBatchProperty()
    {
        return $this->currentBatchId ? ImportBatch::with('files')->find($this->currentBatchId) : null;
    }

    public function render()
    {
        return view('livewire.purchases.upload-fpm', ['batch' => $this->batch]);
    }
}
