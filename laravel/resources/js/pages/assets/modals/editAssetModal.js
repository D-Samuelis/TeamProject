import { Modal } from '../../../components/displays/modal.js';

export function initEditAssetModal() {
    const editBtn = document.querySelector('[data-modal-target="edit-business-modal"]');
    if (!editBtn) return;

    const asset = window.BE_DATA.asset;
    const allBranches = window.BE_DATA.allBranches || [];
    const allServices = window.BE_DATA.allServices || [];

    editBtn.addEventListener('click', (e) => {
        e.preventDefault();

        Modal.showCustom({
            title: `Manage Asset: ${asset.name}`,
            confirmText: 'Save Changes',
            action: 'edit',
            body: `
                <form id="editAssetForm" method="POST" action="/manage/assets/${asset.id}">
                    <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                    <input type="hidden" name="_method" value="PUT">

                    <div class="modal-form__group">
                        <label class="modal-form__label">Asset Name</label>
                        <input type="text" name="name" class="modal-form__input" value="${asset.name}" maxlength="25" required>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Description (Optional)</label>
                        <textarea name="description" class="modal-form__input" rows="3">${asset.description || ''}</textarea>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Assign to Branch</label>
                        <div class="searchable-select-wrapper">
                            <input type="hidden" name="branch_id" id="hidden_branch_id" value="${asset.branch ? asset.branch.id : ''}">
                            <input type="text" id="branch_search" class="modal-form__input" 
                                   placeholder="Search and select branch..." 
                                   value="${asset.branch ? asset.branch.name : ''}" 
                                   autocomplete="off">
                            
                            <div id="branch_dropdown" class="custom-dropdown" style="display: none;">
                                ${allBranches.map(b => `
                                    <div class="dropdown-item" data-value="${b.id}">${b.name}</div>
                                `).join('')}
                            </div>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Services</label>
                        <div id="services-container" class="checkbox-list-wrapper" style="max-height: 180px; overflow-y: auto; border: 1px solid var(--color-border-light); padding: 10px; border-radius: 4px; background: var(--color-bg-light);">
                            ${renderServiceCheckboxesWithData('service_ids[]', allServices, asset.services)}
                            <p id="services-placeholder" style="font-size: 11px; color: var(--color-text-light); padding: 10px; text-align: center;">
                                <i class="fa-solid fa-arrow-up" style="margin-right: 5px;"></i> Firstly, choose a branch to see available services
                            </p>
                        </div>
                    </div>
                </form>
            `,
            onConfirm: async (modal) => {
                const form = modal.querySelector('#editAssetForm');
                const submitBtn = modal.querySelector('.btn-confirm');
                
                Modal.clearFieldErrors(modal);
                if (submitBtn) submitBtn.disabled = true;

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: { 
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
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

        setupEditSearchableSelect();
    });
}

function setupEditSearchableSelect() {
    const modalEl = document.getElementById('dynamic-modal');
    if (!modalEl) return;

    const searchInput = modalEl.querySelector('#branch_search');
    const dropdown = modalEl.querySelector('#branch_dropdown');
    const hiddenInput = modalEl.querySelector('#hidden_branch_id');
    const items = dropdown.querySelectorAll('.dropdown-item');
    const serviceLabels = modalEl.querySelectorAll('.service-filterable');
    const placeholder = modalEl.querySelector('#services-placeholder');

    const filterServices = (selectedBranchId) => {
        let visibleCount = 0;
        serviceLabels.forEach(label => {
            const serviceBranchIds = label.dataset.branchIds.split(',');
            const hasMatch = selectedBranchId && serviceBranchIds.includes(selectedBranchId.toString());
            
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
        }
    };

    if (hiddenInput.value) {
        filterServices(hiddenInput.value);
    }

    searchInput.addEventListener('focus', () => {
        dropdown.style.display = 'block';
        items.forEach(item => item.style.display = 'block');
    });

    searchInput.addEventListener('input', () => {
        const filter = searchInput.value.toLowerCase();
        dropdown.style.display = 'block';
        items.forEach(item => {
            item.style.display = item.textContent.toLowerCase().includes(filter) ? 'block' : 'none';
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
}

function renderServiceCheckboxesWithData(name, allItems, selectedItems) {
    if (!allItems.length) return '';
    const selectedIds = new Set((selectedItems || []).map(item => item.id));

    return allItems.map(item => {
        const branchIds = item.branches ? item.branches.map(b => b.id).join(',') : '';
        return `
            <label class="checkbox-item service-filterable" data-branch-ids="${branchIds}" style="display: none;">
                <input type="checkbox" name="${name}" value="${item.id}" ${selectedIds.has(item.id) ? 'checked' : ''}>
                <div class="checkbox-item__custom"></div>
                <span class="checkbox-item__text" title="${item.name}">${truncate(item.name, 25)}</span>
            </label>
        `;
    }).join('');
}

function truncate(str, n) {
    return (str.length > n) ? str.slice(0, n-1) + '&hellip;' : str;
}