import { Toast } from "../displays/toast.js";
import { openSidebar, closeSidebar } from "../../chatbot/main.js";
import { BEXI_SIDEBAR_KEY } from "../../config/storageKeys.js";

// ─── HTML builders ────────────────────────────────────────────────────────────

/**
 * Returns the right-section HTML for the Bexi sidebar toggle button.
 * Reads the current open-state from localStorage so the button is rendered
 * in the correct state on first paint.
 */
export function buildBexiButtonHtml(label) {
    const isOpen = localStorage.getItem(BEXI_SIDEBAR_KEY) === "true";
    return `
        <button type="button" class="toolbar__action-button toolbar__action-button--bexi" id="bexiToggleBtn">
            <i class="fa-solid ${isOpen ? "fa-xmark" : "fa-message"}"></i>
            <span>${isOpen ? "Close Bexi" : label}</span>
        </button>`;
}

/**
 * Returns the left-section HTML for the status filter dropdown.
 * @param {string} tplId – id of the <div> that holds the filter template HTML
 */
export function buildStatusDropdownHtml(tplId) {
    const tpl = document.getElementById(tplId);
    if (!tpl) return "";
    return `
        <div class="toolbar__status-filters" id="toolbarStatusBtn">
            Status <i class="fa-solid fa-chevron-down"></i>
            <div class="toolbar__status-dropdown" id="toolbarStatusDropdown" style="display:none">
                ${tpl.innerHTML}
            </div>
        </div>`;
}

/**
 * Renders an array of action descriptors into toolbar button HTML.
 * Form actions carry toast metadata as data attributes so the single
 * submit handler (initToolbarForms) never needs to know which action fired.
 */
export function renderToolbarButtons(buttons) {
    return buttons
        .map((action) => {
            const btnHtml = `
                <button type="${action.isForm ? "submit" : "button"}"
                    class="toolbar__action-button ${action.class || ""}"
                    ${action.modal ? `data-modal-target="${action.modal}"` : ""}
                    ${action.id ? `data-id="${action.id}"` : ""}
                    ${action.name ? `data-name="${action.name}"` : ""}
                    ${action.branchData ? `data-branch='${JSON.stringify(action.branchData)}'` : ""}>
                    <i class="fa-solid ${action.icon}"></i> ${action.label}
                </button>`;

            if (!action.isForm) return btnHtml;

            const hiddens = (action.hiddenFields || [])
                .map(
                    (f) =>
                        `<input type="hidden" name="${f.name}" value="${f.value}">`,
                )
                .join("");

            return `
                <form
                    action="${action.action}"
                    method="POST"
                    class="js-toolbar-form"
                    data-toast-title="${action.toastTitle || "Action completed"}"
                    data-toast-type="${action.toastType || "success"}"
                    style="display:inline;">
                    <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                    ${hiddens}
                    ${btnHtml}
                </form>`;
        })
        .join("");
}

/**
 * Renders the full center section from an array of group configs.
 *
 * @param {Array}       centerGroups  – config.centerGroups from BE_DATA.toolbar
 * @param {Array|null}  prefixActions – optional leading action group (e.g. branch actions
 *                                      on the business show page). A divider is inserted
 *                                      automatically between the prefix and the first group.
 */
export function renderCenterGroupsHtml(
    centerGroups = [],
    prefixActions = null,
) {
    let html = "";

    if (prefixActions?.length) {
        html += `<div class="toolbar__group">${renderToolbarButtons(prefixActions)}</div>`;
        if (centerGroups.length) html += `<div class="toolbar__divider"></div>`;
    }

    html += centerGroups
        .map((group, index) => {
            // Divider before every group that isn't the very first,
            // OR before any group that explicitly requests one (hasDivider).
            const addDivider = group.hasDivider || index > 0;
            return `${addDivider ? '<div class="toolbar__divider"></div>' : ""}
                    <div class="toolbar__group">${renderToolbarButtons(group.actions)}</div>`;
        })
        .join("");

    return html;
}

// ─── Event binders ────────────────────────────────────────────────────────────

/**
 * Wires up the Bexi sidebar toggle button (#bexiToggleBtn).
 * Call after the toolbar HTML has been injected into the DOM.
 *
 * @param {{ label: string }|null} rightActionConfig – toolbar.rightAction from BE_DATA
 */
export function initBexiToggle(rightActionConfig) {
    const bexiBtn = document.getElementById("bexiToggleBtn");
    if (!bexiBtn) return;

    // Sync CSS state on load (HTML is already correct, but the class drives styling)
    if (localStorage.getItem(BEXI_SIDEBAR_KEY) === "true") {
        bexiBtn.classList.add("is-active");
    }

    bexiBtn.onclick = (e) => {
        e.stopPropagation();
        const willOpen = !bexiBtn.classList.contains("is-active");

        if (willOpen) {
            bexiBtn.querySelector("span").textContent = "Close Bexi";
            bexiBtn.querySelector("i").className = "fa-solid fa-xmark";
            bexiBtn.classList.add("is-active");
            localStorage.setItem(BEXI_SIDEBAR_KEY, "true");
            openSidebar();
        } else {
            bexiBtn.querySelector("span").textContent =
                rightActionConfig?.label ?? "Ask Bexi";
            bexiBtn.querySelector("i").className = "fa-solid fa-message";
            bexiBtn.classList.remove("is-active");
            localStorage.setItem(BEXI_SIDEBAR_KEY, "false");
            closeSidebar();
        }
    };
}

/**
 * Wires up the status-filter dropdown (#toolbarStatusBtn / #toolbarStatusDropdown).
 * The supplied function is called with the dropdown's id so it can populate the list.
 *
 * @param {(containerId: string) => void} initFiltersFn – page-specific filter initialiser
 */
export function initStatusDropdown(initFiltersFn) {
    const statusBtn = document.getElementById("toolbarStatusBtn");
    const dropdown = document.getElementById("toolbarStatusDropdown");
    if (!statusBtn || !dropdown) return;

    statusBtn.onclick = (e) => {
        e.stopPropagation();
        const isVisible = dropdown.style.display === "block";
        dropdown.style.display = isVisible ? "none" : "block";
        if (!isVisible) initFiltersFn("toolbarStatusDropdown");
    };

    // Close on any outside click (registered once; re-added by each renderToolbar call)
    document.addEventListener(
        "click",
        (e) => {
            if (!statusBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = "none";
            }
        },
        { once: true },
    );
}

/**
 * Attaches submit handlers to every `.js-toolbar-form` in the document.
 *
 * Toast title/type are read from `data-toast-title` / `data-toast-type` on the
 * <form> element (set by renderToolbarButtons), so this handler is completely
 * generic and never needs to be updated when new form actions are added.
 *
 * Safe JSON parsing: a 500 can return an HTML error page instead of JSON, so
 * we wrap response.json() in its own try/catch and fall back to an empty object.
 */
export function initToolbarForms() {
    document.querySelectorAll(".js-toolbar-form").forEach((form) => {
        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const toastTitle = form.dataset.toastTitle || "Action completed";
            const toastType = form.dataset.toastType || "success";

            if (submitBtn) submitBtn.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: new FormData(form),
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                });

                let result = {};
                try {
                    result = await response.json();
                } catch {
                    /* non-JSON body */
                }

                if (response.ok) {
                    sessionStorage.setItem(
                        "pending_toast",
                        JSON.stringify({
                            type: toastType,
                            title: toastTitle,
                            message: result.message || "Done.",
                        }),
                    );
                    window.location.reload();
                } else {
                    Toast.error(
                        "Action failed",
                        result.message ||
                            "Something went wrong. Please try again.",
                    );
                    if (submitBtn) submitBtn.disabled = false;
                }
            } catch (err) {
                console.error("Toolbar form submission failed:", err);
                Toast.error(
                    "Action failed",
                    "An unexpected error occurred. Please try again.",
                );
                if (submitBtn) submitBtn.disabled = false;
            }
        });
    });
}
