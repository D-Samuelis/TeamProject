import { Modal } from '../../../components/displays/modal.js';
import { _esc } from '../../../utils/helpers.js';

export function initEditBranchModal() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-modal-target="edit-branch-modal"]');
        if (!btn) return;

        e.preventDefault();
        
        try {
            // Skúsime vziať dáta z tlačidla, ak nie sú, fallback na globálne dáta o pobočke
            const rawData = btn.dataset.branch || btn.dataset.branchData;
            let branch = typeof rawData === 'string' ? JSON.parse(rawData) : rawData;
            
            if (!branch) {
                branch = window.BE_DATA?.branch;
            }

            if (!branch || !branch.id) {
                throw new Error("Missing branch ID");
            }
            
            openEditBranchModal(branch);
        } catch (err) {
            console.error("Error parsing branch data for edit:", err);
        }
    });
}

function openEditBranchModal(branch) {
    const updateUrl = window.BE_DATA.routes.update.replace(':id', branch.id);
    const businesses = window.BE_DATA?.businesses || [];
    
    // Nájdenie aktuálne priradeného biznisu pre zobrazenie mena v inpute
    const currentBusiness = businesses.find(b => b.id == branch.business_id);

    Modal.showCustom({
        title: 'Edit Branch',
        confirmText: 'Save Changes',
        action: 'edit',
        body: `
            <form id="editBranchForm">
                <input type="hidden" name="business_id" id="edit_business_id_input" value="${branch.business_id}">

                <div class="modal-form__group">
                    <label class="modal-form__label">Business</label>
                    <div class="searchable-select-wrapper" style="position:relative;">
                        <input type="text" id="edit_business_search" class="modal-form__input" 
                               placeholder="Search and select business..." 
                               autocomplete="off" 
                               value="${currentBusiness ? _esc(currentBusiness.name) : ''}" required>
                        <div id="edit_business_dropdown" class="custom-dropdown" style="display:none; position:absolute; z-index:999; background:white; width:100%; border:1px solid #ddd; max-height:200px; overflow-y:auto;">
                            ${businesses.map(b => `<div class="dropdown-item" data-value="${b.id}" style="padding:10px; cursor:pointer;">${_esc(b.name)}</div>`).join('')}
                        </div>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Name</label>
                    <div class="input-wrapper">
                        <input type="text" name="name" class="modal-form__input"
                            value="${_esc(branch.name)}" placeholder="Branch name" required autofocus>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Type</label>
                    <div class="input-wrapper">
                        <select name="type" class="modal-form__input">
                            ${['physical', 'online', 'hybrid'].map(t => `
                                <option value="${t}" ${branch.type === t ? 'selected' : ''}>
                                    ${t.charAt(0).toUpperCase() + t.slice(1)}
                                </option>
                            `).join('')}
                        </select>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label toggle-label" style="display: flex; flex-direction: row; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" ${branch.is_active == 1 ? 'checked' : ''}>
                        Active Status
                    </label>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Street Address</label>
                    <div class="input-wrapper">
                        <input type="text" name="address_line_1" class="modal-form__input"
                            value="${_esc(branch.address_line_1 ?? '')}" placeholder="Street and number">
                    </div>
                </div>

                <div style="display: flex; gap: 12px;">
                    <div class="modal-form__group" style="flex: 2;">
                        <label class="modal-form__label">City</label>
                        <input type="text" name="city" class="modal-form__input" value="${_esc(branch.city ?? '')}">
                    </div>
                    <div class="modal-form__group" style="flex: 1;">
                        <label class="modal-form__label">Postal Code</label>
                        <input type="text" name="postal_code" class="modal-form__input" value="${_esc(branch.postal_code ?? '')}">
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Country</label>
                    <div class="input-wrapper">
                        <input type="text" name="country" class="modal-form__input" value="${_esc(branch.country ?? 'Slovakia')}">
                    </div>
                </div>
            </form>
        `,
        onConfirm: async (modal) => {
            const form = modal.querySelector('#editBranchForm');
            const submitBtn = modal.querySelector('[data-modal-action="confirm"]');
            const businessId = modal.querySelector('#edit_business_id_input').value;

            if (!businessId) {
                modal.querySelector('#edit_business_search').classList.add('input-error');
                return;
            }
            
            if (submitBtn) submitBtn.disabled = true;
            Modal.clearFieldErrors(modal);

            const formData = new FormData(form);
            formData.append('_token', window.BE_DATA.csrf);
            formData.append('_method', 'PUT');

            try {
                const res = await fetch(updateUrl, {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.BE_DATA.csrf
                    },
                    body: formData,
                });

                if (res.ok) {
                    window.location.reload();
                    return;
                }

                if (res.status === 422) {
                    const json = await res.json();
                    Modal.showFieldErrors(modal, json.errors);
                } else {
                    const errorData = await res.json();
                    alert(errorData.message || 'Error updating branch.');
                }
            } catch (error) {
                console.error('Update branch error:', error);
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        }
    });

    // Inicializácia vyhľadávania po renderi modalu
    setTimeout(setupEditBusinessSearch, 50);
}

function setupEditBusinessSearch() {
    const searchInput = document.getElementById('edit_business_search');
    const dropdown    = document.getElementById('edit_business_dropdown');
    const hiddenInput = document.getElementById('edit_business_id_input');
    
    if (!searchInput || !dropdown) return;

    const items = dropdown.querySelectorAll('.dropdown-item');

    searchInput.addEventListener('focus', () => {
        dropdown.style.display = 'block';
    });

    searchInput.addEventListener('input', () => {
        const filter = searchInput.value.toLowerCase();
        dropdown.style.display = 'block';
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(filter) ? 'block' : 'none';
        });
        
        // Ak používateľ zmaže text, vyčistíme ID
        if (!searchInput.value) hiddenInput.value = '';
    });

    dropdown.addEventListener('click', (e) => {
        const item = e.target.closest('.dropdown-item');
        if (item) {
            searchInput.value = item.textContent.trim();
            hiddenInput.value = item.dataset.value;
            dropdown.style.display = 'none';
            searchInput.classList.remove('input-error');
        }
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.searchable-select-wrapper')) {
            dropdown.style.display = 'none';
        }
    });
}