import { Modal } from '../../../components/displays/modal.js';

export function initEditAssetModal() {
    const editBtn = document.querySelector('[data-modal-target="edit-business-modal"]');
    
    if (!editBtn) return;

    console.log("Edit Asset Modal logic loaded");

    const asset = window.BE_DATA.asset;
    const branches = window.BE_DATA.allBranches || [];
    const services = window.BE_DATA.allServices || [];

    editBtn.addEventListener('click', (e) => {
        e.preventDefault();

        Modal.showCustom({
            title: 'Manage Asset',
            confirmText: 'Save Changes',
            action: 'edit',
            body: `
                <form id="editAssetForm" method="POST" action="/manage/assets/${asset.id}">
                    <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                    <input type="hidden" name="_method" value="PUT">

                    <div class="modal-form__group">
                        <label class="modal-form__label">Name</label>
                        <input type="text" name="name" class="modal-form__input" value="${asset.name}" required>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Description (Optional)</label>
                        <textarea name="description" class="modal-form__input" rows="3">${asset.description || ''}</textarea>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Branches</label>
                        <div class="modal-form__checkbox-list" style="max-height: 200px; overflow-y: auto; border: 1px solid var(--color-border-light); padding: 10px; border-radius: 4px;">
                            ${branches.map(b => `
                                <label class="checkbox-item" style="display: block; margin-bottom: 5px; cursor: pointer;">
                                    <input type="checkbox" name="branch_ids[]" value="${b.id}" 
                                        ${asset.branches && asset.branches.some(ab => ab.id === b.id) ? 'checked' : ''}>
                                    ${b.name}
                                </label>
                            `).join('')}
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Services</label>
                        <div class="modal-form__checkbox-list" style="max-height: 200px; overflow-y: auto; border: 1px solid var(--color-border-light); padding: 10px; border-radius: 4px;">
                            ${services.map(b => `
                                <label class="checkbox-item" style="display: block; margin-bottom: 5px; cursor: pointer;">
                                    <input type="checkbox" name="branch_ids[]" value="${b.id}" 
                                        ${asset.services && asset.services.some(ab => ab.id === b.id) ? 'checked' : ''}>
                                    ${b.name}
                                </label>
                            `).join('')}
                        </div>
                    </div>

                </form>
            `,
            onConfirm: async (modal) => {
                Modal.clearFieldErrors(modal);

                const form = modal.querySelector('#editAssetForm');
                const formData = new FormData(form);

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: { 
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    if (res.ok) {
                        window.location.reload();
                        return;
                    }

                    if (res.status === 422) {
                        const json = await res.json();
                        Modal.showFieldErrors(modal, json.errors);
                        return;
                    }
                } catch (err) {
                    console.error(err);
                }
                
                alert('Something went wrong.');
            }
        });
    });
}