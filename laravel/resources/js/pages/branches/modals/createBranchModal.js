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
        openCreateBranchModal(window.BE_DATA?.businesses || []);
    });
}

function openCreateBranchModal(businesses) {
    Modal.showCustom({
        title: "Create New Branch",
        confirmText: "Create Branch",
        action: "create",
        body: `
            <form id="createBranchForm">
                <input type="hidden" name="business_id" id="business_id_input">

                <div class="modal-form__group">
                    <label class="modal-form__label">Business</label>
                    <div class="searchable-select-wrapper" style="position:relative;">
                        <input type="text" id="business_search" class="modal-form__input"
                               placeholder="Search and select business..." autocomplete="off">
                        <div id="business_dropdown" class="custom-dropdown" style="display:none;">
                            ${businesses.map((b) => `<div class="dropdown-item" data-value="${b.id}">${b.name}</div>`).join("")}
                        </div>
                    </div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Name</label>
                    <div class="input-wrapper">
                        <input type="text" name="name" class="modal-form__input"
                               placeholder="Enter branch name" required autofocus>
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
                    <label class="modal-form__label toggle-label"
                           style="display:flex;flex-direction:row;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Active Status
                    </label>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Street Address</label>
                    <div class="input-wrapper">
                        <input type="text" name="address_line_1" class="modal-form__input"
                               placeholder="Bajkalská 21">
                    </div>
                </div>

                <div style="display:flex;gap:12px;">
                    <div class="modal-form__group" style="flex:2;">
                        <label class="modal-form__label">City</label>
                        <input type="text" name="city" class="modal-form__input">
                    </div>
                    <div class="modal-form__group" style="flex:1;">
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
            const businessId = modal.querySelector("#business_id_input").value;

            Modal.clearFieldErrors(modal);

            if (!businessId) {
                modal
                    .querySelector("#business_search")
                    .classList.add("input-error");
                return;
            }

            if (submitBtn) submitBtn.disabled = true;

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
                        title: "Branch created",
                        message: "The new branch has been added successfully.",
                    }),
                );
                window.location.reload();
            } catch (err) {
                if (err.status === 422 && err.errors) {
                    Modal.showFieldErrors(modal, err.errors);
                } else {
                    Toast.error("Create failed", err.message);
                }
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        },
    });

    // Set up the searchable business picker after the modal is in the DOM
    setTimeout(setupBusinessSearch, 10);
}

function setupBusinessSearch() {
    const searchInput = document.getElementById("business_search");
    const dropdown = document.getElementById("business_dropdown");
    const hiddenInput = document.getElementById("business_id_input");

    if (!searchInput || !dropdown) return;

    const items = dropdown.querySelectorAll(".dropdown-item");

    searchInput.addEventListener("focus", () => {
        dropdown.style.display = "block";
        items.forEach((item) => (item.style.display = "block"));
    });

    searchInput.addEventListener("input", () => {
        const filter = searchInput.value.toLowerCase();
        dropdown.style.display = "block";
        items.forEach((item) => {
            item.style.display = item.textContent.toLowerCase().includes(filter)
                ? "block"
                : "none";
        });
        if (!searchInput.value) hiddenInput.value = "";
    });

    items.forEach((item) => {
        item.addEventListener("click", () => {
            searchInput.value = item.textContent.trim();
            hiddenInput.value = item.dataset.value;
            dropdown.style.display = "none";
            searchInput.classList.remove("input-error");
        });
    });

    document.addEventListener("click", (e) => {
        if (!e.target.closest(".searchable-select-wrapper")) {
            dropdown.style.display = "none";
        }
    });
}
