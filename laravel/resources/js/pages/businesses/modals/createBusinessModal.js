import { Modal } from "../../../components/displays/modal.js";
import { Toast } from "../../../components/displays/toast.js";
import { apiFetch } from "../../../utils/apiFetch.js";

export function initCreateBusinessModal() {
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(
            '[data-modal-target="create-business-modal"]',
        );
        if (!btn) return;

        e.preventDefault();
        openCreateBusinessModal();
    });
}

function openCreateBusinessModal() {
    Modal.showCustom({
        title: "Create New Business",
        confirmText: "Create Business",
        action: "create",
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
                        <textarea name="description" class="modal-form__input" placeholder="Optional description..." style="min-height: 100px; resize: vertical;"></textarea>
                    </div>
                </div>
            </form>
        `,
        onConfirm: async (modal) => {
            const form = modal.querySelector("#createBusinessForm");
            const submitBtn = modal.querySelector(
                '[data-modal-action="confirm"]',
            );

            if (submitBtn) submitBtn.disabled = true;
            Modal.clearFieldErrors(modal);

            const formData = new FormData(form);
            formData.append("_token", window.BE_DATA.csrf);

            try {
                await apiFetch(window.BE_DATA.routes.store, {
                    method: "POST",
                    body: formData,
                });

                sessionStorage.setItem(
                    "pending_toast",
                    JSON.stringify({
                        type: "success",
                        title: "Business created",
                        message: "Your new business is ready.",
                    }),
                );
                window.location.reload();
            } catch (err) {
                if (err.status === 422 && err.errors) {
                    Modal.showFieldErrors(modal, err.errors);
                } else {
                    Toast.error("Could not create business", err.message);
                }
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        },
    });
}
