<?php

namespace App\Livewire\Sales;

use App\Livewire\Concerns\RequiresActingCustomer;
use App\Models\SalesEntry;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SalesEntryList extends Component
{
    use WithPagination, RequiresActingCustomer;

    public int $periode_bulan;
    public int $periode_tahun;
    public string $nominal = '';
    public bool $showForm = false;
    public bool $showTrashed = false;

    #[Locked]
    public ?int $editingId = null;

    public function mount(): void
    {
        $this->ensureCustomerSelected();
        $this->periode_bulan = (int) now()->format('n');
        $this->periode_tahun = (int) now()->format('Y');
    }

    public function updatedShowTrashed(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        $this->authorize('create', SalesEntry::class);

        $this->validate([
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer|min:2000|max:2100',
            'nominal' => 'required|numeric|min:0',
        ]);

        // Each submission is its own record - multiple entries per month
        // are allowed on purpose, dashboard sums them all.
        SalesEntry::create([
            'customer_id' => TenantContext::currentCustomerId(),
            'periode_bulan' => $this->periode_bulan,
            'periode_tahun' => $this->periode_tahun,
            'nominal' => $this->nominal,
            'created_by' => Auth::id(),
        ]);

        $this->reset(['nominal', 'showForm']);
        session()->flash('status', 'Omzet berhasil dicatat.');
    }

    public function edit(int $id): void
    {
        $entry = SalesEntry::findOrFail($id);
        $this->authorize('update', $entry);

        $this->editingId = $entry->id;
        $this->periode_bulan = $entry->periode_bulan;
        $this->periode_tahun = $entry->periode_tahun;
        $this->nominal = (string) $entry->nominal;
        $this->showForm = true;
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->reset(['nominal', 'showForm']);
        $this->periode_bulan = (int) now()->format('n');
        $this->periode_tahun = (int) now()->format('Y');
    }

    public function updateEntry(): void
    {
        $entry = SalesEntry::findOrFail($this->editingId);
        $this->authorize('update', $entry);

        $this->validate([
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer|min:2000|max:2100',
            'nominal' => 'required|numeric|min:0',
        ]);

        $entry->update([
            'periode_bulan' => $this->periode_bulan,
            'periode_tahun' => $this->periode_tahun,
            'nominal' => $this->nominal,
        ]);

        $this->cancelEdit();
        session()->flash('status', 'Omzet berhasil diperbarui.');
    }

    public function delete(int $id): void
    {
        $entry = SalesEntry::findOrFail($id);
        $this->authorize('delete', $entry);
        $entry->delete();
        session()->flash('status', 'Omzet berhasil dihapus.');
    }

    public function restore(int $id): void
    {
        $entry = SalesEntry::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $entry);
        $entry->restore();
        session()->flash('status', 'Omzet berhasil dipulihkan.');
    }

    public function render()
    {
        return view('livewire.sales.sales-entry-list', [
            'entries' => SalesEntry::where('customer_id', TenantContext::currentCustomerId())
                ->when($this->showTrashed, fn ($q) => $q->onlyTrashed())
                ->orderByDesc('periode_tahun')->orderByDesc('periode_bulan')->paginate(15),
        ]);
    }
}
