import { Modal } from '../../../components/displays/modal.js';

export function initCreateBusinessModal() {
    // Použijeme delegáciu eventov na document
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-modal-target="create-business-modal"]');
        if (!btn) return;

        e.preventDefault();
        openCreateBusinessModal();
    });
}

function openCreateBusinessModal() {
    Modal.showCustom({
        title: 'Create New Business',
        confirmText: 'Create Business',
        action: 'create',
        body: `
            <form id="createBusinessForm">
                <div class="modal-form__group">
                    <label class="modal-form__label">Business Name</label>
                    <div class="input-wrapper">
                        <input type="text" name="name" class="modal-form__input" placeholder="Enter name..." required autofocus>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Description</label>
                    <div class="input-wrapper">
                        <textarea name="description" class="modal-form__input" placeholder="Optional description..." style="min-height: 100px;"></textarea>
                    </div>
                </div>
            </form>
        `,
        onConfirm: async (modal) => {
            const form = modal.querySelector('#createBusinessForm');
            const submitBtn = modal.querySelector('[data-modal-action="confirm"]');
            
            if (submitBtn) submitBtn.disabled = true;

            const formData = new FormData(form);
            formData.append('_token', window.BE_DATA.csrf);

            try {
                const res = await fetch(window.BE_DATA.routes.store, {
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
                    console.error('Server error during business creation');
                }
            } catch (error) {
                console.error('Fetch error:', error);
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        }
    });
}