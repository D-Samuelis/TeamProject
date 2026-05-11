import { Toolbar } from "../../components/toolbar/Toolbar.js";
<<<<<<< HEAD
import { openSidebar, closeSidebar } from "../../chatbot/main.js";
import { BEXI_SIDEBAR_KEY } from "../../config/storageKeys.js";
import { apiFetch } from "../../utils/apiFetch.js";
=======
import { initBusinessStatusFilters } from "./statusFilters.js";
import {
    buildBexiButtonHtml,
    buildStatusDropdownHtml,
    renderCenterGroupsHtml,
    initBexiToggle,
    initStatusDropdown,
    initToolbarForms,
} from "../../components/toolbar/toolbarHelpers.js";
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf

export function initToolbar() {
    const params = new URLSearchParams(window.location.search);
    const branchId = params.get("branch");

    if (branchId) {
        const item = document.querySelector(
            `.branch-filter-item[data-branch-id="${branchId}"]`,
        );
        if (item) item.classList.add("is-active");
    }

    renderToolbar();
    initToolbarForms();

<<<<<<< HEAD
    const sidebar = document.querySelector(".business__sidebar");
    if (sidebar) {
        sidebar.addEventListener("click", (e) => {
            const item = e.target.closest(".branch-filter-item");
            if (!item) return;
=======
    // Re-render whenever the user clicks a branch in the sidebar
    const sidebar = document.querySelector(".business__sidebar");
    if (sidebar) {
        sidebar.addEventListener("click", (e) => {
            if (!e.target.closest(".branch-filter-item")) return;
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf

            document
                .querySelectorAll(".branch-filter-item")
                .forEach((el) => el.classList.remove("is-active"));
<<<<<<< HEAD
            item.classList.add("is-active");
=======
            e.target.closest(".branch-filter-item").classList.add("is-active");
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf

            renderToolbar();
        });
    }
}

// ─── Render ───────────────────────────────────────────────────────────────────

function renderToolbar() {
    const config = window.BE_DATA?.toolbar || {};
    const actions = { left: "", center: "", right: "" };
<<<<<<< HEAD

    const activeEl =
        document.querySelector(".branch-filter-item.is-active") ||
        document.querySelector(".branch-filter-item.active");

    const isRealBranch = activeEl && activeEl.dataset.filter !== "all";

    let branchData = {};

    if (activeEl) {
        const rawBranch = activeEl.dataset.branch
            ? JSON.parse(activeEl.dataset.branch)
            : {};
        const roleText =
            activeEl.querySelector(".member-role")?.textContent ?? "";

        branchData = {
            ...rawBranch,
            id: activeEl.dataset.branchId,
            name:
                activeEl.querySelector(".member-name")?.textContent.trim() ||
                rawBranch.name ||
                "Branch",

            // Always trust visible DOM state
            is_active: /\bActive\b/.test(roleText) ? 1 : 0,

            // Check trashed state from class
            trashed: activeEl.classList.contains("team-member-item--trashed"),
        };
    }

    const isArchived = branchData.trashed;
    const isActive = Number(branchData.is_active) === 1;
    const nextActive = isActive ? 0 : 1;

    let branchActions = [];

    if (isRealBranch) {
        try {
            let branchData = {};

            if (activeEl.dataset.branch) {
                branchData = JSON.parse(activeEl.dataset.branch);
            } else {
                branchData = {
                    id: activeEl.dataset.branchId,
                    name:
                        activeEl
                            .querySelector(".member-name")
                            ?.textContent.trim() || "Branch",
                    is_active: activeEl
                        .querySelector(".member-role")
                        ?.textContent.includes("Active")
                        ? 1
                        : 0,
                };
            }

            branchActions = [
                {
                    label: isArchived
                        ? "Status: Archived"
                        : `Status: ${isActive ? "Active" : "Inactive"}`,

                    icon: isArchived
                        ? "fa-box-archive text-gray"
                        : isActive
                          ? "fa-circle text-green"
                          : "fa-circle text-yellow",

                    isForm: !isArchived,

                    ...(isArchived
                        ? {
                              disabled: true,
                              class: "toolbar__action-button--disabled",
                          }
                        : {
                              toastTitle: isActive
                                  ? "Branch deactivated"
                                  : "Branch activated",
                              toastType: isActive ? "warning" : "success",
                              toastText: isActive
                                  ? "The branch is now inactive."
                                  : "The branch is now active.",
                              action: window.BE_DATA.routes.branchUpdate.replace(
                                  ":id",
                                  branchData.id,
                              ),
                              hiddenFields: [
                                  {
                                      name: "business_id",
                                      value: window.BE_DATA.business.id,
                                  },
                                  { name: "is_active", value: nextActive },
                                  { name: "_method", value: "PUT" },
                              ],
                          }),
                },
                {
                    label: "Manage Branch",
                    icon: "fa-gear",
                    modal: "edit-branch-modal",
                    branchData,
                },
                {
                    label: isArchived ? "Restore Branch" : "Archive Branch",
                    icon: isArchived ? "fa-rotate-left" : "fa-box-archive",

                    ...(isArchived
                        ? {
                              isForm: true,
                              toastTitle: "Branch restored",
                              toastType: "success",
                              toastText: "The branch is now active again.",
                              action: window.BE_DATA.routes.branchRestore.replace(
                                  ":id",
                                  branchData.id,
                              ),
                              hiddenFields: [
                                  { name: "_method", value: "PATCH" },
                              ],
                          }
                        : {
                              class: "delete-action",
                              modal: "archive-branch-modal",
                              id: branchData.id,
                              name: branchData.name,
                              toastTitle: "Branch archived",
                              toastType: "warning",
                              toastText: "The branch has been archived.",
                          }),
                },
            ];
        } catch (e) {
            console.error("Toolbar render error:", e);
        }
    }

    let centerHtml = "";

    if (branchActions.length > 0) {
        centerHtml += `<div class="toolbar__group">${renderButtons(branchActions)}</div>`;

        if (
            Array.isArray(config.centerGroups) &&
            config.centerGroups.length > 0
        ) {
            centerHtml += `<div class="toolbar__divider"></div>`;
        }
    }

    if (Array.isArray(config.centerGroups)) {
        centerHtml += config.centerGroups
            .map((group, index) => {
                const showDivider = group.hasDivider || index > 0;
                const dividerHtml = showDivider
                    ? '<div class="toolbar__divider"></div>'
                    : "";

                return `${dividerHtml}<div class="toolbar__group">${renderButtons(group.actions)}</div>`;
            })
            .join("");
    }
    actions.center = centerHtml;

    // --- RIGHT (Bexi Chatbot) ---
    if (config.rightAction) {
        const isBexiOpen = localStorage.getItem(BEXI_SIDEBAR_KEY) === "true";
        actions.right = `
            <button type="button" class="toolbar__action-button toolbar__action-button--bexi" id="bexiToggleBtn">
                <i class="fa-solid ${isBexiOpen ? "fa-xmark" : "fa-message"}"></i>
                <span>${isBexiOpen ? "Close Bexi" : config.rightAction.label}</span>
            </button>
`;
    }

    Toolbar.setActions(actions);
}

function renderButtons(buttons) {
    return buttons
        .map((action) => {
            const btnHtml = `
                <button type="${action.isForm ? "submit" : "button"}" class="toolbar__action-button ${action.class || ""}" ${
                    action.modal ? `data-modal-target="${action.modal}"` : ""
                } ${action.id ? `data-id="${action.id}"` : ""} ${
                    action.name ? `data-name="${action.name}"` : ""
                } ${
                    action.branchData
                        ? `data-branch='${JSON.stringify(action.branchData)}'`
                        : ""
                }>
                    <i class="fa-solid ${action.icon}"></i> ${action.label}
                </button>
`;

            if (action.isForm) {
                const hiddens = (action.hiddenFields || [])
                    .map(
                        (f) =>
                            `<input type="hidden" name="${f.name}" value="${f.value}">`,
                    )
                    .join("");

                // Add data-intercept and toast attributes here
                return `
                    <form action="${action.action}" method="POST" style="display:inline;"
                        data-intercept
                        data-toast-title="${action.toastTitle || ""}"
                        data-toast-type="${action.toastType || "success"}"
                        data-toast-text="${action.toastText || ""}">
                        <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                        ${hiddens}
                        ${btnHtml}
                    </form>`;
            }
            return btnHtml;
        })
        .join("");
}

function initToolbarForms() {
    document.addEventListener("submit", async (e) => {
        const form = e.target.closest("form[data-intercept]");
        if (!form) return;

        e.preventDefault();

        const btn = form.querySelector('[type="submit"]');
        if (btn) btn.disabled = true;

        try {
            const response = await apiFetch(form.action, {
                method: "POST",
                body: new FormData(form),
            });

            sessionStorage.setItem(
                "pending_toast",
                JSON.stringify({
                    type: form.dataset.toastType || "success",
                    title: form.dataset.toastTitle || "Done",
                    message: response.message ?? form.dataset.toastText
                }),
            );

            window.location.reload();
        } catch (err) {
            console.error("Action failed", err);
            if (btn) btn.disabled = false;
        }
    });
=======

    // ── LEFT: status filter dropdown ──────────────────────────────────────
    actions.left = buildStatusDropdownHtml("tpl-business-filters");

    // ── CENTER: optional branch-specific prefix + config groups ──────────
    const branchActions = buildBranchActions();
    actions.center = renderCenterGroupsHtml(
        Array.isArray(config.centerGroups) ? config.centerGroups : [],
        branchActions.length ? branchActions : null,
    );

    // ── RIGHT: Bexi chatbot toggle ────────────────────────────────────────
    if (config.rightAction) {
        actions.right = buildBexiButtonHtml(config.rightAction.label);
    }

    Toolbar.setActions(actions);
    bindEvents(config.rightAction);
}

/**
 * Returns branch-specific toolbar actions when a real branch is selected in
 * the sidebar. Returns an empty array on the index page or when "All" is active.
 */
function buildBranchActions() {
    // Branch actions depend on business data that only exists on the show page
    if (!window.BE_DATA?.business || !window.BE_DATA?.routes?.branchUpdate) {
        return [];
    }

    const activeEl =
        document.querySelector(".branch-filter-item.is-active") ||
        document.querySelector(".branch-filter-item.active");

    if (!activeEl || activeEl.dataset.filter === "all") return [];

    try {
        let branchData = {};

        const roleText =
            activeEl.querySelector(".member-role")?.textContent ?? "";

        branchData = activeEl.dataset.branch
            ? JSON.parse(activeEl.dataset.branch)
            : {};

        branchData = {
            ...branchData,
            id: activeEl.dataset.branchId,
            name:
                activeEl.querySelector(".member-name")?.textContent.trim() ||
                branchData.name ||
                "Branch",

            // Always trust visible DOM state
            is_active: /\bActive\b/.test(roleText) ? 1 : 0,

            // Check trashed state from class
            trashed: activeEl.classList.contains("team-member-item--trashed"),
        };

        const isArchived = branchData.trashed;
        const isActive = Number(branchData.is_active) === 1;
        const nextActive = isActive ? 0 : 1;

        return [
            {
                label: isArchived
                    ? "Status: Archived"
                    : `Status: ${isActive ? "Active" : "Inactive"}`,

                icon: isArchived
                    ? "fa-box-archive text-gray"
                    : isActive
                      ? "fa-circle text-green"
                      : "fa-circle text-yellow",

                isForm: !isArchived,

                ...(isArchived
                    ? {
                          disabled: true,
                          class: "toolbar__action-button--disabled",
                      }
                    : {
                          toastTitle: isActive
                              ? "Branch deactivated"
                              : "Branch activated",
                          toastType: isActive ? "warning" : "success",
                          toastText: isActive
                              ? "The branch is now inactive."
                              : "The branch is now active.",
                          action: window.BE_DATA.routes.branchUpdate.replace(
                              ":id",
                              branchData.id,
                          ),
                          hiddenFields: [
                              {
                                  name: "business_id",
                                  value: window.BE_DATA.business.id,
                              },
                              { name: "is_active", value: nextActive },
                              { name: "_method", value: "PUT" },
                          ],
                      }),
            },
            {
                label: "Manage Branch",
                icon: "fa-gear",
                modal: "edit-branch-modal",
                branchData,
            },
            {
                label: isArchived ? "Restore Branch" : "Archive Branch",
                icon: isArchived ? "fa-rotate-left" : "fa-box-archive",

                ...(isArchived
                    ? {
                          isForm: true,
                          toastTitle: "Branch restored",
                          toastType: "success",
                          toastText: "The branch is now active again.",
                          action: window.BE_DATA.routes.branchRestore.replace(
                              ":id",
                              branchData.id,
                          ),
                          hiddenFields: [{ name: "_method", value: "PATCH" }],
                      }
                    : {
                          class: "delete-action",
                          modal: "archive-branch-modal",
                          id: branchData.id,
                          name: branchData.name,
                          toastTitle: "Branch archived",
                          toastType: "warning",
                          toastText: "The branch has been archived.",
                      }),
            },
        ];
    } catch (err) {
        console.error("Toolbar branch action render error:", err);
        return [];
    }
}

// ─── Event binding ────────────────────────────────────────────────────────────

function bindEvents(rightActionConfig) {
    initStatusDropdown((containerId) => initBusinessStatusFilters(containerId));
    initBexiToggle(rightActionConfig);
    initToolbarForms();
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf
}
