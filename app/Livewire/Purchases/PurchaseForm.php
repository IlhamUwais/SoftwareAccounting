<?php

namespace App\Livewire\Purchases;

use App\Models\MasterItem;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PurchaseForm extends Component
{
    public Purchase $purchase;

    public int $supplier_id;
    public string $nomor_faktur;
    public string $tanggal_faktur;
    public string $termin;
    public string $potongan;
    public ?string $uang_muka = null;
    public string $dpp;
    public string $ppn;

    /** @var array<int, array{id:?int, master_item_id:?int, nama_barang_snapshot:string, harga_satuan:string, quantity:string, satuan:?string}> */
    public array $details = [];

    public function mount(Purchase $purchase): void
    {
        $this->authorize('update', $purchase);

        $this->purchase = $purchase->load('details');
        $this->supplier_id = $purchase->supplier_id;
        $this->nomor_faktur = $purchase->nomor_faktur;
        $this->tanggal_faktur = $purchase->tanggal_faktur->format('Y-m-d');
        $this->termin = (string) $purchase->termin;
        $this->potongan = (string) $purchase->potongan;
        $this->uang_muka = $purchase->uang_muka !== null ? (string) $purchase->uang_muka : null;
        $this->dpp = (string) $purchase->dpp;
        $this->ppn = (string) $purchase->ppn;

        $this->details = $purchase->details->map(fn ($d) => [
            'id' => $d->id,
            'master_item_id' => $d->master_item_id,
            'nama_barang_snapshot' => $d->nama_barang_snapshot,
            'harga_satuan' => (string) $d->harga_satuan,
            'quantity' => (string) $d->quantity,
            'satuan' => $d->satuan,
        ])->toArray();
    }

    public function addDetailRow(): void
    {
        $this->details[] = ['id' => null, 'master_item_id' => null, 'nama_barang_snapshot' => '', 'harga_satuan' => '0', 'quantity' => '1', 'satuan' => null];
    }

    public function removeDetailRow(int $index): void
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details);
    }

    public function save(PurchaseService $service): void
    {
        $this->authorize('update', $this->purchase);

        // Must be the purchase's OWN customer, not whichever customer a
        // SuperAdmin currently has "acting" in session - otherwise editing
        // customer B's purchase while acting as customer A would silently
        // create master items and detail rows under the wrong tenant.
        $customerId = $this->purchase->customer_id;

        $this->validate([
            'supplier_id' => [
                'required',
                Rule::exists('suppliers', 'id')
                    ->where('customer_id', $customerId)
                    ->whereNull('deleted_at'),
            ],
            'nomor_faktur' => [
                'required',
                'string',
                Rule::unique('purchases', 'nomor_faktur')
                    ->where('customer_id', $customerId)
                    ->whereNull('deleted_at')
                    ->ignore($this->purchase->id),
            ],
            'tanggal_faktur' => 'required|date',
            'termin' => 'required|numeric|min:0',
            'potongan' => 'required|numeric|min:0',
            'uang_muka' => 'nullable|numeric|min:0',
            'dpp' => 'required|numeric|min:0',
            'ppn' => 'required|numeric|min:0',
            'details.*.id' => [
                'nullable',
                Rule::exists('purchase_details', 'id')->where('purchase_id', $this->purchase->id),
            ],
            'details.*.nama_barang_snapshot' => 'required|string',
            'details.*.harga_satuan' => 'required|numeric|min:0',
            'details.*.quantity' => 'required|numeric|min:0.001',
        ]);

        DB::transaction(function () use ($service, $customerId) {
            $service->update($this->purchase, [
                'supplier_id' => $this->supplier_id,
                'nomor_faktur' => $this->nomor_faktur,
                'tanggal_faktur' => $this->tanggal_faktur,
                'termin' => $this->termin,
                'potongan' => $this->potongan,
                'uang_muka' => $this->uang_muka,
                'dpp' => $this->dpp,
                'ppn' => $this->ppn,
            ]);

            // Update-in-place per the agreed decision: sync detail rows.
            foreach ($this->details as $row) {
                $masterItem = MasterItem::findOrCreateForCustomer($customerId, $row['nama_barang_snapshot']);

                $this->purchase->details()->updateOrCreate(
                    ['id' => $row['id']],
                    [
                        'master_item_id' => $masterItem->id,
                        'nama_barang_snapshot' => $row['nama_barang_snapshot'],
                        'harga_satuan' => $row['harga_satuan'],
                        'quantity' => $row['quantity'],
                        'satuan' => $row['satuan'],
                    ]
                );
            }

            // Remove rows deleted in the UI.
            $keepIds = collect($this->details)->pluck('id')->filter()->all();
            $this->purchase->details()->whereNotIn('id', $keepIds)->delete();
        });

        session()->flash('status', 'Pembelian berhasil disimpan.');
        $this->redirect(route('purchases.index'), navigate: true);
    }

    public function render()
    {
        $customerId = $this->purchase->customer_id;

        return view('livewire.purchases.purchase-form', [
            'suppliers' => Supplier::where('customer_id', $customerId)->orderBy('nama')->get(),
        ]);
    }
}
