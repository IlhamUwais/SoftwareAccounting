<div
    x-data="toastManager()"
    x-init="init()"
    x-on:toast.window="push($event.detail)"
    class="fixed z-[100] flex flex-col gap-2
           inset-x-0 bottom-0 p-3
           sm:inset-x-auto sm:top-4 sm:right-4 sm:bottom-auto sm:p-0 sm:w-80"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-4"
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="rounded-lg shadow-card-md border px-4 py-3 flex items-start gap-3 bg-white"
            :class="{
                'border-emerald-200': toast.type === 'success',
                'border-red-200': toast.type === 'error',
            }"
        >
            <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0"
                 :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-red-500'"></div>
            <p class="text-sm flex-1"
               :class="toast.type === 'success' ? 'text-emerald-800' : 'text-red-800'"
               x-text="toast.message"></p>
            <button type="button" @click="dismiss(toast.id)"
                    class="text-navy-300 hover:text-navy-600 flex-shrink-0 leading-none text-lg">&times;</button>
        </div>
    </template>
</div>

<script>
    function toastManager() {
        return {
            toasts: [],
            nextId: 1,
            init() {
                @if(session('status'))
                    this.push({ type: 'success', message: @js(session('status')) });
                @endif
                @if(session('error'))
                    this.push({ type: 'error', message: @js(session('error')) });
                @endif
            },
            push(detail) {
                const id = this.nextId++;
                this.toasts.push({ id, type: detail.type ?? 'success', message: detail.message });

                // Cap at 3 visible toasts at once.
                if (this.toasts.length > 3) {
                    this.toasts.shift();
                }

                setTimeout(() => this.dismiss(id), 4000);
            },
            dismiss(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            },
        };
    }
</script>
