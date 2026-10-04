<div>
    {{-- ── Page header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Ringkasan keuangan dan aktivitas pajak</p>
        </div>

        {{-- Date range filter --}}
        <div class="flex flex-wrap items-center gap-3 bg-white rounded-card border border-navy-100 px-4 py-3 shadow-card self-start sm:self-auto">
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-navy-500 uppercase tracking-wide whitespace-nowrap">Dari</label>
                <input type="month" wire:model.live="fromMonth"
                       class="form-input py-1 text-sm w-36 !border-0 !ring-0 !shadow-none focus:!ring-2 focus:!ring-gold-400 focus:!border-gold-400">
            </div>
            <div class="w-px h-4 bg-navy-200 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-navy-500 uppercase tracking-wide whitespace-nowrap">Sampai</label>
                <input type="month" wire:model.live="toMonth"
                       class="form-input py-1 text-sm w-36 !border-0 !ring-0 !shadow-none focus:!ring-2 focus:!ring-gold-400 focus:!border-gold-400">
            </div>
        </div>
    </div>

    {{-- ── KPI Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">

        {{-- Total Termin --}}
        <div class="card p-4 col-span-1">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-md bg-navy-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-navy-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 7h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V7z"/>
                    </svg>
                </div>
                <p class="text-xs text-navy-500 font-medium">Total Termin</p>
            </div>
            <p class="text-lg font-bold text-navy-900 tabular leading-tight">Rp {{ number_format($cards['total_termin'], 0, ',', '.') }}</p>
        </div>

        {{-- Total PPN --}}
        <div class="card p-4 col-span-1">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-md bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p class="text-xs text-navy-500 font-medium">Total PPN</p>
            </div>
            <p class="text-lg font-bold text-navy-900 tabular leading-tight">Rp {{ number_format($cards['total_ppn'], 0, ',', '.') }}</p>
        </div>

        {{-- Gabungan --}}
        <div class="card p-4 col-span-2 lg:col-span-1 border-gold-300 bg-gradient-to-br from-gold-50 to-white">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-md bg-gold-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-gold-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xs text-navy-500 font-medium">Termin+PPN−Diskon</p>
            </div>
            <p class="text-lg font-bold text-gold-700 tabular leading-tight">Rp {{ number_format($cards['total_gabungan'], 0, ',', '.') }}</p>
        </div>

        {{-- Total Supplier --}}
        <div class="card p-4 col-span-1">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-md bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-xs text-navy-500 font-medium">Total Supplier</p>
            </div>
            <p class="text-lg font-bold text-navy-900 tabular leading-tight">{{ $cards['total_supplier'] }}</p>
        </div>

        {{-- Omzet --}}
        <div class="card p-4 col-span-1">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-md bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <p class="text-xs text-navy-500 font-medium">Omzet</p>
            </div>
            <p class="text-lg font-bold text-navy-900 tabular leading-tight">Rp {{ number_format($cards['omzet'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- ── Chart ── --}}
    <div class="card mb-6">
        <div class="card-header">
            <h2 class="text-sm font-semibold text-navy-800">Grafik Omzet</h2>
        </div>
        <div class="card-body">
            <div wire:ignore x-data="omzetChart(@js($omzetChart))" x-init="render()">
                <canvas x-ref="canvas" height="80"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Supplier table ── --}}
    <div class="card mb-6 overflow-hidden">
        <div class="card-header">
            <h2 class="text-sm font-semibold text-navy-800">Rincian per Supplier</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Supplier</th>
                        <th class="text-right">Total Termin</th>
                        <th class="text-right">Total PPN</th>
                        <th class="text-right">Termin+PPN−Diskon</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supplierTable as $row)
                        <tr class="cursor-pointer"
                            wire:click="$set('selectedSupplierId', {{ $row->supplier->id }})">
                            <td class="font-medium text-navy-700">{{ $row->supplier->nama }}</td>
                            <td class="text-right tabular text-navy-700">Rp {{ number_format($row->total_termin, 0, ',', '.') }}</td>
                            <td class="text-right tabular text-navy-700">Rp {{ number_format($row->total_ppn, 0, ',', '.') }}</td>
                            <td class="text-right tabular font-semibold text-navy-900">Rp {{ number_format($row->total_gabungan, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-navy-400">Tidak ada data pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Supplier detail panel ── --}}
    @if($selectedSupplierId)
        <div class="card mb-6 border-l-4 border-gold-500">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-navy-800">Detail Pembelian</h2>
                <button wire:click="$set('selectedSupplierId', null)"
                        class="text-xs font-medium text-navy-400 hover:text-navy-700 transition-colors px-2 py-1 rounded hover:bg-navy-100">
                    Tutup
                </button>
            </div>
            <div class="divide-y divide-navy-50">
                @foreach($supplierDetail as $purchase)
                    <div class="px-5 py-3">
                        <p class="text-sm font-semibold text-navy-800">
                            {{ $purchase->nomor_faktur }}
                            <span class="font-normal text-navy-500 ml-2">{{ $purchase->tanggal_faktur->translatedFormat('d M Y') }}</span>
                        </p>
                        <ul class="mt-1.5 space-y-0.5">
                            @foreach($purchase->details as $d)
                                <li class="text-xs text-navy-500 flex items-center gap-1">
                                    <span class="w-1 h-1 rounded-full bg-navy-300 flex-shrink-0"></span>
                                    {{ $d->nama_barang_snapshot }} &mdash; {{ $d->quantity }} {{ $d->satuan }} &times; Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── SPT List ── --}}
    <div class="card">
        <div class="card-header">
            <h2 class="text-sm font-semibold text-navy-800">SPT pada Periode Ini</h2>
        </div>
        <div class="card-body">
            <ul class="space-y-2">
                @forelse($sptList as $spt)
                    <li class="flex items-center gap-3 text-sm">
                        <div class="w-1.5 h-1.5 rounded-full bg-gold-500 flex-shrink-0"></div>
                        <span class="text-navy-800 font-medium">{{ \App\Models\SptDocument::JENIS_OPTIONS[$spt->jenis_spt] }}</span>
                        <span class="text-navy-400 text-xs">&mdash; {{ $spt->period_start->format('M Y') }} s/d {{ $spt->period_end->format('M Y') }}</span>
                    </li>
                @empty
                    <li class="text-navy-400 text-sm">Tidak ada SPT pada periode ini.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <script>
        function omzetChart(data) {
            return {
                render() {
                    new Chart(this.$refs.canvas, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Omzet',
                                data: data.values,
                                borderColor: '#c8a84b',
                                backgroundColor: 'rgba(200,168,75,0.08)',
                                tension: 0.3,
                                fill: true,
                                pointBackgroundColor: '#c8a84b',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw),
                                    }
                                }
                            },
                            scales: {
                                x: { grid: { display: false }, ticks: { color: '#6692cb' } },
                                y: {
                                    grid: { color: 'rgba(179,201,229,0.3)' },
                                    ticks: {
                                        color: '#6692cb',
                                        callback: v => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(v),
                                    }
                                }
                            }
                        },
                    });
                }
            }
        }
    </script>
</div>
