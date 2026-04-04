import { Modal } from '../../../components/displays/modal.js';

export function initEditBusinessMetaDataModal() {
    console.log("DEBUG: initEditBranchModal sa spustil pri loade stránky.");
    
    const btn = document.querySelector('[data-modal-target="edit-business-modal"]');

    if (!btn) return;

    btn.addEventListener('click', () => {
        const { name, description } = window.BE_DATA.business;
        const action = window.BE_DATA.routes.update;

        Modal.showCustom({
            title: 'Edit Business Info',
            confirmText: 'Save Changes',
            action:      'edit',
            rules: {
                name: { required: { value: true, message: 'Business name is required' } },
            },
            body: `
                <form id="editBusinessForm" method="POST" action="${action}">
                    <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                    <input type="hidden" name="_method" value="PUT">

                    <div class="modal-form__group">
                        <label class="modal-form__label">Business Name</label>
                        <div class="input-wrapper">
                            <input type="text" name="name" class="modal-form__input"
                                value="${name}" placeholder=" " required autofocus>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Description</label>
                        <div class="input-wrapper">
                            <textarea name="description" class="modal-form__input"
                                placeholder=" " style="min-height: 100px;">${description ?? ''}</textarea>
                        </div>
                    </div>
                </form>
            `,
            onConfirm: async (modal) => {
                Modal.clearFieldErrors(modal);

                const form = modal.querySelector('#editBusinessForm');

                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form),
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