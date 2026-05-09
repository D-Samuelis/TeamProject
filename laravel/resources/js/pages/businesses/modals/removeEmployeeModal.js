import { Modal } from "../../../components/displays/modal.js";
import { Toast } from "../../../components/displays/toast.js";
import { apiFetch } from "../../../utils/apiFetch.js";

export function initRemoveUserModal() {
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".js-remove-user-btn");
        if (!btn) return;

        e.preventDefault();

        const { userId, userName, displayName, targetType, targetId } =
            btn.dataset;

        Modal.showCustom({
            title: "Remove Employee",
            confirmText: "Remove Access",
            action: "delete",
            body: `
                <div class="modal-confirm-content">
                    <p>Are you sure you want to unassign <strong>${userName}</strong> from <strong>${displayName}</strong>?</p>
                    <p class="text-muted small" style="margin-top: 1rem; line-height: 1.5;">
                        The user will lose access to this section immediately, but can be re-assigned later if needed.
                    </p>
                </div>
            `,
            onConfirm: async (modal) => {
                const submitBtn = modal.querySelector(
                    '[data-modal-action="confirm"]',
                );
                const url = window.BE_DATA.routes.deleteUser.replace(
                    ":id",
                    userId,
                );

                if (submitBtn) submitBtn.disabled = true;

                try {
                    await apiFetch(url, {
                        method: "POST",
                        body: JSON.stringify({
                            _method: "DELETE",
                            target_type: targetType,
                            target_id: targetId,
                        }),
                    });

                    sessionStorage.setItem(
                        "pending_toast",
                        JSON.stringify({
                            type: "success",
                            title: "Employee removed",
                            message: "Their access has been revoked.",
                        }),
                    );
                    window.location.reload();
                } catch (err) {
                    Toast.error("Remove failed", err.message);
                    if (submitBtn) submitBtn.disabled = false;
                }
            },
        });
    });
}
