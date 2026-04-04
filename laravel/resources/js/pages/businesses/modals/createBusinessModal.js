import { Modal } from '../../../components/displays/modal.js';

export function initCreateBusinessModal() {
    const createBtn = document.querySelector('[data-modal-target="create-business-modal"]');

    if (!createBtn) return;

    createBtn.addEventListener('click', (e) => {
        e.preventDefault();

        Modal.showCustom({
            title: 'Create New Business',
            confirmText: 'Create Business',
            action:      'create',
            rules: {
                name: { required: { value: true, message: 'Business name is required' } },
            },
            body: `
                <form id="modalForm" method="POST" action="${window.BE_DATA.routes.store}">
                    <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                    
                    <div class="modal-form__group">
                        <label class="modal-form__label">Business Name</label>
                        <div class="input-wrapper">
                            <input type="text" name="name" class="modal-form__input" placeholder=" " required autofocus>
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
            onConfirm: async (modal) => {
                const form = modal.querySelector('#modalForm');
                const submitBtn = modal.querySelector('[data-modal-action="confirm"]');
                
                if (submitBtn) submitBtn.disabled = true;

                try {
                    const res = await fetch(window.BE_DATA.routes.store, {
                        method: 'POST',
                        headers: { 
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(form),
                    });

                    if (res.ok) {
                        window.location.reload();
                        return;
                    }

                    if (res.status === 422) {
                        const json = await res.json();
                        Modal.showFieldErrors(modal, json.errors);
                    } else {
                        alert('Server error. Please try again.');
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    alert('Network error. Check your connection.');
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                }
            }
        });
    });
}