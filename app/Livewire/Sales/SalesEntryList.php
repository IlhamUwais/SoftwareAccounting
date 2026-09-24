<?php

namespace App\Livewire\Sales;

use App\Models\SalesEntry;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SalesEntryList extends Component
{
    use WithPagination;

    public int $periode_bulan;
    public int $periode_tahun;
    public string $nominal = '';
    public bool $showForm = false;

    public function mount(): void
    {
        $this->periode_bulan = (int) now()->format('n');
        $this->periode_tahun = (int) now()->format('Y');
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

    public function delete(int $id): void
    {
        $entry = SalesEntry::findOrFail($id);
        $this->authorize('delete', $entry);
        $entry->delete();
    }

    public function render()
    {
        return view('livewire.sales.sales-entry-list', [
            'entries' => SalesEntry::where('customer_id', TenantContext::currentCustomerId())
                ->orderByDesc('periode_tahun')->orderByDesc('periode_bulan')->paginate(15),
        ]);
    }
}
