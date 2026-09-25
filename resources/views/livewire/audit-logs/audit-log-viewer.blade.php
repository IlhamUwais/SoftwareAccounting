<div>
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-lg font-semibold">Audit Log</h2>
            <p class="text-xs text-gray-500">Riwayat perubahan data oleh pengguna dan sistem</p>
        </div>
        <div class="flex gap-2">
            <select wire:model.live="filterEvent" class="text-sm rounded border-gray-300 py-1.5">
                <option value="">Semua Event</option>
                <option value="created">Created</option>
                <option value="updated">Updated</option>
                <option value="deleted">Deleted</option>
                <option value="restored">Restored</option>
            </select>
            <select wire:model.live="filterSubject" class="text-sm rounded border-gray-300 py-1.5">
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

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b bg-gray-50">
                    <th class="py-2.5 px-3">Waktu</th>
                    <th class="py-2.5 px-3">Pelaku</th>
                    <th class="py-2.5 px-3">Event</th>
                    <th class="py-2.5 px-3">Modul</th>
                    <th class="py-2.5 px-3">Detail Perubahan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $act)
                    <tr class="border-b hover:bg-gray-50 align-top">
                        <td class="py-2.5 px-3 whitespace-nowrap text-xs text-gray-600">
                            {{ $act->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="py-2.5 px-3 whitespace-nowrap">
                            <span class="font-medium text-gray-800">{{ $act->causer?->username ?? 'System' }}</span>
                            @if($act->causer?->role)
                                <span class="text-xs text-gray-400 block">{{ $act->causer->role }}</span>
                            @endif
                        </td>
                        <td class="py-2.5 px-3 whitespace-nowrap">
                            <span @class([
                                'text-xs font-semibold px-2 py-0.5 rounded uppercase',
                                'bg-green-100 text-green-700' => $act->event === 'created',
                                'bg-blue-100 text-blue-700' => $act->event === 'updated',
                                'bg-red-100 text-red-700' => $act->event === 'deleted',
                                'bg-yellow-100 text-yellow-700' => !in_array($act->event, ['created', 'updated', 'deleted']),
                            ])>{{ $act->event ?? 'Log' }}</span>
                        </td>
                        <td class="py-2.5 px-3 whitespace-nowrap text-gray-700">
                            {{ class_basename($act->subject_type ?? '') }}
                            @if($act->subject_id)
                                <span class="text-xs text-gray-400">#{{ $act->subject_id }}</span>
                            @endif
                        </td>
                        <td class="py-2.5 px-3 text-xs">
                            @php
                                $props = $act->properties ?? [];
                                $attributes = $props['attributes'] ?? [];
                                $old = $props['old'] ?? [];
                            @endphp
                            @if(!empty($attributes) || !empty($old))
                                <div class="space-y-1">
                                    @foreach($attributes as $key => $val)
                                        <div>
                                            <span class="font-semibold text-gray-600">{{ $key }}:</span>
                                            @if(isset($old[$key]))
                                                <span class="line-through text-red-600">{{ is_array($old[$key]) ? json_encode($old[$key]) : $old[$key] }}</span>
                                                <span class="text-gray-400">&rarr;</span>
                                            @endif
                                            <span class="text-green-700 font-medium">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400">{{ $act->description }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400">Belum ada catatan aktivitas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $activities->links() }}</div>
</div>
