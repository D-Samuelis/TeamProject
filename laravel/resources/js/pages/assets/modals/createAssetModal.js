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
            rules: {
                name: { 
                    required: { value: true, message: 'Asset name is required' } 
                },
                description: { 
                    required: { value: true, message: 'Please provide a short description' } 
                }
            },
            body: `
                <form id="createAssetForm" class="modal-form">
                    <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                    
                    <div class="modal-form__group">
                        <label class="modal-form__label">Asset Name</label>
                        <input type="text" name="name" class="modal-form__input" placeholder="e.g. Žigulík 01" maxlength="25" required autofocus>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Description</label>
                        <textarea name="description" class="modal-form__input" style="min-height: 80px;" placeholder="Briefly describe the asset..."></textarea>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Assign to Branches</label>
                        <div class="checkbox-list-wrapper" style="max-height: 160px; overflow-y: auto; border: 1px solid var(--color-border-light); padding: 10px; border-radius: 4px; background: var(--color-bg-light);">
                            ${renderCheckboxList('branch_ids[]', window.BE_DATA.allBranches)}
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Assign to Services</label>
                        <div id="services-container" class="checkbox-list-wrapper" style="max-height: 160px; overflow-y: auto; border: 1px solid var(--color-border-light); padding: 10px; border-radius: 4px; background: var(--color-bg-light);">
                            ${renderServicesWithData('service_ids[]', window.BE_DATA.allServices)}
                            <p id="services-placeholder" style="font-size: 11px; color: var(--color-text-light); padding: 10px; text-align: center; display: none;">
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
                            if (key.includes('.') || key === 'service_ids' || key === 'branch_ids') {
                                const baseKey = key.split('.')[0];
                                normalizedErrors[`${baseKey}[]`] = json.errors[key];
                            } else {
                                normalizedErrors[key] = json.errors[key];
                            }
                        }
                        Modal.showFieldErrors(modal, normalizedErrors);
                    } else if (res.status === 500) {
                        alert('Database Error: Skontroluj, či si vyplnil Description.');
                    }
                } catch (err) {
                    console.error("Fetch error:", err);
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                }
            }
        });

        const modalEl = document.getElementById('dynamic-modal');
        if (modalEl) {
            const branchInputs = modalEl.querySelectorAll('input[name="branch_ids[]"]');
            const serviceLabels = modalEl.querySelectorAll('.service-filterable');
            const placeholder = modalEl.querySelector('#services-placeholder');

            const filterServices = () => {
                const activeBranchIds = Array.from(branchInputs)
                    .filter(i => i.checked)
                    .map(i => i.value);

                let visibleCount = 0;

                serviceLabels.forEach(label => {
                    const serviceBranchIds = label.dataset.branchIds.split(',');
                    const hasMatch = serviceBranchIds.some(id => activeBranchIds.includes(id));
                    
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

            branchInputs.forEach(input => input.addEventListener('change', filterServices));
            filterServices();
        }
    });
}

function renderCheckboxList(name, items) {
    if (!items || items.length === 0) return '<p style="font-size: 12px; color: var(--color-text-light); padding: .5rem;">No branches available</p>';
    
    return items.map(item => `
        <label class="checkbox-item">
            <input type="checkbox" name="${name}" value="${item.id}">
            <div class="checkbox-item__custom"></div>
            <span class="checkbox-item__text" title="${item.name}">${truncate(item.name, 25)}</span>
        </label>
    `).join('');
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