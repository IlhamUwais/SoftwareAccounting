<div>
    <div class="flex flex-wrap items-end gap-4 mb-6">
        <div>
            <label class="block text-sm text-gray-600">Dari</label>
            <input type="month" wire:model.live="fromMonth" class="rounded border-gray-300">
        </div>
        <div>
            <label class="block text-sm text-gray-600">Sampai</label>
            <input type="month" wire:model.live="toMonth" class="rounded border-gray-300">
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500">Total Termin</p>
            <p class="text-lg font-semibold">Rp {{ number_format($cards['total_termin'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500">Total PPN</p>
            <p class="text-lg font-semibold">Rp {{ number_format($cards['total_ppn'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500">Termin + PPN - Diskon</p>
            <p class="text-lg font-semibold">Rp {{ number_format($cards['total_gabungan'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500">Total Supplier</p>
            <p class="text-lg font-semibold">{{ $cards['total_supplier'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500">Omzet</p>
            <p class="text-lg font-semibold">Rp {{ number_format($cards['omzet'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-8">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Grafik Omzet</h3>
        <div wire:ignore x-data="omzetChart(@js($omzetChart))" x-init="render()">
            <canvas x-ref="canvas" height="80"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-8">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Supplier</h3>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-2">Nama Supplier</th>
                    <th class="py-2">Total Termin</th>
                    <th class="py-2">Total PPN</th>
                    <th class="py-2">Total Termin + PPN - Diskon</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplierTable as $row)
                    <tr class="border-b hover:bg-gray-50 cursor-pointer"
                        wire:click="$set('selectedSupplierId', {{ $row->supplier->id }})">
                        <td class="py-2 text-indigo-700">{{ $row->supplier->nama }}</td>
                        <td class="py-2">Rp {{ number_format($row->total_termin, 0, ',', '.') }}</td>
                        <td class="py-2">Rp {{ number_format($row->total_ppn, 0, ',', '.') }}</td>
                        <td class="py-2">Rp {{ number_format($row->total_gabungan, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-400">Tidak ada data pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($selectedSupplierId)
        <div class="bg-white rounded-lg shadow p-4 mb-8">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-sm font-semibold text-gray-700">Detail Pembelian</h3>
                <button wire:click="$set('selectedSupplierId', null)" class="text-xs text-gray-400 hover:text-gray-600">Tutup</button>
            </div>
            @foreach($supplierDetail as $purchase)
                <div class="border-b py-2">
                    <p class="text-sm font-medium">{{ $purchase->nomor_faktur }} — {{ $purchase->tanggal_faktur->translatedFormat('d M Y') }}</p>
                    <ul class="text-xs text-gray-500 mt-1 space-y-0.5">
                        @foreach($purchase->details as $d)
                            <li>{{ $d->nama_barang_snapshot }} — {{ $d->quantity }} {{ $d->satuan }} x Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">SPT pada Periode Ini</h3>
        <ul class="text-sm space-y-1">
            @forelse($sptList as $spt)
                <li>{{ \App\Models\SptDocument::JENIS_OPTIONS[$spt->jenis_spt] }} — {{ $spt->period_start->format('M Y') }} s/d {{ $spt->period_end->format('M Y') }}</li>
            @empty
                <li class="text-gray-400">Tidak ada SPT pada periode ini.</li>
            @endforelse
        </ul>
    </div>

    <script>
        function omzetChart(data) {
            return {
                render() {
                    new Chart(this.$refs.canvas, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{ label: 'Omzet', data: data.values, borderColor: '#4f46e5', tension: 0.2 }],
                        },
                        options: { responsive: true },
                    });
                }
            }
        }
    </script>
</div>
