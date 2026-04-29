import { Modal } from '../../../components/displays/modal.js';

export function initAssignEmployeeModal() {
    // Event delegation pre toolbar
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-modal-target="assign-user-modal"]');
        // Pozor: v BE_DATA si predtým použil 'assign-user-modal', 
        // tak som to zjednotil, aby to sedelo na toolbar config.
        if (!btn) return;

        e.preventDefault();
        openAssignEmployeeModal();
    });
}

function openAssignEmployeeModal() {
    const business = window.BE_DATA.business;

    Modal.showCustom({
        title: 'Assign Employee',
        confirmText: 'Assign & Notify',
        action: 'create',
        body: `
            <form id="assignEmployeeForm">
                <div class="modal-form__group">
                    <label class="modal-form__label">Assign To</label>
                    <div class="input-wrapper">
                        <select id="modal-target-selector" class="modal-form__input">
                            <option value="business" data-id="${business.id}">Entire Business</option>
                            
                            ${business.branches?.length ? `
                                <optgroup label="Branches">
                                    ${business.branches.map(b => 
                                        `<option value="branch" data-id="${b.id}">${b.name}</option>`
                                    ).join('')}
                                </optgroup>
                            ` : ''}

                            ${business.services?.length ? `
                                <optgroup label="Services">
                                    ${business.services.map(s => 
                                        `<option value="service" data-id="${s.id}">${s.name}</option>`
                                    ).join('')}
                                </optgroup>
                            ` : ''}
                        </select>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Member Email</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" class="modal-form__input" placeholder="staff@example.com" required autofocus>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Role</label>
                    <div class="input-wrapper">
                        <select name="role" class="modal-form__input">
                            <option value="manager">Manager</option>
                            <option value="staff" selected>Staff</option>
                        </select>
                    </div>
                </div>
            </form>
        `,
        onConfirm: async (modal) => {
            const form = modal.querySelector('#assignEmployeeForm');
            const submitBtn = modal.querySelector('[data-modal-action="confirm"]');
            const selector = modal.querySelector('#modal-target-selector');
            const selectedOption = selector.options[selector.selectedIndex];

            if (submitBtn) submitBtn.disabled = true;
            Modal.clearFieldErrors(modal);

            const formData = new FormData(form);
            
            // Prilepíme potrebné BE dáta a dynamické ID zo selectu
            formData.append('_token', window.BE_DATA.csrf);
            formData.append('target_type', selector.value);
            formData.append('target_id', selectedOption.dataset.id);

            try {
                const res = await fetch(window.BE_DATA.routes.assignUser, {
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
                    console.error('Assignment failed');
                }
            } catch (error) {
                console.error('Fetch error:', error);
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        }
    });
}