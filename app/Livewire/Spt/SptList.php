<?php

namespace App\Livewire\Spt;

use App\Livewire\Concerns\RequiresActingCustomer;
use App\Models\SptDocument;
use App\Models\SptPaymentProof;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SptList extends Component
{
    use WithFileUploads, WithPagination, RequiresActingCustomer;

    // Allowlist agreed during discussion - 8 extensions only.
    public const ALLOWED_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];

    public bool $showForm = false;
    public bool $showTrashed = false;
    public string $jenis_spt = 'SPT_MASA_PPN';
    public string $period_start = '';
    public string $period_end = '';
    public $file;

    #[Locked]
    public ?int $editingId = null;

    #[Locked]
    public ?int $addProofToSptId = null;
    public $proofFile;

    public function mount(): void
    {
        $this->ensureCustomerSelected();
    }

    public function updatedShowTrashed(): void
    {
        $this->resetPage();
    }

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

    protected function messages(): array
    {
        return [
            'file.required' => 'File SPT wajib dipilih.',
            'file.mimes' => 'Format file SPT tidak valid. Format yang didukung: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX.',
            'file.max' => 'Ukuran file SPT maksimal 10 MB.',
            'proofFile.required' => 'File bukti bayar wajib dipilih.',
            'proofFile.mimes' => 'Format bukti bayar tidak valid. Format yang didukung: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX.',
            'proofFile.max' => 'Ukuran file bukti bayar maksimal 10 MB.',
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

    public function edit(int $id): void
    {
        $spt = SptDocument::findOrFail($id);
        $this->authorize('update', $spt);

        $this->editingId = $spt->id;
        $this->jenis_spt = $spt->jenis_spt;
        $this->period_start = optional($spt->period_start)->format('Y-m-d') ?? $spt->period_start;
        $this->period_end = optional($spt->period_end)->format('Y-m-d') ?? $spt->period_end;
        $this->showForm = true;
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->reset(['file', 'showForm', 'period_start', 'period_end']);
    }

    public function updateSpt(): void
    {
        $spt = SptDocument::findOrFail($this->editingId);
        $this->authorize('update', $spt);

        $this->validate([
            'jenis_spt' => 'required|in:'.implode(',', array_keys(SptDocument::JENIS_OPTIONS)),
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        $spt->update([
            'jenis_spt' => $this->jenis_spt,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
        ]);

        $this->cancelEdit();
        session()->flash('status', 'SPT berhasil diperbarui.');
    }

    public function showAddProofForm(int $sptId): void
    {
        SptDocument::findOrFail($sptId);
        $this->authorize('create', SptPaymentProof::class);
        $this->addProofToSptId = $sptId;
    }

    public function cancelAddProof(): void
    {
        $this->reset(['proofFile', 'addProofToSptId']);
    }

    public function addProof(): void
    {
        $ext = implode(',', self::ALLOWED_EXTENSIONS);
        $this->validate(['proofFile' => "required|file|mimes:{$ext}|max:10240"]);

        $spt = SptDocument::findOrFail($this->addProofToSptId);
        $this->authorize('create', SptPaymentProof::class);

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
        session()->flash('status', 'Bukti bayar dihapus.');
    }

    public function restoreProof(int $proofId): void
    {
        $proof = SptPaymentProof::onlyTrashed()->findOrFail($proofId);
        $this->authorize('restore', $proof);
        $proof->restore();
        session()->flash('status', 'Bukti bayar dipulihkan.');
    }

    public function delete(int $id): void
    {
        $spt = SptDocument::findOrFail($id);
        $this->authorize('delete', $spt);
        $spt->delete();
        session()->flash('status', 'SPT berhasil dihapus.');
    }

    public function restore(int $id): void
    {
        $spt = SptDocument::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $spt);
        $spt->restore();
        session()->flash('status', 'SPT berhasil dipulihkan.');
    }

    public function render()
    {
        return view('livewire.spt.spt-list', [
            'sptDocuments' => SptDocument::with(['paymentProofs' => fn ($q) => $q->withTrashed()])
                ->where('customer_id', TenantContext::currentCustomerId())
                ->when($this->showTrashed, fn ($q) => $q->onlyTrashed())
                ->orderByDesc('period_start')->paginate(15),
        ]);
    }
}
