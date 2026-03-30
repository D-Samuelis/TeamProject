import { Modal } from '../../../components/displays/modal.js';

export function initEditBranchModal() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-modal-target="edit-branch-modal"]');
        if (!btn) return;

        const branch = JSON.parse(btn.dataset.branch);

        Modal.showCustom({
            title:       'Edit Branch',
            confirmText: 'Update Branch',
            action:      'edit',
            rules: {
                branchName: { required: { value: true, message: 'Branch name is required' } },
            },
            body: `
                <form id="editBranchForm" method="POST" action="${window.BE_DATA.routes.branchUpdate.replace(':id', branch.id)}">
                    <input type="hidden" name="_token"      value="${window.BE_DATA.csrf}">
                    <input type="hidden" name="_method"     value="PUT">
                    <input type="hidden" name="business_id" value="${window.BE_DATA.business.id}">

                    <div class="modal-form__group">
                        <label class="modal-form__label">Name</label>
                        <div class="input-wrapper">
                            <input type="text" name="name" class="modal-form__input"
                                value="${_esc(branch.name)}" placeholder=" " required autofocus>
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
                        <label class="modal-form__label toggle-label"
                            style="flex-direction: row; align-items: center; gap: 8px;">
                            <input type="hidden"   name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                ${branch.is_active == 1 ? 'checked' : ''}>
                            Active Status
                        </label>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Street Address</label>
                        <div class="input-wrapper">
                            <input type="text" name="address_line_1" class="modal-form__input"
                                value="${_esc(branch.address_line_1 ?? '')}" placeholder="Bajkalská 21">
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Unit / Floor / Suite (Optional)</label>
                        <div class="input-wrapper">
                            <input type="text" name="address_line_2" class="modal-form__input"
                                value="${_esc(branch.address_line_2 ?? '')}" placeholder="2nd floor, door number 6">
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">City</label>
                        <div class="input-wrapper">
                            <input type="text" name="city" class="modal-form__input"
                                value="${_esc(branch.city ?? '')}" placeholder=" ">
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Postal Code</label>
                        <div class="input-wrapper">
                            <input type="text" name="postal_code" class="modal-form__input"
                                value="${_esc(branch.postal_code ?? '')}" placeholder=" ">
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Country</label>
                        <div class="input-wrapper">
                            <input type="text" name="country" class="modal-form__input"
                                value="${_esc(branch.country ?? '')}" placeholder=" ">
                        </div>
                    </div>
                </form>
            `,
            onConfirm: async (modal) => {
                const form = modal.querySelector('#editBranchForm');
                const formData = new FormData(form);
                const data = new URLSearchParams(formData);

                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: data,
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

                alert('Something went wrong. Please try again.');
            }
        });
    });
}

function _esc(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}