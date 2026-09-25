<?php

namespace App\Livewire\MasterItems;

use App\Models\MasterItem;
use App\Support\TenantContext;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MasterItemList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showTrashed = false;

    public function updatedShowTrashed(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $item = MasterItem::findOrFail($id);
        $this->authorize('delete', $item);
        $item->delete();
        session()->flash('status', 'Barang berhasil dihapus.');
    }

    public function restore(int $id): void
    {
        $item = MasterItem::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $item);
        $item->restore();
        session()->flash('status', 'Barang berhasil dipulihkan.');
    }

    public function render()
    {
        $items = MasterItem::where('customer_id', TenantContext::currentCustomerId())
            ->when($this->showTrashed, fn ($q) => $q->onlyTrashed())
            ->when($this->search, fn ($q) => $q->where('nama_barang', 'ilike', "%{$this->search}%"))
            ->withCount('purchaseDetails')
            ->orderBy('nama_barang')
            ->paginate(15);

        return view('livewire.master-items.master-item-list', ['items' => $items]);
    }
}
