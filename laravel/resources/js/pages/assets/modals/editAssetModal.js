import { Modal } from '../../../components/displays/modal.js';

export function initEditAssetModal() {
    const editBtn = document.querySelector('[onclick="openModal(\'editAssetModal\')"]');
    if (!editBtn) return;

    const asset = window.BE_DATA.asset;
    const branches = window.BE_DATA.allBranches;
    const services = window.BE_DATA.allServices;

    editBtn.onclick = (e) => {
        e.preventDefault();

        Modal.showCustom({
            title: 'Manage Asset',
            confirmText: 'Save Changes',
            action: 'update',
            body: `
                <form id="editAssetForm" method="POST" action="/manage/assets/${asset.id}">
                    <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                    <input type="hidden" name="_method" value="PUT">

                    <div class="modal-form__group">
                        <label class="modal-form__label">Name *</label>
                        <input type="text" name="name" class="modal-form__input" value="${asset.name}" required>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Description</label>
                        <textarea name="description" class="modal-form__input" rows="3">${asset.description || ''}</textarea>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Branches</label>
                        <div class="modal-form__checkbox-list">
                            ${branches.map(b => `
                                <label class="checkbox-item">
                                    <input type="checkbox" name="branch_ids[]" value="${b.id}" 
                                        ${asset.branches.some(ab => ab.id === b.id) ? 'checked' : ''}>
                                    ${b.name}
                                </label>
                            `).join('')}
                        </div>
                    </div>
                </form>
            `,
            onConfirm: async (modal) => {
                const form = modal.querySelector('#editAssetForm');
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                if (res.ok) window.location.reload();
                else alert('Error updating asset.');
            }
        });
    };
}