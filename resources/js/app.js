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

// Product modal helpers (page-local functions used by product index)
window.openProductCreateModal = function () {
    const modal = document.getElementById('product-modal');
    if (!modal) return;
    // show
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    // show create form, hide edit form
    const createForm = document.getElementById('product-modal-form-create');
    const editForm = document.getElementById('product-modal-form-edit');
    if (createForm) {
        createForm.reset();
        createForm.classList.remove('hidden');
    }
    if (editForm) editForm.classList.add('hidden');
    const title = document.getElementById('product-modal-title');
    if (title) title.textContent = 'Buat Produk';
};

window.openProductEditModal = function (id, productJson) {
    const modal = document.getElementById('product-modal');
    if (!modal) return;
    let product = productJson;
    try { if (typeof productJson === 'string') product = JSON.parse(productJson); } catch (err) { console.error(err); }
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    const createForm = document.getElementById('product-modal-form-create');
    const editForm = document.getElementById('product-modal-form-edit');
    if (createForm) createForm.classList.add('hidden');
    if (!editForm) return;
    editForm.classList.remove('hidden');

    // set action to produk/{id}
    if (window.productBaseUrl) {
        editForm.action = window.productBaseUrl.replace(/\/$/, '') + '/' + id;
    } else {
        editForm.action = '/produk/' + id;
    }

    // populate fields
    const setVal = (name, val) => {
        const el = editForm.querySelector('[name="' + name + '"]');
        if (!el) return;
        if (el.type === 'checkbox') el.checked = !!val;
        else el.value = val === null || val === undefined ? '' : val;
    };

    setVal('name', product.name ?? '');
    setVal('category_id', product.category_id ?? '');
    setVal('cost_price', product.cost_price ?? '');
    setVal('price', product.price ?? '');
    setVal('stock', product.stock ?? 0);
    setVal('sku', product.sku ?? '');
    setVal('barcode', product.barcode ?? '');
    setVal('description', product.description ?? '');
    setVal('is_active', product.is_active ?? false);

    // image preview
    const previewWrapper = document.getElementById('edit-image-preview-wrapper');
    if (previewWrapper) {
        previewWrapper.innerHTML = '';
        if (product.image) {
            const img = document.createElement('img');
            img.src = (product.image.startsWith('http') ? product.image : (window.location.origin + '/storage/' + product.image));
            img.alt = product.name || 'Product Image';
            img.className = 'h-24 w-24 rounded-lg object-cover';
            previewWrapper.appendChild(img);
        }
    }

    const title = document.getElementById('product-modal-title');
    if (title) title.textContent = 'Ubah Produk';
};

window.closeProductModal = function () {
    const modal = document.getElementById('product-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    // reset both forms
    const createForm = document.getElementById('product-modal-form-create');
    const editForm = document.getElementById('product-modal-form-edit');
    if (createForm) createForm.reset();
    if (editForm) editForm.reset();
};

window.openUserCreateModal = function (preserveForm = false) {
    const modal = document.getElementById('user-modal');
    if (!modal) return;
    const createForm = document.getElementById('user-modal-form-create');
    const editForm = document.getElementById('user-modal-form-edit');

    if (createForm) {
        if (!preserveForm) createForm.reset();
        createForm.classList.remove('hidden');
    }
    if (editForm) {
        if (!preserveForm) editForm.reset();
        editForm.classList.add('hidden');
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    const title = document.getElementById('user-modal-title');
    if (title) title.textContent = 'Buat User';
};

window.openUserEditModalFromData = function (user) {
    if (!user) return;
    const modal = document.getElementById('user-modal');
    if (!modal) return;

    const createForm = document.getElementById('user-modal-form-create');
    const editForm = document.getElementById('user-modal-form-edit');
    if (!editForm) return;

    if (createForm) createForm.classList.add('hidden');
    editForm.classList.remove('hidden');

    const routeTemplate = editForm.dataset.routeTemplate || '';
    if (routeTemplate) {
        editForm.action = routeTemplate.replace('__ID__', user.id);
    }

    const setVal = (name, value) => {
        const el = editForm.querySelector('[name="' + name + '"]');
        if (!el) return;
        if (el.type === 'checkbox') {
            el.checked = !!value;
        } else {
            el.value = value === null || value === undefined ? '' : value;
        }
    };

    setVal('user_id', user.id);
    setVal('name', user.name ?? '');
    setVal('email', user.email ?? '');
    setVal('role_id', user.role_id ?? '');
    setVal('password', '');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    const title = document.getElementById('user-modal-title');
    if (title) title.textContent = 'Edit User';
};

window.openUserEditModalFromButton = function (button) {
    if (!button || !button.dataset) return;
    let user = null;
    try {
        user = JSON.parse(button.dataset.user || '{}');
    } catch (err) {
        console.error(err);
    }
    if (!user) return;
    window.openUserEditModalFromData(user);
};

window.closeUserModal = function () {
    const modal = document.getElementById('user-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');

    const createForm = document.getElementById('user-modal-form-create');
    const editForm = document.getElementById('user-modal-form-edit');
    if (createForm) createForm.reset();
    if (editForm) editForm.reset();
};

window.openReportCreateModal = function () {
    const modal = document.getElementById('report-modal');
    if (!modal) return;

    const form = document.getElementById('report-modal-form');
    if (!form) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    form.action = '/report';
    form.method = 'POST';
    document.getElementById('report-modal-method').value = 'POST';
    document.getElementById('report-modal-form-mode').value = 'create';
    document.getElementById('report-modal-title').textContent = 'Buat Laporan Baru';
    document.getElementById('report-modal-save-button').classList.add('hidden');
    document.getElementById('report-modal-create-button').classList.remove('hidden');
    document.getElementById('report-modal-create-another-button').classList.remove('hidden');
    window.populateReportForm({ type: 'in', from_date: '', to_date: '' });
};

window.openReportEditModal = function (id, reportData) {
    const modal = document.getElementById('report-modal');
    if (!modal) return;

    const form = document.getElementById('report-modal-form');
    if (!form) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    form.action = '/report/' + id;
    form.method = 'POST';
    document.getElementById('report-modal-method').value = 'PATCH';
    document.getElementById('report-modal-form-mode').value = 'edit';
    document.getElementById('report-edit-id').value = id;
    document.getElementById('report-modal-title').textContent = 'Ubah Laporan';
    document.getElementById('report-modal-save-button').classList.remove('hidden');
    document.getElementById('report-modal-create-button').classList.add('hidden');
    document.getElementById('report-modal-create-another-button').classList.add('hidden');

    window.populateReportForm({
        type: reportData.type || 'in',
        from_date: reportData.from_date || '',
        to_date: reportData.to_date || '',
    });
};

window.populateReportForm = function (data = {}) {
    const type = data.type || 'in';
    const fromDate = data.from_date || '';
    const toDate = data.to_date || '';

    const typeInputs = document.querySelectorAll('#report-modal-form input[name="type"]');
    typeInputs.forEach((input) => {
        input.checked = input.value === type;
    });

    const fromInput = document.getElementById('report_from_date');
    const toInput = document.getElementById('report_to_date');

    if (fromInput) fromInput.value = fromDate;
    if (toInput) toInput.value = toDate;
};

window.closeReportModal = function () {
    const modal = document.getElementById('report-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    const form = document.getElementById('report-modal-form');
    if (form) form.reset();
};

window.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('report-modal');
    const backdrop = modal ? modal.querySelector('[data-modal-backdrop]') : null;

    if (backdrop) {
        backdrop.addEventListener('click', window.closeReportModal);
    }

    if (window.reportOpenOnLoad) {
        const mode = window.reportOpenOnLoad.mode;
        if (mode === 'create') {
            window.openReportCreateModal();
        } else if (mode === 'edit') {
            window.openReportEditModal(window.reportOpenOnLoad.values.id, window.reportOpenOnLoad.values);
        }
    }
});
