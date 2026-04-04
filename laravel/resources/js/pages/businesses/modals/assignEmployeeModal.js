import { Modal } from '../../../components/displays/modal.js';

export function initAssignEmployeeModal() {
    const btn = document.querySelector('[data-modal-target="assign-employee-modal"]');
    if (!btn) return;

    btn.addEventListener('click', (e) => {
        e.preventDefault();

        Modal.showCustom({
            title: 'Assign Employee',
            confirmText: 'Assign & Notify',
            action:      'create',
            rules: {
                email: { required: { value: true, message: 'Email is required' } },
            },
            body: `
                <form id="assignEmployeeForm" method="POST" action="${window.BE_DATA.routes.assignUser}">
                    <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                    
                    <!-- Hidden fields updated by the selector -->
                    <input type="hidden" name="target_type" id="modal-hidden-target-type" value="business">
                    <input type="hidden" name="target_id"   id="modal-hidden-target-id"   value="${window.BE_DATA.business.id}">

                    <div class="modal-form__group">
                        <label class="modal-form__label">Assign To</label>
                        <div class="input-wrapper">
                            <select id="modal-target-selector" class="modal-form__input">
                                <option value="business" data-id="${window.BE_DATA.business.id}">Entire Business</option>
                                
                                <optgroup label="Branches">
                                    ${window.BE_DATA.business.branches.map(b => 
                                        `<option value="branch" data-id="${b.id}">${b.name}</option>`
                                    ).join('')}
                                </optgroup>

                                <optgroup label="Services">
                                    ${window.BE_DATA.business.services.map(s => 
                                        `<option value="service" data-id="${s.id}">${s.name}</option>`
                                    ).join('')}
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Member Email</label>
                        <div class="input-wrapper">
                            <input type="email" name="email" class="modal-form__input" placeholder="staff@example.com" required>
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
                Modal.clearFieldErrors(modal);
                const form = modal.querySelector('#assignEmployeeForm');
                
                const selector = modal.querySelector('#modal-target-selector');
                const selectedOption = selector.options[selector.selectedIndex];
                form.querySelector('#modal-hidden-target-type').value = selector.value;
                form.querySelector('#modal-hidden-target-id').value = selectedOption.dataset.id;

                const res = await fetch(form.action, {
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
                    return;
                }

                alert('Error while assigning employee. Please try again.');
            }
        });
    });
}