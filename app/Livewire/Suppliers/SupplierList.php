<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Support\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SupplierList extends Component
{
    use WithPagination;

    public string $nama = '';
    public string $npwp = '';
    public ?string $alamat = null;
    public bool $showForm = false;
    public bool $showTrashed = false;

    public function updatedShowTrashed(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        $this->authorize('create', Supplier::class);

        $this->validate([
            'nama' => 'required|string|max:255',
            'npwp' => 'required|string|max:32',
            'alamat' => 'nullable|string',
        ]);

        Supplier::create([
            'customer_id' => TenantContext::currentCustomerId(),
            'nama' => $this->nama,
            'npwp' => $this->npwp,
            'alamat' => $this->alamat,
        ]);

        $this->reset(['nama', 'npwp', 'alamat', 'showForm']);
        session()->flash('status', 'Supplier ditambahkan.');
    }

    public function delete(int $id): void
    {
        $supplier = Supplier::findOrFail($id);
        $this->authorize('delete', $supplier);
        $supplier->delete();
        session()->flash('status', 'Supplier berhasil dihapus.');
    }

    public function restore(int $id): void
    {
        $supplier = Supplier::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $supplier);
        $supplier->restore();
        session()->flash('status', 'Supplier berhasil dipulihkan.');
    }

    public function render()
    {
        return view('livewire.suppliers.supplier-list', [
            'suppliers' => Supplier::where('customer_id', TenantContext::currentCustomerId())
                ->when($this->showTrashed, fn ($q) => $q->onlyTrashed())
                ->orderBy('nama')->paginate(15),
        ]);
    }
}
