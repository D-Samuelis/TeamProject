import { Modal } from "../../../components/displays/modal.js";
import { Toast } from "../../../components/displays/toast.js";
import { apiFetch } from "../../../utils/apiFetch.js";

export function initCreateBranchModal() {
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(
            '[data-modal-target="create-branch-modal"]',
        );
        if (!btn) return;

        e.preventDefault();
        openCreateBranchModal();
    });
}

function openCreateBranchModal() {
    Modal.showCustom({
        title: "Create New Branch",
        confirmText: "Create Branch",
        action: "create",
        body: `
            <form id="createBranchForm">
                <div class="modal-form__group">
                    <label class="modal-form__label">Name</label>
                    <div class="input-wrapper">
                        <input type="text" name="name" class="modal-form__input" placeholder="Enter branch name" required autofocus>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Type</label>
                    <div class="input-wrapper">
                        <select name="type" class="modal-form__input">
                            <option value="physical">Physical</option>
                            <option value="online">Online</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label toggle-label" style="display: flex; flex-direction: row; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Active Status
                    </label>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Street Address</label>
                    <div class="input-wrapper">
                        <input type="text" name="address_line_1" class="modal-form__input" placeholder="Bajkalská 21">
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Unit / Floor / Suite (Optional)</label>
                    <div class="input-wrapper">
                        <input type="text" name="address_line_2" class="modal-form__input" placeholder="2nd floor / door number 6">
                    </div>
                </div>

                <div style="display: flex; gap: 12px;">
                    <div class="modal-form__group" style="flex: 2;">
                        <label class="modal-form__label">City</label>
                        <input type="text" name="city" class="modal-form__input">
                    </div>
                    
                    <div class="modal-form__group" style="flex: 1;">
                        <label class="modal-form__label">Postal Code</label>
                        <input type="text" name="postal_code" class="modal-form__input">
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Country</label>
                    <div class="input-wrapper">
                        <input type="text" name="country" class="modal-form__input" value="Slovakia">
                    </div>
                </div>
            </form>
        `,
        onConfirm: async (modal) => {
            const form = modal.querySelector("#createBranchForm");
            const submitBtn = modal.querySelector(
                '[data-modal-action="confirm"]',
            );

            if (submitBtn) submitBtn.disabled = true;
            Modal.clearFieldErrors(modal);

            const formData = new FormData(form);
            formData.append("_token", window.BE_DATA.csrf);
            formData.append("business_id", window.BE_DATA.business.id);

            try {
                await apiFetch(window.BE_DATA.routes.branchStore, {
                    method: "POST",
                    body: formData,
                });

                sessionStorage.setItem(
                    "pending_toast",
                    JSON.stringify({
                        type: "success",
                        title: "Branch created",
                        message: "The new branch has been added.",
                    }),
                );
                window.location.reload();
            } catch (err) {
                if (err.status === 422 && err.errors) {
                    Modal.showFieldErrors(modal, err.errors);
                } else {
                    Toast.error("Could not create branch", err.message);
                }
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        },
    });
}
