import { Modal } from "../../../components/displays/modal.js";
import { getFutureDateData } from "../../../utils/date.js";
import { Toast } from "../../../components/displays/toast.js";
import { apiFetch } from "../../../utils/apiFetch.js";

export function initArchiveServiceModal() {
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(
            '[data-modal-target="archive-service-modal"]',
        );
        if (!btn) return;

        e.preventDefault();

        const serviceData = window.BE_DATA.service || {};

        const id = btn.dataset.id || serviceData.id;
        const name = btn.dataset.name || serviceData.name || "this service";

        const businessId =
            btn.dataset.business_id ||
            serviceData.business_id ||
            serviceData.business?.id;

        if (!id) {
            console.error("No service ID found for archiving");
            return;
        }

        Modal.showCustom({
            title: "Archive Service",
            confirmText: "Archive Service",
            action: "warning",
            body: `
                <div class="modal-confirm-content">
                    <p>Are you sure you want to archive service <strong>${name}</strong>?</p>
                    
                    <div class="archive-expiry-wrapper text-muted small" style="margin-top: 1.5rem;">
                        <span>This service will be marked as archived and automatically deleted in</span>

                        <select id="archive-expiry-select-service" class="form-select-inline" style="margin: 0 4px; padding: 2px 5px; border-radius: 4px; border: 1px solid #ccc;">
                            <option value="${getFutureDateData(1).timestamp}">1 Day [${getFutureDateData(1).display}]</option>
                            <option value="${getFutureDateData(7).timestamp}" selected>1 Week [${getFutureDateData(7).display}]</option>
                            <option value="${getFutureDateData(30).timestamp}">1 Month [${getFutureDateData(30).display}]</option>
                        </select> 

                        <span>if not restored in time.</span>
                    </div>
                </div>
            `,
            onConfirm: async (modal) => {
                const submitBtn = modal.querySelector(
                    '[data-modal-action="confirm"]',
                );
                const expiryTimestamp = modal.querySelector(
                    "#archive-expiry-select-service",
                ).value;
                const url = window.BE_DATA.routes.delete.replace(":id", id);

                if (submitBtn) submitBtn.disabled = true;

                try {
                    await apiFetch(url, {
                        method: "POST",
                        body: JSON.stringify({
                            _method: "DELETE",
                            delete_at_timestamp: expiryTimestamp,
                            business_id: businessId,
                        }),
                    });

                    sessionStorage.setItem(
                        "pending_toast",
                        JSON.stringify({
                            type: "warning",
                            title: "Service archived",
                            message:
                                "It can be restored before the expiry date.",
                        }),
                    );
                    window.location.reload();
                } catch (err) {
                    Toast.error("Archive failed", err.message);
                    if (submitBtn) submitBtn.disabled = false;
                }
            },
        });
    });
}
