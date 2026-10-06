@props(['title' => '', 'closeWireClick' => "\$toggle('showForm')"])

<div
    x-data="{ open: true }"
    x-init="document.body.style.overflow = 'hidden'; $root.addEventListener('alpine:destroy', () => { document.body.style.overflow = '' })"
    wire:keydown.escape.window="{{ $closeWireClick }}"
    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-navy-950/60"
        wire:click="{{ $closeWireClick }}"
    ></div>

    {{-- Panel --}}
    <div class="relative w-full sm:max-w-lg bg-white rounded-t-2xl sm:rounded-card shadow-card-md max-h-[90vh] flex flex-col">
        <div class="card-header flex-shrink-0">
            <h2 class="text-sm font-semibold text-navy-800">{{ $title }}</h2>
            <button
                type="button"
                wire:click="{{ $closeWireClick }}"
                class="w-7 h-7 flex items-center justify-center rounded-md text-navy-400 hover:text-navy-700 hover:bg-navy-100 transition-colors"
                aria-label="Tutup"
            >
                &times;
            </button>
        </div>
        <div class="card-body overflow-y-auto">
            {{ $slot }}
        </div>
    </div>
</div>
