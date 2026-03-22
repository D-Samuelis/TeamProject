import { Modal } from '../../../components/modal/modal.js';

export function initCreateBusinessModal() {
    const createBtn = document.querySelector('[data-modal-target="create-business-modal"]');

    if (!createBtn) return;

    createBtn.addEventListener('click', () => {
        Modal.showCustom({
            title: 'Create New Business',
            confirmText: 'Create Business',
            rules: {
                businessName: { required: { value: true, message: 'Business name is required' } },
            },
            body: `
                <form id="modalForm" method="POST" action="${window.BE_DATA.routes.store}">
                    <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                    
                    <div class="modal-form__group">
                        <label class="modal-form__label">Business Name</label>
                        <div class="input-wrapper">
                            <input type="text" name="businessName" class="modal-form__input" placeholder=" " required autofocus>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Description</label>
                        <div class="input-wrapper">
                            <textarea name="description" class="modal-form__input" placeholder=" " style="min-height: 100px;"></textarea>
                        </div>
                    </div>
                </form>
            `,
            onConfirm: (modal) => {
                modal.querySelector('#modalForm').submit();
            }
        });
    });
}