import { Modal } from '../../../components/modal/modal.js';

export function initEditBusinessMetaDataModal() {
    const btn = document.querySelector('[data-modal-target="edit-business-modal"]');

    if (!btn) return;

    btn.addEventListener('click', () => {
        const { name, description } = window.BE_DATA.business;
        const action = window.BE_DATA.routes.update;

        Modal.showCustom({
            title: 'Edit Business Info',
            confirmText: 'Save Changes',
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
            onConfirm: (modal) => {
                modal.querySelector('#editBusinessForm').submit();
            }
        });
    });
}