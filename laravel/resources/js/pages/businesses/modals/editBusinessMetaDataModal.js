import { Modal } from '../../../components/displays/modal.js';

export function initEditBusinessMetaDataModal() {
    // Delegácia eventov pre dynamický toolbar
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-modal-target="edit-business-modal"]');
        if (!btn) return;

        e.preventDefault();
        openEditBusinessModal();
    });
}

function openEditBusinessModal() {
    const { name, description } = window.BE_DATA.business;
    const updateUrl = window.BE_DATA.routes.update;

    Modal.showCustom({
        title: 'Edit Business Info',
        confirmText: 'Save Changes',
        action: 'edit',
        body: `
            <form id="editBusinessForm">
                <div class="modal-form__group">
                    <label class="modal-form__label">Business Name</label>
                    <div class="input-wrapper">
                        <input type="text" name="name" class="modal-form__input"
                            value="${_esc(name)}" placeholder="Enter business name" required autofocus>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Description</label>
                    <div class="input-wrapper">
                        <textarea name="description" class="modal-form__input"
                            placeholder="Optional description" style="min-height: 100px; resize: vertical;">${_esc(description ?? '')}</textarea>
                    </div>
                </div>
            </form>
        `,
        onConfirm: async (modal) => {
            const form = modal.querySelector('#editBusinessForm');
            const submitBtn = modal.querySelector('[data-modal-action="confirm"]');
            
            if (submitBtn) submitBtn.disabled = true;
            Modal.clearFieldErrors(modal);

            const formData = new FormData(form);
            formData.append('_token', window.BE_DATA.csrf);
            formData.append('_method', 'PUT');

            try {
                const res = await fetch(updateUrl, {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.BE_DATA.csrf
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
                    const errorData = await res.json();
                    alert(errorData.message || 'Update failed');
                }
            } catch (error) {
                console.error('Fetch error:', error);
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        }
    });
}

/**
 * Jednoduchý escaping pre XSS ochranu a integritu HTML v value/textarea
 */
function _esc(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}
