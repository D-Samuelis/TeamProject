import { Modal } from "../../../components/displays/modal.js";
import { Toast } from "../../../components/displays/toast.js";
import { apiFetch } from "../../../utils/apiFetch.js";

export function initAssignEmployeeModal() {
    document.addEventListener("click", (e) => {
        const btn = e.target.closest('[data-modal-target="assign-user-modal"]');
        if (!btn) return;

        e.preventDefault();

        const activeBranchEl = document.querySelector(
            ".branch-filter-item.is-active",
        );
        const activeBranchId = activeBranchEl
            ? activeBranchEl.dataset.branchId
            : null;

        openAssignEmployeeModal(activeBranchId);
    });
}

function openAssignEmployeeModal(activeBranchId = null) {
    const business = window.BE_DATA.business;

    Modal.showCustom({
        title: "Assign Employee",
        confirmText: "Assign & Notify",
        action: "create",
        body: `
            <form id="assignEmployeeForm">
                <div class="modal-form__group">
                    <label class="modal-form__label">Assign To</label>
                    <div class="input-wrapper">
                        <select id="modal-target-selector" class="modal-form__input">
                            <option value="business" data-id="${business.id}">Entire Business</option>
                            
                            ${
                                business.branches?.length
                                    ? `
                                <optgroup label="Branches">
                                    ${business.branches
                                        .map((b) => {
                                            const isSelected =
                                                String(b.id) ===
                                                String(activeBranchId)
                                                    ? "selected"
                                                    : "";
                                            return `<option value="branch" data-id="${b.id}" ${isSelected}>${b.name}</option>`;
                                        })
                                        .join("")}
                                </optgroup>
                            `
                                    : ""
                            }

                            ${
                                business.services?.length
                                    ? `
                                <optgroup label="Services">
                                    ${business.services
                                        .map(
                                            (s) =>
                                                `<option value="service" data-id="${s.id}">${s.name}</option>`,
                                        )
                                        .join("")}
                                </optgroup>
                            `
                                    : ""
                            }
                        </select>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Member Email</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" class="modal-form__input" placeholder="staff@example.com" required autofocus>
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
            const form = modal.querySelector("#assignEmployeeForm");
            const submitBtn = modal.querySelector(
                '[data-modal-action="confirm"]',
            );
            const selector = modal.querySelector("#modal-target-selector");
            const selectedOption = selector.options[selector.selectedIndex];

            if (submitBtn) submitBtn.disabled = true;
            Modal.clearFieldErrors(modal);

            const formData = new FormData(form);
            formData.append("_token", window.BE_DATA.csrf);
            formData.append("target_type", selector.value);
            formData.append("target_id", selectedOption.dataset.id);

            try {
                await apiFetch(window.BE_DATA.routes.assignUser, {
                    method: "POST",
                    body: formData,
                });

                sessionStorage.setItem(
                    "pending_toast",
                    JSON.stringify({
                        type: "success",
                        title: "Employee assigned",
                        message: "They will be notified by email.",
                    }),
                );
                window.location.reload();
            } catch (err) {
                if (err.status === 422 && err.errors) {
                    Modal.showFieldErrors(modal, err.errors);
                } else {
                    Toast.error("Assignment failed", err.message);
                }
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        },
    });
}
