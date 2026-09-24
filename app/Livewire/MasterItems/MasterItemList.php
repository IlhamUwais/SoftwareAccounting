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

    public function render()
    {
        $items = MasterItem::where('customer_id', TenantContext::currentCustomerId())
            ->when($this->search, fn ($q) => $q->where('nama_barang', 'ilike', "%{$this->search}%"))
            ->withCount('purchaseDetails')
            ->orderBy('nama_barang')
            ->paginate(15);

        return view('livewire.master-items.master-item-list', ['items' => $items]);
    }
}
