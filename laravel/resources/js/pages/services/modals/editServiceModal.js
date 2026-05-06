import { Modal } from '../../../components/displays/modal.js';

export function initEditServiceModal() {
    consumeQueuedPageAlert();

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-modal-target="edit-service-modal"]');
        if (!trigger) return;

        event.preventDefault();

        // Ťaháme dáta z window.BE_DATA (štandard pre show detail)
        const { service, routes, branches, categories = [], csrf } = window.BE_DATA || {};
        
        if (!service || !routes?.update) {
            console.error("Missing service data or update route.");
            return;
        }

        // Filtrujeme pobočky patriace pod biznis tejto služby
        const businessBranches = (branches || [])
            .filter((branch) => Number(branch.business_id) === Number(service.business_id));

        Modal.showCustom({
            title: `Manage Service: ${service.name}`,
            confirmText: 'Save Changes',
            action: 'edit',
            rules: {
                name: { required: { value: true, message: 'Service name is required' } },
                category_id: { required: { value: true, message: 'Please choose an existing category' } },
                duration_minutes: { required: { value: true, message: 'Duration is required' } },
                price: { required: { value: true, message: 'Price is required' } },
            },
            body: `
                <form id="editServiceForm" method="POST" action="${routes.update}">
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="business_id" value="${service.business_id}">

                    <div class="modal-form__group">
                        <label class="modal-form__label">Service Name</label>
                        <div class="input-wrapper">
                            <input type="text" name="name" class="modal-form__input" 
                                   value="${escapeAttribute(service.name || '')}" required>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Description</label>
                        <div class="input-wrapper">
                            <textarea name="description" class="modal-form__input" 
                                      style="min-height: 100px;">${escapeHtml(service.description || '')}</textarea>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Category</label>
                        <div class="input-wrapper">
                            <select name="category_id" class="modal-form__input">
                                ${renderCategoryOptions(categories, service.category_id)}
                            </select>
                        </div>
                        <p class="category-request-hint">
                            <span>Missing a category?</span>
                            <button
                                type="button"
                                id="open_category_request_modal"
                                class="category-request-link"
                            >
                                <i class="fa-solid fa-circle-info"></i>
                                <span>Request new category</span>
                            </button>
                        </p>
                    </div>

                    <div class="modal-form__grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="modal-form__group">
                            <label class="modal-form__label">Duration (min)</label>
                            <input type="number" name="duration_minutes" min="1" class="modal-form__input" 
                                   value="${service.duration_minutes}" required>
                        </div>
                        <div class="modal-form__group">
                            <label class="modal-form__label">Price (€)</label>
                            <input type="number" name="price" min="0" step="0.01" class="modal-form__input" 
                                   value="${service.price}" required>
                        </div>
                    </div>

                    <div class="modal-form__grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="modal-form__group">
                            <label class="modal-form__label">Location Type</label>
                            <select name="location_type" class="modal-form__input">
                                <option value="branch" ${!['online', 'hybrid'].includes(service.location_type) ? 'selected' : ''}>Branch</option>
                                <option value="online" ${service.location_type === 'online' ? 'selected' : ''}>Online</option>
                                <option value="hybrid" ${service.location_type === 'hybrid' ? 'selected' : ''}>Hybrid</option>
                            </select>
                        </div>
                        <div class="modal-form__group">
                            <label class="modal-form__label">Cancel Period</label>
                            <input type="text" name="cancellation_period" class="modal-form__input" 
                                   value="${escapeAttribute(service.cancellation_period || '')}" placeholder="e.g. 24h">
                        </div>
                    </div>

                    <div class="service-settings__modal-section modal-form__group" style="margin-top: 1.5rem;">
                        <label class="modal-form__label" style="display: block; margin-bottom: 10px;">Assigned Branches</label>
                        <div class="service-settings__modal-branch-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            ${renderBranchCheckboxes(businessBranches, service.branches || [])}
                        </div>
                    </div>

                    <div class="service-settings__toggle-grid modal-form__group" style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 10px;">
                        <label class="checkbox-item toggle-style">
                            <input type="checkbox" name="is_active" value="1" ${service.is_active ? 'checked' : ''}>
                            <span class="checkbox-item__text"><strong>Active</strong> - Service is visible</span>
                        </label>

                        <label class="checkbox-item toggle-style">
                            <input type="checkbox" name="requires_manual_acceptance" value="1" ${service.requires_manual_acceptance ? 'checked' : ''}>
                            <span class="checkbox-item__text"><strong>Manual acceptance</strong> - Approval needed</span>
                        </label>
                    </div>
                </form>
            `,
            onConfirm: async (modal) => {
                const form = modal.querySelector('#editServiceForm');
                const submitBtn = modal.querySelector('[data-modal-action="confirm"]');

                Modal.clearFieldErrors(modal);
                if (submitBtn) submitBtn.disabled = true;

                try {
                    const formData = new FormData(form);
                    
                    // Ošetrenie checkboxov (FormData neposiela nič, ak je checkbox unchecked)
                    if (!formData.has('is_active')) formData.set('is_active', '0');
                    if (!formData.has('requires_manual_acceptance')) formData.set('requires_manual_acceptance', '0');
                    
                    // Laravel niekedy potrebuje explicitné spracovanie polí pre pobočky
                    if (!formData.has('branch_ids[]')) {
                        formData.set('branch_ids[]', ''); // Poslať prázdne, aby backend vedel, že sme všetko odškrtli
                    }

                    const response = await fetch(routes.update, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: formData,
                    });

                    if (response.ok) {
                        window.location.reload();
                    } else if (response.status === 422) {
                        const json = await response.json();
                        Modal.showFieldErrors(modal, json.errors || {});
                        if (submitBtn) submitBtn.disabled = false;
                    } else {
                        alert('Something went wrong while saving.');
                        if (submitBtn) submitBtn.disabled = false;
                    }
                } catch (error) {
                    console.error('Update failed', error);
                    alert('Network error.');
                    if (submitBtn) submitBtn.disabled = false;
                }
            },
        });

        setTimeout(() => {
            setupCategoryRequestButton(routes.categoryRequest, csrf, service);
        }, 10);
    });
}

function setupCategoryRequestButton(requestUrl, csrf, service) {
    const button = document.getElementById('open_category_request_modal');

    if (!button || !requestUrl) return;

    button.addEventListener('click', () => {
        Modal.close(document.getElementById('dynamic-modal'));
        openCategoryRequestModal({
            requestUrl,
            csrf,
            serviceId: service.id,
            serviceName: service.name ?? '',
            businessId: service.business_id ?? '',
        });
    });
}

function openCategoryRequestModal({ requestUrl, csrf, serviceName = '', businessId = '', serviceId = '' }) {
    Modal.showCustom({
        title: 'Request New Category',
        type: 'New Request',
        confirmText: 'Request',
        cancelText: 'Cancel',
        action: 'create',
        body: `
            <form id="categoryRequestForm">
                <div class="modal-form__group">
                    <label class="modal-form__label">Requested category</label>
                    <div class="input-wrapper">
                        <input
                            type="text"
                            name="requested_category_name"
                            class="modal-form__input"
                            maxlength="100"
                            placeholder="Category name"
                            required
                            autofocus
                        >
                    </div>
                </div>
            </form>
        `,
        onConfirm: async (modal) => {
            const input = modal.querySelector('input[name="requested_category_name"]');
            const confirmButton = modal.querySelector('.btn-confirm');
            const categoryName = input?.value.trim() ?? '';

            if (!categoryName) {
                input?.classList.add('input-error');
                input?.focus();
                return;
            }

            input.classList.remove('input-error');
            if (confirmButton) confirmButton.disabled = true;

            const formData = new FormData();
            formData.append('_token', csrf);
            formData.append('requested_category_name', categoryName);
            formData.append('service_id', serviceId);
            formData.append('service_name', serviceName);
            formData.append('business_id', businessId);

            try {
                const response = await fetch(requestUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: formData,
                });

                if (!response.ok) {
                    throw new Error('Category request failed.');
                }

                queuePageAlert('success', 'Category request was sent to admin.');
                window.location.reload();
            } catch (error) {
                console.error(error);
                if (confirmButton) confirmButton.disabled = false;
                alert('Request failed. Please try again.');
            }
        },
    });
}

function queuePageAlert(type, message) {
    sessionStorage.setItem('bexora_page_alert', JSON.stringify({ type, message }));
}

function consumeQueuedPageAlert() {
    const rawAlert = sessionStorage.getItem('bexora_page_alert');
    if (!rawAlert) return;

    sessionStorage.removeItem('bexora_page_alert');

    try {
        const { type = 'success', message = '' } = JSON.parse(rawAlert);
        if (!message) return;

        document.querySelector('.alerts-wrapper')?.remove();

        const wrapper = document.createElement('div');
        wrapper.className = 'alerts-wrapper';

        const alert = document.createElement('div');
        alert.className = `alert alert--${type}`;
        alert.innerHTML = `
            <i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-xmark'}"></i>
            <span>${escapeHtml(message)}</span>
        `;

        wrapper.appendChild(alert);

        const main = document.querySelector('main.main');
        if (main) {
            main.before(wrapper);
        } else {
            document.body.prepend(wrapper);
        }
    } catch (error) {
        console.error('Failed to show queued alert.', error);
    }
}

function renderBranchCheckboxes(branches, selectedBranches) {
    const selectedIds = new Set(selectedBranches.map((branch) => Number(branch.id)));

    if (!branches.length) {
        return `<p style="font-size: 12px; color: #bbb;">No branches found for this business.</p>`;
    }

    return branches.map((branch) => `
        <label class="checkbox-item" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
            <input type="checkbox" name="branch_ids[]" value="${branch.id}"
                ${selectedIds.has(Number(branch.id)) ? 'checked' : ''}>
            <span style="font-size: 13px;">
                ${escapeHtml(branch.name)} ${branch.city ? `<small style="color:#aaa;">(${escapeHtml(branch.city)})</small>` : ''}
            </span>
        </label>
    `).join('');
}

function renderCategoryOptions(categories, selectedId = null) {
    const selectedValue = selectedId ? String(selectedId) : '';

    return [
        '<option value="">No category</option>',
        ...categories.map((category) => {
            const value = String(category.id);
            const selected = value === selectedValue ? ' selected' : '';
            return `<option value="${escapeAttribute(value)}"${selected}>${escapeHtml(category.name)}</option>`;
        }),
    ].join('');
}

// Pomocné funkcie na escape dát
function escapeHtml(value) {
    return String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;');
}
function escapeAttribute(value) {
    return escapeHtml(value).replaceAll('"', '&quot;');
}
