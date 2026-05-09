import { Modal } from "../../../components/displays/modal.js";
import { Toast } from "../../../components/displays/toast.js";
import { apiFetch } from "../../../utils/apiFetch.js";
import { _esc } from "../../../utils/helpers.js";

export function initCreateServiceModal() {
    document.addEventListener("click", (e) => {
        const createBtn = e.target.closest(
            '[data-modal-target="create-service-modal"]',
        );
        if (!createBtn) return;

        e.preventDefault();

        const {
            csrf,
            routes,
            businesses = [],
            branches = [],
            categories = [],
        } = window.BE_DATA || {};

        Modal.showCustom({
            title: "Create New Service",
            confirmText: "Create Service",
            action: "create",
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
                <form id="modalForm">
                    <input type="hidden" name="business_id" id="business_id_input">

                    <div class="modal-form__group">
                        <label class="modal-form__label">Business</label>
                        <div class="searchable-select-wrapper" style="position:relative;">
                            <input type="text" id="business_search" class="modal-form__input" 
                                   placeholder="Search and select business..." autocomplete="off">
                            <div id="business_dropdown" class="custom-dropdown" style="display:none;">
                                ${businesses.map((b) => `<div class="dropdown-item" data-value="${b.id}">${_esc(b.name)}</div>`).join("")}
                            </div>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Name</label>
                        <div class="input-wrapper">
                            <input type="text" name="name" class="modal-form__input" placeholder="Service name" required>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Description (Optional)</label>
                        <div class="input-wrapper">
                            <textarea name="description" class="modal-form__input" placeholder="Brief description..." style="min-height:80px;"></textarea>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Category</label>
                        <div class="input-wrapper">
                            <select name="category_id" class="modal-form__input">
                                ${renderCategoryOptions(categories)}
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

                    <div class="modal-form__row" style="display: flex; gap: 15px;">
                        <div class="modal-form__group" style="flex: 1;">
                            <label class="modal-form__label">Duration (min)</label>
                            <input type="number" name="duration_minutes" min="1" class="modal-form__input" placeholder="30" required>
                        </div>
                        <div class="modal-form__group" style="flex: 1;">
                            <label class="modal-form__label">Price (€)</label>
                            <input type="number" name="price" min="0" step="0.01" class="modal-form__input" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Branches</label>
                        <div id="branch-combobox-wrapper" style="margin-top: .5rem;">
                            <p id="branch_placeholder" class="placeholder-box">
                                <i class="fa-solid fa-arrow-up"></i> Select a business first
                            </p>
                            <div class="searchable-select-wrapper" id="branch_select_wrapper" style="display:none; position:relative;">
                                <div class="combobox-multi-tags" id="branch_tags">
                                    <input type="text" id="branch_search" class="modal-form__input" 
                                           placeholder="Search branches..." autocomplete="off">
                                </div>
                                <div id="branch_dropdown" class="custom-dropdown" style="display:none;"></div>
                            </div>
                        </div>
                        <div id="branch_hidden_inputs"></div>
                    </div>

                    <div class="modal-form__group checkbox-group">
                        <label class="modal-form__checkbox-label">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span>Active and visible</span>
                        </label>
                    </div>
                </form>
            `,
            onConfirm: async (modal) => {
                const submitBtn = modal.querySelector(
                    '[data-modal-action="confirm"]',
                );
                const form = modal.querySelector("#modalForm");

                Modal.clearFieldErrors(modal);

                const businessId =
                    modal.querySelector("#business_id_input").value;
                const branchesSelected = modal.querySelectorAll(
                    'input[name="branch_ids[]"]',
                );

                if (!businessId || branchesSelected.length === 0) {
                    if (!businessId)
                        modal
                            .querySelector("#business_search")
                            .classList.add("input-error");
                    if (branchesSelected.length === 0)
                        modal
                            .querySelector("#branch_search")
                            .classList.add("input-error");
                    return;
                }

                if (submitBtn) submitBtn.disabled = true;

                const formData = new FormData(form);
                formData.append("_token", csrf);

                try {
                    await apiFetch(routes.store, {
                        method: "POST",
                        body: formData,
                    });

                    sessionStorage.setItem(
                        "pending_toast",
                        JSON.stringify({
                            type: "success",
                            title: "Service created",
                            message:
                                "The new service has been added successfully.",
                        }),
                    );
                    window.location.reload();
                } catch (err) {
                    if (err.status === 422 && err.errors) {
                        Modal.showFieldErrors(modal, err.errors);
                    } else {
                        Toast.error("Could not create service", err.message);
                    }
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                }
            },
        });

        setTimeout(() => {
            setupBusinessSelect(businesses, branches);
            setupCategoryRequestButton(routes.categoryRequest, csrf);
        }, 10);
    });
}

// ── Category request ─────────────────────────────────────────────────────────

function setupCategoryRequestButton(requestUrl, csrf) {
    const button = document.getElementById("open_category_request_modal");
    const form = document.getElementById("modalForm");

    if (!button || !form || !requestUrl) return;

    button.addEventListener("click", () => {
        const serviceName =
            form.querySelector('input[name="name"]')?.value ?? "";
        const businessId =
            form.querySelector('input[name="business_id"]')?.value ?? "";

        Modal.close(document.getElementById("dynamic-modal"));
        openCategoryRequestModal({ requestUrl, csrf, serviceName, businessId });
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
            formData.append("service_name", serviceName);
            formData.append("business_id", businessId);
            if (serviceId) formData.append("service_id", serviceId);

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

// ── Business searchable select ───────────────────────────────────────────────

function setupBusinessSelect(businesses, branches) {
    const searchInput = document.getElementById("business_search");
    const dropdown = document.getElementById("business_dropdown");
    const hiddenInput = document.getElementById("business_id_input");
    const items = dropdown.querySelectorAll(".dropdown-item");

    if (!searchInput) return;

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

        if (!searchInput.value) {
            hiddenInput.value = "";
            lockBranchSelect();
        }
    });

    items.forEach((item) => {
        item.addEventListener("click", () => {
            searchInput.value = item.textContent;
            hiddenInput.value = item.dataset.value;
            dropdown.style.display = "none";
            unlockBranchSelect(branches, parseInt(item.dataset.value));
        });
    });

    document.addEventListener("click", (e) => {
        if (!e.target.closest(".searchable-select-wrapper")) {
            dropdown.style.display = "none";
        }
    });
}

// ── Branch lock / unlock ─────────────────────────────────────────────────────

function lockBranchSelect() {
    const wrapper = document.getElementById("branch_select_wrapper");
    const placeholder = document.getElementById("branch_placeholder");

    if (wrapper) wrapper.style.display = "none";

    if (placeholder) {
        placeholder.style.display = "block";
        placeholder.innerHTML =
            '<i class="fa-solid fa-arrow-up" style="margin-right:5px;"></i> Firstly, choose a business to see available branches';
    }

    document.querySelectorAll(".combobox-tag").forEach((t) => t.remove());
    document.getElementById("branch_hidden_inputs").innerHTML = "";
}

function unlockBranchSelect(allBranches, businessId) {
    const wrapper = document.getElementById("branch_select_wrapper");
    const placeholder = document.getElementById("branch_placeholder");
    const searchInput = document.getElementById("branch_search");

    if (wrapper) wrapper.style.display = "block";
    if (searchInput) searchInput.value = "";

    document.querySelectorAll(".combobox-tag").forEach((t) => t.remove());
    document.getElementById("branch_hidden_inputs").innerHTML = "";

    const available = allBranches.filter((b) => b.business_id === businessId);

    if (placeholder) {
        placeholder.style.display = available.length === 0 ? "block" : "none";
        placeholder.innerHTML =
            '<i class="fa-solid fa-circle-info" style="margin-right:5px;"></i> No branches available for this business';
    }

    if (available.length > 0) {
        setupBranchMultiSelect(available);
    }
}

// ── Branch multi searchable select ──────────────────────────────────────────

function setupBranchMultiSelect(available) {
    const searchInput = document.getElementById("branch_search");
    const dropdown = document.getElementById("branch_dropdown");
    const tagsEl = document.getElementById("branch_tags");
    const hiddenInputs = document.getElementById("branch_hidden_inputs");
    const placeholder = document.getElementById("branch_placeholder");

    if (!searchInput) return;

    let selected = [];

    function renderDropdown(filter = "") {
        const q = filter.toLowerCase();
        const filtered = available.filter(
            (b) =>
                b.name.toLowerCase().includes(q) &&
                !selected.find((s) => s.id === b.id),
        );

        dropdown.innerHTML = filtered.length
            ? filtered
                  .map(
                      (b) => `
                <div class="dropdown-item" data-value="${b.id}">
                    ${_esc(b.name)}${b.city ? ` <span style="font-size:12px;color:#aaa;">(${_esc(b.city)})</span>` : ""}
                </div>`,
                  )
                  .join("")
            : `<div class="dropdown-item" style="color:#999;font-style:italic;pointer-events:none;">No results</div>`;
    }

    function addTag(branch) {
        selected.push(branch);

        const hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.name = "branch_ids[]";
        hidden.value = branch.id;
        hidden.id = `branch_hidden_${branch.id}`;
        hiddenInputs.appendChild(hidden);

        const tag = document.createElement("div");
        tag.className = "combobox-tag";
        tag.dataset.id = branch.id;
        tag.innerHTML = `${_esc(branch.name)}<button type="button" data-remove="${branch.id}">&times;</button>`;
        tagsEl.appendChild(tag);

        if (placeholder) placeholder.style.display = "none";
    }

    function removeTag(id) {
        selected = selected.filter((s) => s.id !== id);
        document.querySelector(`.combobox-tag[data-id="${id}"]`)?.remove();
        document.getElementById(`branch_hidden_${id}`)?.remove();
        renderDropdown(searchInput.value);
    }

    searchInput.addEventListener("focus", () => {
        renderDropdown(searchInput.value);
        dropdown.style.display = "block";
    });

    searchInput.addEventListener("input", () => {
        renderDropdown(searchInput.value);
        dropdown.style.display = "block";
    });

    dropdown.addEventListener("mousedown", (e) => {
        const item = e.target.closest(".dropdown-item");
        if (!item || item.style.pointerEvents === "none") return;

        const id = parseInt(item.dataset.value);
        const branch = available.find((b) => b.id === id);
        if (branch) {
            addTag(branch);
            searchInput.value = "";
            dropdown.style.display = "none";
            renderDropdown("");
        }
    });

    tagsEl.addEventListener("click", (e) => {
        const btn = e.target.closest("[data-remove]");
        if (btn) removeTag(parseInt(btn.dataset.remove));
    });

    document.addEventListener("click", (e) => {
        if (!e.target.closest("#branch-combobox-wrapper")) {
            dropdown.style.display = "none";
        }
    });
}
