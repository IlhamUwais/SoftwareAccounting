<div>
    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title">Audit Log</h1>
            <p class="page-subtitle">Riwayat perubahan data oleh pengguna dan sistem</p>
        </div>
        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-3">
            <div>
                <label class="form-label sr-only">Filter Event</label>
                <select wire:model.live="filterEvent" class="form-select text-sm py-2">
                    <option value="">Semua Event</option>
                    <option value="created">Created</option>
                    <option value="updated">Updated</option>
                    <option value="deleted">Deleted</option>
                    <option value="restored">Restored</option>
                </select>
            </div>
            <div>
                <label class="form-label sr-only">Filter Modul</label>
                <select wire:model.live="filterSubject" class="form-select text-sm py-2">
                    <option value="">Semua Modul</option>
                    <option value="Customer">Customer</option>
                    <option value="Purchase">Purchase</option>
                    <option value="Supplier">Supplier</option>
                    <option value="MasterItem">Master Item</option>
                    <option value="SalesEntry">Sales Entry</option>
                    <option value="SptDocument">SPT Document</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ── Table ── --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">Waktu</th>
                        <th>Pelaku</th>
                        <th>Event</th>
                        <th>Modul</th>
                        <th>Detail Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $act)
                        <tr class="align-top">
                            <td class="whitespace-nowrap text-xs text-navy-500">
                                {{ $act->created_at->format('d/m/Y') }}<br>
                                <span class="font-mono text-navy-400">{{ $act->created_at->format('H:i:s') }}</span>
                            </td>
                            <td class="whitespace-nowrap">
                                <span class="font-semibold text-navy-800 text-sm">{{ $act->causer?->username ?? 'System' }}</span>
                                @if($act->causer?->role)
                                    <span class="block text-xs text-navy-400">{{ $act->causer->role }}</span>
                                @endif
                            </td>
                            <td>
                                <span @class([
                                    'badge',
                                    'badge-green'  => $act->event === 'created',
                                    'badge-blue'   => $act->event === 'updated',
                                    'badge-red'    => $act->event === 'deleted',
                                    'badge-yellow' => !in_array($act->event, ['created', 'updated', 'deleted']),
                                ])>{{ $act->event ?? 'Log' }}</span>
                            </td>
                            <td class="whitespace-nowrap">
                                <span class="text-sm text-navy-700">{{ class_basename($act->subject_type ?? '') }}</span>
                                @if($act->subject_id)
                                    <span class="text-xs text-navy-400 block">#{{ $act->subject_id }}</span>
                                @endif
                            </td>
                            <td class="text-xs max-w-xs">
                                @php
                                    $props = $act->properties ?? [];
                                    $attributes = $props['attributes'] ?? [];
                                    $old = $props['old'] ?? [];
                                @endphp
                                @if(!empty($attributes) || !empty($old))
                                    <div class="space-y-1">
                                        @foreach($attributes as $key => $val)
                                            <div class="flex flex-wrap items-baseline gap-1">
                                                <span class="font-semibold text-navy-600">{{ $key }}:</span>
                                                @if(isset($old[$key]))
                                                    <span class="line-through text-red-500">{{ is_array($old[$key]) ? json_encode($old[$key]) : $old[$key] }}</span>
                                                    <span class="text-navy-400">&rarr;</span>
                                                @endif
                                                <span class="text-emerald-600 font-medium">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-navy-400">{{ $act->description }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-navy-400">
                                <svg class="w-10 h-10 mx-auto mb-2 text-navy-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Belum ada catatan aktivitas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $activities->links() }}</div>
</div>
