import { Modal } from '../../components/modal/modal.js';
import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initBusinessListView } from './listView.js';
import { initBusinessStatusFilters } from './statusFilters.js';

document.addEventListener('DOMContentLoaded', () => {
    initCollapsibleList('managementList');
    initBusinessListView(window.BE_DATA.businesses);
    initBusinessStatusFilters();
    initCollapsibleList('statusList');

    const createBtn = document.querySelector('[data-modal-target="create-business-modal"]');

    if (createBtn) {
        createBtn.addEventListener('click', () => {
            Modal.showCustom({
                title: 'Create New Business',
                confirmText: 'Create Business',
                body: `
                    <form id="modalForm" method="POST" action="${window.BE_DATA.routes.store}">
                        <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                        
                        <div class="modal-form__group">
                            <label class="modal-form__label">Business Name</label>
                            <div class="input-wrapper">
                                <input type="text" name="name" class="modal-form__input" placeholder="e.g. My Awesome Barbershop" required>
                            </div>
                        </div>

                        <div class="modal-form__group">
                            <label class="modal-form__label">Description</label>
                            <div class="input-wrapper">
                                <textarea name="description" class="modal-form__input" placeholder="Tell us more..." style="min-height: 100px;"></textarea>
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
});