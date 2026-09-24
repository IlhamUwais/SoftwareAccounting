<?php

namespace App\Livewire\Dashboard;

use App\Models\Purchase;
use App\Models\SalesEntry;
use App\Models\SptDocument;
use App\Support\TenantContext;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public string $fromMonth;
    public string $toMonth;
    public ?int $selectedSupplierId = null;

    public function mount(): void
    {
        // Default range: current month only.
        $this->fromMonth = now()->format('Y-m');
        $this->toMonth = now()->format('Y-m');
    }

    private function customerId(): ?int
    {
        return TenantContext::currentCustomerId();
    }

    private function periodBounds(): array
    {
        $start = Carbon::createFromFormat('Y-m', $this->fromMonth)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $this->toMonth)->endOfMonth();

        return [$start, $end];
    }

    private function purchasesInRange()
    {
        [$start, $end] = $this->periodBounds();

        return Purchase::query()
            ->where('customer_id', $this->customerId())
            ->whereBetween('tanggal_faktur', [$start, $end]);
    }

    public function getCardsProperty(): array
    {
        $purchases = $this->purchasesInRange()->get(['termin', 'potongan', 'ppn', 'supplier_id']);

        $totalTermin = $purchases->sum('termin');
        $totalPpn = $purchases->sum('ppn');
        $totalPotongan = $purchases->sum('potongan');
        $totalSupplier = $purchases->pluck('supplier_id')->unique()->count();

        [$start, $end] = $this->periodBounds();
        $omzet = SalesEntry::where('customer_id', $this->customerId())
            ->get()
            ->filter(function ($entry) use ($start, $end) {
                $entryDate = Carbon::create($entry->periode_tahun, $entry->periode_bulan, 1);
                return $entryDate->between($start->copy()->startOfMonth(), $end->copy()->endOfMonth());
            })
            ->sum('nominal');

        return [
            'total_termin' => $totalTermin,
            'total_ppn' => $totalPpn,
            // Card formula: Termin + PPN - Diskon
            'total_gabungan' => bcadd(bcsub((string) $totalTermin, (string) $totalPotongan, 2), (string) $totalPpn, 2),
            'total_supplier' => $totalSupplier,
            'omzet' => $omzet,
        ];
    }

    public function getSupplierTableProperty()
    {
        return $this->purchasesInRange()
            ->with('supplier')
            ->get()
            ->groupBy('supplier_id')
            ->map(function ($rows) {
                $termin = $rows->sum('termin');
                $ppn = $rows->sum('ppn');
                $potongan = $rows->sum('potongan');

                return (object) [
                    'supplier' => $rows->first()->supplier,
                    'total_termin' => $termin,
                    'total_ppn' => $ppn,
                    // Consistent with the main card: diskon is subtracted here too.
                    'total_gabungan' => bcadd(bcsub((string) $termin, (string) $potongan, 2), (string) $ppn, 2),
                ];
            })
            ->values();
    }

    public function getSupplierDetailProperty()
    {
        if (! $this->selectedSupplierId) {
            return collect();
        }

        return $this->purchasesInRange()
            ->where('supplier_id', $this->selectedSupplierId)
            ->with('details')
            ->get();
    }

    public function getOmzetChartProperty(): array
    {
        [$start, $end] = $this->periodBounds();
        $entries = SalesEntry::where('customer_id', $this->customerId())->get();

        $labels = [];
        $values = [];
        $cursor = $start->copy()->startOfMonth();

        while ($cursor <= $end) {
            $labels[] = $cursor->translatedFormat('M Y');
            $values[] = (float) $entries
                ->where('periode_tahun', $cursor->year)
                ->where('periode_bulan', $cursor->month)
                ->sum('nominal');
            $cursor->addMonth();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function getSptListProperty()
    {
        [$start, $end] = $this->periodBounds();

        return SptDocument::where('customer_id', $this->customerId())
            ->overlapsPeriod($start, $end)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.index', [
            'cards' => $this->cards,
            'supplierTable' => $this->supplierTable,
            'supplierDetail' => $this->supplierDetail,
            'omzetChart' => $this->omzetChart,
            'sptList' => $this->sptList,
        ]);
    }
}
