import { Modal } from "../../../components/displays/modal.js";
import { Toast } from "../../../components/displays/toast.js";
import { apiFetch } from "../../../utils/apiFetch.js";
import { _esc } from "../../../utils/helpers.js";

export function initEditServiceModal() {
    document.addEventListener("click", (event) => {
        const trigger = event.target.closest(
            '[data-modal-target="edit-service-modal"]',
        );
        if (!trigger) return;

        event.preventDefault();

        const {
            service,
            routes,
            branches,
            categories = [],
            csrf,
        } = window.BE_DATA || {};

        if (!service || !routes?.update) {
            console.error("Missing service data or update route.");
            return;
        }

        const businessBranches = (branches || []).filter(
            (branch) =>
                Number(branch.business_id) === Number(service.business_id),
        );

        Modal.showCustom({
            title: `Manage Service: ${_esc(service.name)}`,
            confirmText: "Save Changes",
            action: "edit",
            rules: {
                name: {
                    required: {
                        value: true,
                        message: "Service name is required",
                    },
                },
                category_id: {
                    required: {
                        value: true,
                        message: "Please choose an existing category",
                    },
                },
                duration_minutes: {
                    required: { value: true, message: "Duration is required" },
                },
                price: {
                    required: { value: true, message: "Price is required" },
                },
            },
            body: `
                <form id="editServiceForm">
                    <div class="modal-form__group">
                        <label class="modal-form__label">Service Name</label>
                        <div class="input-wrapper">
                            <input type="text" name="name" class="modal-form__input" 
                                   value="${_esc(service.name || "")}" required>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Description</label>
                        <div class="input-wrapper">
                            <textarea name="description" class="modal-form__input" 
                                      style="min-height: 100px;">${_esc(service.description || "")}</textarea>
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
                                   value="${_esc(service.duration_minutes)}" required>
                        </div>
                        <div class="modal-form__group">
                            <label class="modal-form__label">Price (€)</label>
                            <input type="number" name="price" min="0" step="0.01" class="modal-form__input" 
                                   value="${_esc(service.price)}" required>
                        </div>
                    </div>

                    <div class="modal-form__grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="modal-form__group">
                            <label class="modal-form__label">Location Type</label>
                            <select name="location_type" class="modal-form__input">
                                <option value="branch" ${!["online", "hybrid"].includes(service.location_type) ? "selected" : ""}>Branch</option>
                                <option value="online" ${service.location_type === "online" ? "selected" : ""}>Online</option>
                                <option value="hybrid" ${service.location_type === "hybrid" ? "selected" : ""}>Hybrid</option>
                            </select>
                        </div>
                        <div class="modal-form__group">
                            <label class="modal-form__label">Cancel Period</label>
                            <input type="text" name="cancellation_period" class="modal-form__input" 
                                   value="${_esc(service.cancellation_period || "")}" placeholder="e.g. 24h">
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
                            <input type="checkbox" name="is_active" value="1" ${service.is_active ? "checked" : ""}>
                            <span class="checkbox-item__text"><strong>Active</strong> - Service is visible</span>
                        </label>

                        <label class="checkbox-item toggle-style">
                            <input type="checkbox" name="requires_manual_acceptance" value="1" ${service.requires_manual_acceptance ? "checked" : ""}>
                            <span class="checkbox-item__text"><strong>Manual acceptance</strong> - Approval needed</span>
                        </label>
                    </div>
                </form>
            `,
            onConfirm: async (modal) => {
                const form = modal.querySelector("#editServiceForm");
                const submitBtn = modal.querySelector(
                    '[data-modal-action="confirm"]',
                );

                Modal.clearFieldErrors(modal);
                if (submitBtn) submitBtn.disabled = true;

                const formData = new FormData(form);
                formData.append("_token", csrf);
                formData.append("_method", "PUT");
                formData.append(
                    "business_id",
                    service.business_id || service.business?.id || "",
                );

                if (!formData.has("is_active")) formData.set("is_active", "0");
                if (!formData.has("requires_manual_acceptance"))
                    formData.set("requires_manual_acceptance", "0");
                if (!formData.has("branch_ids[]"))
                    formData.set("branch_ids[]", "");

                try {
                    await apiFetch(routes.update, {
                        method: "POST",
                        body: formData,
                    });

                    sessionStorage.setItem(
                        "pending_toast",
                        JSON.stringify({
                            type: "success",
                            title: "Service updated",
                            message: "The service details have been saved.",
                        }),
                    );
                    window.location.reload();
                } catch (err) {
                    if (err.status === 422 && err.errors) {
                        Modal.showFieldErrors(modal, err.errors);
                    } else {
                        Toast.error("Update failed", err.message);
                    }
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                }
            },
        });

        setTimeout(() => {
            setupCategoryRequestButton(routes.categoryRequest, csrf, service);
        }, 10);
    });
}

// ── Category request ─────────────────────────────────────────────────────────

function setupCategoryRequestButton(requestUrl, csrf, service) {
    const button = document.getElementById("open_category_request_modal");

    if (!button || !requestUrl) return;

    button.addEventListener("click", () => {
        Modal.close(document.getElementById("dynamic-modal"));
        openCategoryRequestModal({
            requestUrl,
            csrf,
            serviceId: service.id,
            serviceName: service.name ?? "",
            businessId: service.business_id ?? "",
        });
    });
}

function openCategoryRequestModal({
    requestUrl,
    csrf,
    serviceName = "",
    businessId = "",
    serviceId = "",
}) {
    Modal.showCustom({
        title: "Request New Category",
        type: "New Request",
        confirmText: "Request",
        cancelText: "Cancel",
        action: "create",
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
            const input = modal.querySelector(
                'input[name="requested_category_name"]',
            );
            const confirmButton = modal.querySelector(".btn-confirm");
            const categoryName = input?.value.trim() ?? "";

            if (!categoryName) {
                input?.classList.add("input-error");
                input?.focus();
                return;
            }

            input.classList.remove("input-error");
            if (confirmButton) confirmButton.disabled = true;

            const formData = new FormData();
            formData.append("_token", csrf);
            formData.append("requested_category_name", categoryName);
            formData.append("service_id", serviceId);
            formData.append("service_name", serviceName);
            formData.append("business_id", businessId);

            try {
                await apiFetch(requestUrl, {
                    method: "POST",
                    body: formData,
                });

                sessionStorage.setItem(
                    "pending_toast",
                    JSON.stringify({
                        type: "success",
                        title: "Request sent",
                        message: "Category request was sent to admin.",
                    }),
                );
                window.location.reload();
            } catch (err) {
                Toast.error(
                    "Request failed",
                    "Could not submit the category request. Please try again.",
                );
            } finally {
                if (confirmButton) confirmButton.disabled = false;
            }
        },
    });
}

// ── Rendering helpers ────────────────────────────────────────────────────────

function renderBranchCheckboxes(branches, selectedBranches) {
    const selectedIds = new Set(
        selectedBranches.map((branch) => Number(branch.id)),
    );

    if (!branches.length) {
        return `<p style="font-size: 12px; color: #bbb;">No branches found for this business.</p>`;
    }

    return branches
        .map(
            (branch) => `
        <label class="checkbox-item" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
            <input type="checkbox" name="branch_ids[]" value="${branch.id}"
                ${selectedIds.has(Number(branch.id)) ? "checked" : ""}>
            <span style="font-size: 13px;">
                ${_esc(branch.name)} ${branch.city ? `<small style="color:#aaa;">(${_esc(branch.city)})</small>` : ""}
            </span>
        </label>
    `,
        )
        .join("");
}

function renderCategoryOptions(categories, selectedId = null) {
    const selectedValue = selectedId ? String(selectedId) : "";

    return [
        '<option value="">No category</option>',
        ...categories.map((category) => {
            const value = String(category.id);
            const selected = value === selectedValue ? " selected" : "";
            return `<option value="${_esc(value)}"${selected}>${_esc(category.name)}</option>`;
        }),
    ].join("");
}
