import { Toolbar } from "../../components/toolbar/Toolbar.js";
import { initBusinessStatusFilters } from "./statusFilters.js";
import {
    buildBexiButtonHtml,
    buildStatusDropdownHtml,
    renderCenterGroupsHtml,
    initBexiToggle,
    initStatusDropdown,
    initToolbarForms,
} from "../../components/toolbar/toolbarHelpers.js";

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

    // Re-render whenever the user clicks a branch in the sidebar
    const sidebar = document.querySelector(".business__sidebar");
    if (sidebar) {
        sidebar.addEventListener("click", (e) => {
            if (!e.target.closest(".branch-filter-item")) return;

            document
                .querySelectorAll(".branch-filter-item")
                .forEach((el) => el.classList.remove("is-active"));
            e.target.closest(".branch-filter-item").classList.add("is-active");

            renderToolbar();
        });
    }
}

// ─── Render ───────────────────────────────────────────────────────────────────

function renderToolbar() {
    const config = window.BE_DATA?.toolbar || {};
    const actions = { left: "", center: "", right: "" };

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
}
