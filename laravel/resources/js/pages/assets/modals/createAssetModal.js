import { Modal } from '../../../components/displays/modal.js';

export function initCreateAssetModal() {
    const btn = document.querySelector('[data-modal-target="create-asset-modal"]');
    if (!btn) return;

    btn.addEventListener('click', (e) => {
        e.preventDefault();

        Modal.showCustom({
            title: 'Create New Asset',
            type: 'New Record',
            action: 'create',
            confirmText: 'Create Asset',
            body: `
                <form id="createAssetForm" class="modal-form">
                    <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">

                    <div class="modal-form__group">
                        <label class="modal-form__label">Asset Name</label>
                        <input type="text" name="name" class="modal-form__input" placeholder="e.g. Žigulík 01" maxlength="25" required autofocus>
                    </div>

                     <div class="modal-form__group">
                        <label class="modal-form__label">Status</label>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="is_active"}>
                            <span>Active</span>
                        </label>
                        <input type="hidden" name="is_active" value="0">
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Description</label>
                        <textarea name="description" class="modal-form__input" style="min-height: 80px;" placeholder="Briefly describe the asset..."></textarea>
                    </div>

                    <div class="modal-form__group" style="position: relative;">
                        <label class="modal-form__label">Assign to Branch</label>
                        <input type="hidden" name="branch_id" id="hidden_branch_id">
                        <div class="searchable-select-wrapper">
                            <input type="text" id="branch_search" class="modal-form__input" placeholder="Search and select branch..." autocomplete="off">
                            <div id="branch_dropdown" class="custom-dropdown" style="display: none;">
                                ${window.BE_DATA.allBranches.map(b => `
                                    <div class="dropdown-item" data-value="${b.id}">${b.name}</div>
                                `).join('')}
                            </div>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Assign to Services</label>
                        <div id="services-container" class="checkbox-list-wrapper" style="max-height: 160px; overflow-y: auto; border: 1px solid var(--color-border-light); padding: 10px; border-radius: 4px; background: var(--color-bg-light);">
                            ${renderServicesWithData('service_ids[]', window.BE_DATA.allServices)}
                            <p id="services-placeholder" style="font-size: 11px; color: var(--color-text-light); padding: 10px; text-align: center;">
                                <i class="fa-solid fa-arrow-up" style="margin-right: 5px;"></i> Firstly, choose a branch to see available services
                            </p>
                        </div>
                    </div>
                </form>
            `,
            onConfirm: async (modal) => {
                const form = modal.querySelector('#createAssetForm');
                const submitBtn = modal.querySelector('.btn-confirm');

                Modal.clearFieldErrors(modal);
                if (!form.querySelector('#hidden_branch_id').value) {
                    Modal.showFieldErrors(modal, { branch_id: ['Please select a branch from the list'] });
                    return;
                }

                if (submitBtn) submitBtn.disabled = true;

                try {
                    const res = await fetch(window.BE_DATA.routes.store, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': window.BE_DATA.csrf
                        },
                        body: new FormData(form)
                    });

                    if (res.ok) {
                        window.location.reload();
                        return;
                    }

                    if (res.status === 422) {
                        const json = await res.json();
                        const normalizedErrors = {};
                        for (const key in json.errors) {
                            const normalizedKey = key === 'service_ids' ? 'service_ids[]' : key;
                            normalizedErrors[normalizedKey] = json.errors[key];
                        }
                        Modal.showFieldErrors(modal, normalizedErrors);
                    }
                } catch (err) {
                    console.error("Fetch error:", err);
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                }
            }
        });

        setupSearchableSelect();
    });
}

function setupSearchableSelect() {
    const modalEl = document.getElementById('dynamic-modal');
    if (!modalEl) return;

    const searchInput = modalEl.querySelector('#branch_search');
    const dropdown = modalEl.querySelector('#branch_dropdown');
    const hiddenInput = modalEl.querySelector('#hidden_branch_id');
    const items = dropdown.querySelectorAll('.dropdown-item');
    const serviceLabels = modalEl.querySelectorAll('.service-filterable');
    const placeholder = modalEl.querySelector('#services-placeholder');

    searchInput.addEventListener('focus', () => {
        dropdown.style.display = 'block';
        items.forEach(item => item.style.display = 'block');
    });

    searchInput.addEventListener('input', () => {
        const filter = searchInput.value.toLowerCase();
        dropdown.style.display = 'block';

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(filter) ? 'block' : 'none';
        });

        if (!searchInput.value) {
            hiddenInput.value = '';
            filterServices('');
        }
    });

    items.forEach(item => {
        item.addEventListener('click', () => {
            searchInput.value = item.textContent;
            hiddenInput.value = item.dataset.value;
            dropdown.style.display = 'none';
            filterServices(item.dataset.value);
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.searchable-select-wrapper')) {
            dropdown.style.display = 'none';
        }
    });

    function filterServices(selectedBranchId) {
        let visibleCount = 0;
        serviceLabels.forEach(label => {
            const serviceBranchIds = label.dataset.branchIds.split(',');
            const hasMatch = selectedBranchId && serviceBranchIds.includes(selectedBranchId);

            if (hasMatch) {
                label.style.display = 'flex';
                visibleCount++;
            } else {
                label.style.display = 'none';
                label.querySelector('input').checked = false;
            }
        });

        if (placeholder) {
            placeholder.style.display = (visibleCount === 0) ? 'block' : 'none';
            placeholder.innerHTML = selectedBranchId
                ? '<i class="fa-solid fa-circle-info"></i> No services available for this branch'
                : '<i class="fa-solid fa-arrow-up"></i> Firstly, choose a branch to see available services';
        }
    }
}

function renderServicesWithData(name, items) {
    if (!items || items.length === 0) return '';
    return items.map(item => {
        const branchIds = item.branches ? item.branches.map(b => b.id).join(',') : '';
        return `
            <label class="checkbox-item service-filterable" data-branch-ids="${branchIds}" style="display: none;">
                <input type="checkbox" name="${name}" value="${item.id}">
                <div class="checkbox-item__custom"></div>
                <span class="checkbox-item__text" title="${item.name}">${truncate(item.name, 25)}</span>
            </label>
        `;
    }).join('');
}

function truncate(str, n) {
    return (str.length > n) ? str.slice(0, n-1) + '&hellip;' : str;
}
