import { Modal } from '../../../components/displays/modal.js';

export function initCreateBranchModal() {
    // Delegácia eventov pre dynamicky generovaný toolbar
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-modal-target="create-branch-modal"]');
        if (!btn) return;

        e.preventDefault();
        openCreateBranchModal();
    });
}

function openCreateBranchModal() {
    Modal.showCustom({
        title:       'Create New Branch',
        confirmText: 'Create Branch',
        action:      'create',
        body: `
            <form id="createBranchForm">
                <div class="modal-form__group">
                    <label class="modal-form__label">Name</label>
                    <div class="input-wrapper">
                        <input type="text" name="name" class="modal-form__input" placeholder="Enter branch name" required autofocus>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Type</label>
                    <div class="input-wrapper">
                        <select name="type" class="modal-form__input">
                            <option value="physical">Physical</option>
                            <option value="online">Online</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label toggle-label" style="flex-direction: row; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Active Status
                    </label>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Street Address</label>
                    <div class="input-wrapper">
                        <input type="text" name="address_line_1" class="modal-form__input" placeholder="Bajkalská 21">
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Unit / Floor / Suite (Optional)</label>
                    <div class="input-wrapper">
                        <input type="text" name="address_line_2" class="modal-form__input" placeholder="2nd floor / door number 6">
                    </div>
                </div>

                <div class="modal-form__group" style="flex: 2;">
                    <label class="modal-form__label">City</label>
                    <input type="text" name="city" class="modal-form__input">
                </div>
                
                <div class="modal-form__group" style="flex: 1;">
                    <label class="modal-form__label">Postal Code</label>
                    <input type="text" name="postal_code" class="modal-form__input">
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Country</label>
                    <div class="input-wrapper">
                        <input type="text" name="country" class="modal-form__input" value="Slovakia">
                    </div>
                </div>
            </form>
        `,
        onConfirm: async (modal) => {
            const form = modal.querySelector('#createBranchForm');
            const submitBtn = modal.querySelector('[data-modal-action="confirm"]');
            
            if (submitBtn) submitBtn.disabled = true;
            Modal.clearFieldErrors(modal);

            const formData = new FormData(form);
            // Dáta, ktoré ťaháme z BE_DATA
            formData.append('_token', window.BE_DATA.csrf);
            formData.append('business_id', window.BE_DATA.business.id);

            try {
                const res = await fetch(window.BE_DATA.routes.branchStore, {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
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
                    console.error('Server error');
                }
            } catch (error) {
                console.error('Fetch error:', error);
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        }
    });
}