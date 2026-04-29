import { Modal } from '../../../components/displays/modal.js';

export function initEditServiceModal() {
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-modal-target="edit-service-modal"]');
        if (!trigger) return;

        event.preventDefault();

        const { service, routes } = window.BE_DATA;
        if (!service || !routes?.update) return;
        const businessBranches = (window.BE_DATA.branches || [])
            .filter((branch) => Number(branch.business_id) === Number(service.business_id));

        Modal.showCustom({
            title: `Manage Service: ${service.name}`,
            confirmText: 'Save Changes',
            action: 'edit',
            rules: {
                name: { required: { value: true, message: 'Service name is required' } },
                duration_minutes: { required: { value: true, message: 'Duration is required' } },
                price: { required: { value: true, message: 'Price is required' } },
            },
            body: `
                <form id="editServiceForm" method="POST" action="${routes.update}">
                    <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="business_id" value="${service.business_id}">

                    <div class="modal-form__group">
                        <label class="modal-form__label">Service Name</label>
                        <div class="input-wrapper">
                            <input
                                type="text"
                                name="name"
                                class="modal-form__input"
                                value="${escapeAttribute(service.name || '')}"
                                placeholder=" "
                                required>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Description</label>
                        <div class="input-wrapper">
                            <textarea
                                name="description"
                                class="modal-form__input"
                                placeholder=" "
                                style="min-height: 120px;">${escapeHtml(service.description || '')}</textarea>
                        </div>
                    </div>

                    <div class="modal-form__grid">
                        <div class="modal-form__group">
                            <label class="modal-form__label">Duration (minutes)</label>
                            <div class="input-wrapper">
                                <input
                                    type="number"
                                    name="duration_minutes"
                                    min="1"
                                    class="modal-form__input"
                                    value="${escapeAttribute(service.duration_minutes || '')}"
                                    placeholder=" "
                                    required>
                            </div>
                        </div>

                        <div class="modal-form__group">
                            <label class="modal-form__label">Price</label>
                            <div class="input-wrapper">
                                <input
                                    type="number"
                                    name="price"
                                    min="0"
                                    step="0.01"
                                    class="modal-form__input"
                                    value="${escapeAttribute(service.price || '')}"
                                    placeholder=" "
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-form__grid">
                        <div class="modal-form__group">
                            <label class="modal-form__label">Location Type</label>
                            <div class="input-wrapper">
                                <select name="location_type" class="modal-form__input">
                                    <option value="branch" ${!['online', 'hybrid'].includes(service.location_type) ? 'selected' : ''}>Branch</option>
                                    <option value="online" ${service.location_type === 'online' ? 'selected' : ''}>Online</option>
                                    <option value="hybrid" ${service.location_type === 'hybrid' ? 'selected' : ''}>Hybrid</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-form__group">
                            <label class="modal-form__label">Cancellation Period</label>
                            <div class="input-wrapper">
                                <input
                                    type="text"
                                    name="cancellation_period"
                                    class="modal-form__input"
                                    value="${escapeAttribute(service.cancellation_period || '')}"
                                    placeholder="e.g. 2d 3h, 90m">
                            </div>
                        </div>
                    </div>

                    <div class="service-settings__modal-section">
                        <span class="service-settings__modal-section-title">Branches</span>
                        <div class="service-settings__modal-branch-grid">
                            ${renderBranchCheckboxes(businessBranches, service.branches || [])}
                        </div>
                    </div>

                    <div class="service-settings__toggle-grid">
                        <label class="service-settings__toggle-card">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" ${service.is_active ? 'checked' : ''}>
                            <span class="service-settings__toggle-control"></span>
                            <span class="service-settings__toggle-text">
                                <strong>Active</strong>
                                <small>Service is visible and bookable.</small>
                            </span>
                        </label>

                        <label class="service-settings__toggle-card">
                            <input type="hidden" name="requires_manual_acceptance" value="0">
                            <input type="checkbox" name="requires_manual_acceptance" value="1" ${service.requires_manual_acceptance ? 'checked' : ''}>
                            <span class="service-settings__toggle-control"></span>
                            <span class="service-settings__toggle-text">
                                <strong>Manual acceptance</strong>
                                <small>Appointments wait for approval.</small>
                            </span>
                        </label>
                    </div>
                </form>
            `,
            onConfirm: async (modal) => {
                const form = modal.querySelector('#editServiceForm');
                const submitButton = modal.querySelector('.btn-confirm');

                Modal.clearFieldErrors(modal);

                if (submitButton) submitButton.disabled = true;

                try {
                    const formData = new FormData(form);
                    const isActiveInput = form.querySelector('input[name="is_active"][type="checkbox"]');
                    const requiresManualInput = form.querySelector('input[name="requires_manual_acceptance"][type="checkbox"]');

                    formData.delete('is_active');
                    formData.delete('requires_manual_acceptance');
                    formData.set('is_active', isActiveInput?.checked ? '1' : '0');
                    formData.set('requires_manual_acceptance', requiresManualInput?.checked ? '1' : '0');

                    const response = await fetch(routes.update, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (response.ok) {
                        window.location.reload();
                        return;
                    }

                    if (response.status === 422) {
                        const json = await response.json();
                        Modal.showFieldErrors(modal, json.errors || {});
                        return;
                    }

                    alert('Something went wrong while saving the service.');
                } catch (error) {
                    console.error('Service metadata update failed', error);
                    alert('Network error. Please try again.');
                } finally {
                    if (submitButton) submitButton.disabled = false;
                }
            },
        });
    });
}

function renderBranchCheckboxes(branches, selectedBranches) {
    const selectedIds = new Set(selectedBranches.map((branch) => Number(branch.id)));

    if (!branches.length) {
        return `<p class="service-settings__branch-empty">No branches available for this business.</p>`;
    }

    return branches
        .map((branch) => `
            <label class="checkbox-item">
                <input
                    type="checkbox"
                    name="branch_ids[]"
                    value="${branch.id}"
                    ${selectedIds.has(Number(branch.id)) ? 'checked' : ''}>
                <div class="checkbox-item__custom"></div>
                <span class="checkbox-item__text">
                    ${escapeHtml(branch.name || 'Unnamed branch')}
                    ${branch.city ? `<small>(${escapeHtml(branch.city)})</small>` : ''}
                </span>
            </label>
        `)
        .join('');
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;');
}

function escapeAttribute(value) {
    return escapeHtml(value).replaceAll('"', '&quot;');
}
