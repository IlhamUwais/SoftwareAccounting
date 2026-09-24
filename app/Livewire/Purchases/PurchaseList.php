<?php

namespace App\Livewire\Purchases;

use App\Models\Purchase;
use App\Services\PurchaseService;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PurchaseList extends Component
{
    use WithPagination;

    public bool $showTrashed = false;

    public function restore(int $purchaseId, PurchaseService $service): void
    {
        $purchase = Purchase::onlyTrashed()->findOrFail($purchaseId);
        $this->authorize('restore', $purchase);

        try {
            $service->restore($purchase);
            session()->flash('status', 'Pembelian berhasil di-restore.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', $e->validator->errors()->first());
        }
    }

    public function delete(int $purchaseId): void
    {
        $purchase = Purchase::findOrFail($purchaseId);
        $this->authorize('delete', $purchase);
        $purchase->delete();
        session()->flash('status', 'Pembelian dihapus (soft delete).');
    }

    public function render()
    {
        $customerId = TenantContext::currentCustomerId();

        $query = Purchase::with('supplier')->where('customer_id', $customerId);
        $query = $this->showTrashed ? $query->onlyTrashed() : $query;

        return view('livewire.purchases.purchase-list', [
            'purchases' => $query->orderByDesc('tanggal_faktur')->paginate(15),
        ]);
    }
}
