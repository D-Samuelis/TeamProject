import { Modal } from "../../../components/displays/modal.js";
import { Toast } from "../../../components/displays/toast.js";
import { apiFetch } from "../../../utils/apiFetch.js";
import { _esc } from "../../../utils/helpers.js";

export function initEditBusinessMetaDataModal() {
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(
            '[data-modal-target="edit-business-modal"]',
        );
        if (!btn) return;

        e.preventDefault();
        openEditBusinessModal();
    });
}

function openEditBusinessModal() {
    const { name, description } = window.BE_DATA.business;
    const updateUrl = window.BE_DATA.routes.update;

    Modal.showCustom({
        title: "Edit Business Info",
        confirmText: "Save Changes",
        action: "edit",
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
                            placeholder="Optional description" style="min-height: 100px; resize: vertical;">${_esc(description ?? "")}</textarea>
                    </div>
                </div>
            </form>
        `,
        onConfirm: async (modal) => {
            const form = modal.querySelector("#editBusinessForm");
            const submitBtn = modal.querySelector(
                '[data-modal-action="confirm"]',
            );

            if (submitBtn) submitBtn.disabled = true;
            Modal.clearFieldErrors(modal);

            const formData = new FormData(form);
            formData.append("_token", window.BE_DATA.csrf);
            formData.append("_method", "PUT");

            try {
                await apiFetch(updateUrl, {
                    method: "POST",
                    body: formData,
                });

                sessionStorage.setItem(
                    "pending_toast",
                    JSON.stringify({
                        type: "success",
                        title: "Business updated",
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
