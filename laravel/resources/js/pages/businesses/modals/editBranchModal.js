import { Modal } from "../../../components/displays/modal.js";
import { Toast } from "../../../components/displays/toast.js";
import { apiFetch } from "../../../utils/apiFetch.js";
import { _esc } from "../../../utils/helpers.js";

export function initEditBranchModal() {
    document.addEventListener("click", (e) => {
        const btn = e.target.closest('[data-modal-target="edit-branch-modal"]');
        if (!btn) return;

        e.preventDefault();

        try {
            const rawData = btn.dataset.branch || btn.dataset.branchData;
            const branch =
                typeof rawData === "string" ? JSON.parse(rawData) : rawData;

            if (!branch || !branch.id) {
                throw new Error("Missing branch ID");
            }

            openEditBranchModal(branch);
        } catch (err) {
            console.error("Error parsing branch data for edit:", err);
        }
    });
}

function openEditBranchModal(branch) {
    const updateUrl = window.BE_DATA.routes.branchUpdate.replace(
        ":id",
        branch.id,
    );

    Modal.showCustom({
        title: "Edit Branch",
        confirmText: "Save Changes",
        action: "edit",
        body: `
            <form id="editBranchForm">
                <div class="modal-form__group">
                    <label class="modal-form__label">Name</label>
                    <div class="input-wrapper">
                        <input type="text" name="name" class="modal-form__input"
                            value="${_esc(branch.name)}" placeholder="Branch name" required autofocus>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Type</label>
                    <div class="input-wrapper">
                        <select name="type" class="modal-form__input">
                            ${["physical", "online", "hybrid"]
                                .map(
                                    (t) => `
                                <option value="${t}" ${branch.type === t ? "selected" : ""}>
                                    ${t.charAt(0).toUpperCase() + t.slice(1)}
                                </option>
                            `,
                                )
                                .join("")}
                        </select>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label toggle-label" style="display: flex; flex-direction: row; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" ${branch.is_active == 1 ? "checked" : ""}>
                        Active Status
                    </label>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Street Address</label>
                    <div class="input-wrapper">
                        <input type="text" name="address_line_1" class="modal-form__input"
                            value="${_esc(branch.address_line_1 ?? "")}" placeholder="Street and number">
                    </div>
                </div>

                <div style="display: flex; gap: 12px;">
                    <div class="modal-form__group" style="flex: 2;">
                        <label class="modal-form__label">City</label>
                        <input type="text" name="city" class="modal-form__input" value="${_esc(branch.city ?? "")}">
                    </div>
                    <div class="modal-form__group" style="flex: 1;">
                        <label class="modal-form__label">Postal Code</label>
                        <input type="text" name="postal_code" class="modal-form__input" value="${_esc(branch.postal_code ?? "")}">
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Country</label>
                    <div class="input-wrapper">
                        <input type="text" name="country" class="modal-form__input" value="${_esc(branch.country ?? "Slovakia")}">
                    </div>
                </div>
            </form>
        `,
        onConfirm: async (modal) => {
            const form = modal.querySelector("#editBranchForm");
            const submitBtn = modal.querySelector(
                '[data-modal-action="confirm"]',
            );

            if (submitBtn) submitBtn.disabled = true;
            Modal.clearFieldErrors(modal);

            const formData = new FormData(form);
            formData.append("_token", window.BE_DATA.csrf);
            formData.append("_method", "PUT");
            formData.append("business_id", window.BE_DATA.business.id);

            try {
                await apiFetch(updateUrl, {
                    method: "POST",
                    body: formData,
                });

                sessionStorage.setItem(
                    "pending_toast",
                    JSON.stringify({
                        type: "success",
                        title: "Branch updated",
                        message: "Your changes have been saved.",
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
}