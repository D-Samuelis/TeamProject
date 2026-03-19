import { Modal } from '../../../components/modal/modal.js';

export function initCreateBranchModal() {
    const btn = document.querySelector('[data-modal-target="create-branch-modal"]');
    if (!btn) return;

    btn.addEventListener('click', () => {
        Modal.showCustom({
            title:       'Create Branch',
            confirmText: 'Save Branch',
            rules: {
                branchName: { required: { value: true, message: 'Branch name is required' } },
            },
            body: `
                <form id="createBranchForm" method="POST" action="${window.BE_DATA.routes.branchStore}">
                    <input type="hidden" name="_token"       value="${window.BE_DATA.csrf}">
                    <input type="hidden" name="business_id"  value="${window.BE_DATA.business.id}">

                    <div class="modal-form__group">
                        <label class="modal-form__label">Name</label>
                        <div class="input-wrapper">
                            <input type="text" name="branchName" class="modal-form__input"
                                placeholder=" " required autofocus>
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
                        <label class="modal-form__label toggle-label"
                            style="flex-direction: row; align-items: center; gap: 8px;">
                            <input type="hidden"   name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" checked>
                            Active Status
                        </label>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Address Line 1</label>
                        <div class="input-wrapper">
                            <input type="text" name="address_line_1" class="modal-form__input" placeholder=" ">
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Address Line 2</label>
                        <div class="input-wrapper">
                            <input type="text" name="address_line_2" class="modal-form__input" placeholder=" ">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="modal-form__group">
                            <label class="modal-form__label">City</label>
                            <div class="input-wrapper">
                                <input type="text" name="city" class="modal-form__input" placeholder=" ">
                            </div>
                        </div>
                        <div class="modal-form__group">
                            <label class="modal-form__label">Postal Code</label>
                            <div class="input-wrapper">
                                <input type="text" name="postal_code" class="modal-form__input" placeholder=" ">
                            </div>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Country</label>
                        <div class="input-wrapper">
                            <input type="text" name="country" class="modal-form__input" placeholder=" ">
                        </div>
                    </div>
                </form>
            `,
            onConfirm: (modal) => {
                modal.querySelector('#createBranchForm').submit();
            }
        });
    });
}