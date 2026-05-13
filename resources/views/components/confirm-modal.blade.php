<div id="global-confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4" aria-hidden="true">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" data-modal-backdrop></div>

    <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl dark:bg-slate-900" role="dialog" aria-modal="true">
        <div class="p-6">
            <h3 class="confirm-modal-title text-lg font-semibold text-slate-900 dark:text-white">Konfirmasi</h3>
            <p class="confirm-modal-message mt-2 text-sm text-slate-600 dark:text-slate-300">Apakah Anda yakin ingin melanjutkan aksi ini?</p>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="confirm-no inline-flex items-center justify-center px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">Batal</button>
                <button type="button" class="confirm-yes inline-flex items-center justify-center px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">Ya</button>
            </div>
        </div>
    </div>
</div>
