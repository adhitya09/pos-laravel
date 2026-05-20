@props(['id' => 'modal', 'title' => '', 'description' => '', 'maxWidth' => 'max-w-2xl'])

<div
    x-data="{
        open: false,
        openModal() { this.open = true; },
        closeModal() { this.open = false; }
    }"
    x-cloak
    x-show="open"
    @keydown.escape.window="closeModal()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 transition-all duration-200"
    :class="open ? 'pointer-events-auto' : 'pointer-events-none'"
    aria-modal="true"
    role="dialog"
    aria-hidden="true"
>
    <div
        x-show="open"
        x-transition.opacity.duration.200ms
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200"
        :class="open ? 'pointer-events-auto' : 'pointer-events-none'">
    </div>

    <div class="relative w-full p-2 max-w-full">
        <div
            x-show="open"
            x-transition.duration.200ms.scale.95.opacity
            class="pointer-events-auto mx-auto w-full {{ $maxWidth }} overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-2xl transition-all duration-200 dark:border-slate-700/70 dark:bg-slate-950"
            @click.away="closeModal()"
        >
            <div class="flex items-start justify-between gap-4 border-b border-slate-200/70 px-6 py-4 dark:border-slate-700/70">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $title }}</h2>
                    @if ($description)
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
                    @endif
                </div>
                <button
                    type="button"
                    @click="closeModal()"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                    aria-label="Close modal"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l8 8M14 6l-8 8" />
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5">
                {{ $slot }}
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-200/70 px-6 py-4 dark:border-slate-700/70">
                <button
                    type="button"
                    @click="closeModal()"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
