<?php

namespace App\Livewire\Spt;

use App\Models\SptDocument;
use App\Models\SptPaymentProof;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SptList extends Component
{
    use WithFileUploads, WithPagination;

    // Allowlist agreed during discussion - 8 extensions only.
    public const ALLOWED_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];

    public bool $showForm = false;
    public string $jenis_spt = 'SPT_MASA_PPN';
    public string $period_start = '';
    public string $period_end = '';
    public $file;

    public ?int $addProofToSptId = null;
    public $proofFile;

    protected function rules(): array
    {
        $ext = implode(',', self::ALLOWED_EXTENSIONS);

        return [
            'jenis_spt' => 'required|in:'.implode(',', array_keys(SptDocument::JENIS_OPTIONS)),
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'file' => "required|file|mimes:{$ext}|max:10240",
        ];
    }

    public function save(): void
    {
        $this->authorize('create', SptDocument::class);
        $this->validate();

        $customerId = TenantContext::currentCustomerId();
        $path = $this->file->store("spt/{$customerId}", config('filesystems.default'));

        SptDocument::create([
            'customer_id' => $customerId,
            'jenis_spt' => $this->jenis_spt,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'file_reference' => $path,
            'original_filename' => $this->file->getClientOriginalName(),
            'uploaded_by' => Auth::id(),
        ]);

        $this->reset(['file', 'showForm', 'period_start', 'period_end']);
        session()->flash('status', 'SPT berhasil diunggah.');
    }

    public function addProof(): void
    {
        $ext = implode(',', self::ALLOWED_EXTENSIONS);
        $this->validate(['proofFile' => "required|file|mimes:{$ext}|max:10240"]);

        $spt = SptDocument::findOrFail($this->addProofToSptId);
        $this->authorize('create', \App\Models\SptPaymentProof::class);

        $path = $this->proofFile->store("spt-proofs/{$spt->customer_id}", config('filesystems.default'));

        SptPaymentProof::create([
            'spt_document_id' => $spt->id,
            'file_reference' => $path,
            'original_filename' => $this->proofFile->getClientOriginalName(),
            'uploaded_by' => Auth::id(),
        ]);

        $this->reset(['proofFile', 'addProofToSptId']);
        session()->flash('status', 'Bukti bayar ditambahkan.');
    }

    public function deleteProof(int $proofId): void
    {
        $proof = SptPaymentProof::findOrFail($proofId);
        $this->authorize('delete', $proof);
        $proof->delete();
    }

    public function delete(int $id): void
    {
        $spt = SptDocument::findOrFail($id);
        $this->authorize('delete', $spt);
        $spt->delete();
    }

    public function render()
    {
        return view('livewire.spt.spt-list', [
            'sptDocuments' => SptDocument::with('paymentProofs')
                ->where('customer_id', TenantContext::currentCustomerId())
                ->orderByDesc('period_start')->paginate(15),
        ]);
    }
}
