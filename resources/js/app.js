import './bootstrap';
import Alpine from 'alpinejs';

// Optional (kalau nanti butuh datepicker)
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

window.Alpine = Alpine;
window.flatpickr = flatpickr;

Alpine.start();

// Dashboard donut charts
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('#chartPaymentMethods') || document.querySelector('#chartCashFlow')) {
        import('./components/chart/chart-donut').then((module) => module.initDashboardDonutCharts());
    }
});

// Confirmation modal helper
window.showConfirm = function ({ title = 'Konfirmasi', message = 'Apakah Anda yakin ingin melanjutkan aksi ini?', confirmText = 'Ya', cancelText = 'Batal' } = {}) {
    return new Promise((resolve) => {
        const modal = document.getElementById('global-confirm-modal');
        if (!modal) return resolve(true);

        modal.querySelector('.confirm-modal-title').textContent = title;
        modal.querySelector('.confirm-modal-message').textContent = message;
        modal.querySelector('.confirm-yes').textContent = confirmText;
        modal.querySelector('.confirm-no').textContent = cancelText;

        modal.classList.remove('hidden');

        const yes = modal.querySelector('.confirm-yes');
        const no = modal.querySelector('.confirm-no');
        const backdrop = modal.querySelector('[data-modal-backdrop]');

        function cleanup(result) {
            yes.removeEventListener('click', onYes);
            no.removeEventListener('click', onNo);
            backdrop.removeEventListener('click', onNo);
            document.removeEventListener('keydown', onKey);
            modal.classList.add('hidden');
            resolve(result);
        }

        function onYes(e) { e.preventDefault(); cleanup(true); }
        function onNo(e) { e && e.preventDefault(); cleanup(false); }
        function onKey(e) { if (e.key === 'Escape') cleanup(false); }

        yes.addEventListener('click', onYes);
        no.addEventListener('click', onNo);
        backdrop.addEventListener('click', onNo);
        document.addEventListener('keydown', onKey);
    });
};

// Intercept form submissions for update/delete and forms with data-confirm-message
document.addEventListener('submit', function (e) {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;

    const methodInput = form.querySelector('input[name="_method"]');
    const method = methodInput ? methodInput.value.toUpperCase() : (form.method || 'GET').toUpperCase();

    const hasExplicitConfirm = form.dataset.confirmMessage;
    const shouldConfirm = hasExplicitConfirm || ['PUT', 'PATCH', 'DELETE'].includes(method) || form.classList.contains('confirmable');

    if (!shouldConfirm) return; // let it submit

    e.preventDefault();

    const title = form.dataset.confirmTitle || (['PUT', 'PATCH'].includes(method) ? 'Konfirmasi Perubahan' : 'Konfirmasi');
    const message = form.dataset.confirmMessage || (method === 'DELETE' ? 'Anda yakin ingin menghapus data ini?' : 'Anda yakin ingin melanjutkan?');

    window.showConfirm({ title, message }).then((ok) => {
        if (ok) form.submit();
    });
});

// Intercept links/buttons with data-confirm attribute
document.addEventListener('click', function (e) {
    const el = e.target.closest('[data-confirm]');
    if (!el) return;
    e.preventDefault();
    const href = el.getAttribute('href');
    const title = el.dataset.confirmTitle || 'Konfirmasi';
    const message = el.dataset.confirmMessage || 'Anda yakin ingin melanjutkan aksi ini?';
    window.showConfirm({ title, message }).then((ok) => {
        if (!ok) return;
        if (href) window.location.href = href;
        else if (el.dataset.action) {
            try { new Function(el.dataset.action)(); } catch (err) { console.error(err); }
        }
    });
});
