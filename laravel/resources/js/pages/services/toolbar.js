import { Toolbar } from "../../components/toolbar/Toolbar.js";
import { openSidebar, closeSidebar } from "../../chatbot/main.js";
import { BEXI_SIDEBAR_KEY } from "../../config/storageKeys.js";
import { initBexiToggle } from "../../components/toolbar/toolBarHelpers.js";
import { Toast } from "../../components/displays/toast.js";
import { apiFetch } from "../../utils/apiFetch.js";

export function initToolbar() {
    const config = window.BE_DATA?.toolbar || {};
    const actions = { left: "", center: "", right: "" };

    const tplConnections = document.getElementById("tpl-connections");

    if (tplConnections) {
        const templateElement =
            tplConnections.tagName === "TEMPLATE"
                ? tplConnections
                : tplConnections.querySelector("template");

        if (templateElement) {
            const tempDiv = document.createElement("div");
            tempDiv.appendChild(templateElement.content.cloneNode(true));

            actions.left += `
                <div class="toolbar__status-filters" id="toolbarConnectionsBtn">
                    Connections <i class="fa-solid fa-chevron-down"></i>
                    <div class="toolbar__status-dropdown toolbar__status-dropdown--wide" id="toolbarConnectionsDropdown" style="display:none">
                        ${tempDiv.innerHTML}
                    </div>
                </div>`;
        }
    }

    if (Array.isArray(config.centerGroups)) {
        actions.center = config.centerGroups
            .map((group) => {
                const buttonsHtml = group.actions
                    .map((action) => {
                        const dataAttr = action.serviceData
                            ? `data-service='${JSON.stringify(action.serviceData)}'`
                            : "";
                        const assetAttr = action.assetData
                            ? `data-asset='${JSON.stringify(action.assetData)}'`
                            : "";

                        const btnHtml = `
                    <button type="${action.isForm ? "submit" : "button"}"
                        class="toolbar__action-button ${action.class || ""}"
                        ${action.modal ? `data-modal-target="${action.modal}"` : ""}
                        ${action.id ? `data-id="${action.id}"` : ""}
                        ${dataAttr} ${assetAttr}>
                        <i class="fa-solid ${action.icon}"></i> ${action.label}
                    </button>`;

                        if (action.isForm) {
                            const hiddenFields = (action.hiddenFields || [])
                                .map(
                                    (f) =>
                                        `<input type="hidden" name="${f.name}" value="${f.value}">`,
                                )
                                .join("");

                            return `
                        <form action="${action.action}" method="POST" style="display:inline;"
                              data-intercept
                              data-toast-title="${action.toastTitle || ""}"
                              data-toast-type="${action.toastType || "success"}"
                              data-toast-text="${action.toastText || ""}">
                            <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                            ${hiddenFields}
                            ${btnHtml}
                        </form>`;
                        }
                        return btnHtml;
                    })
                    .join("");

                const divider = group.hasDivider
                    ? '<div class="toolbar__divider"></div>'
                    : "";
                return `${divider}<div class="toolbar__group">${buttonsHtml}</div>`;
            })
            .join("");
    }

    if (config.rightAction) {
        const isBexiOpen = localStorage.getItem(BEXI_SIDEBAR_KEY) === "true";
        actions.right = `
            <button type="button" class="toolbar__action-button toolbar__action-button--bexi ${isBexiOpen ? "is-active" : ""}" id="bexiToggleBtn">
                <i class="fa-solid ${isBexiOpen ? "fa-xmark" : "fa-message"}"></i>
                <span>${isBexiOpen ? "Close Bexi" : config.rightAction.label}</span>
            </button>`;
    }

    Toolbar.setActions(actions);

    initBexiToggle();
    initToolbarForms();

    if (tplConnections) {
        setupDropdown("toolbarConnectionsBtn", "toolbarConnectionsDropdown");
    }
}

function initToolbarForms() {
    document.addEventListener("submit", async (e) => {
        const form = e.target.closest("form[data-intercept]");
        if (!form) return;

        e.preventDefault();

        const toastTitle = form.dataset.toastTitle || "Done";
        const toastType = form.dataset.toastType || "success";
        const toastText = form.dataset.toastText || "";

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
                    type: toastType,
                    title: toastTitle,
                    message: response?.message ?? toastText,
                }),
            );
            window.location.reload();
        } catch (err) {
            Toast.error("Action failed", err.message);
            if (btn) btn.disabled = false;
        }
    });
}

function setupDropdown(btnId, dropdownId) {
    const btn = document.getElementById(btnId);
    const dropdown = document.getElementById(dropdownId);
    if (!btn || !dropdown) return;

    btn.addEventListener("click", (e) => {
        e.stopPropagation();
        const isVisible = dropdown.style.display === "block";
        document.querySelectorAll(".toolbar__status-dropdown").forEach((d) => {
            if (d !== dropdown) d.style.display = "none";
        });
        dropdown.style.display = isVisible ? "none" : "block";
    });

    document.addEventListener("click", (e) => {
        if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = "none";
        }
    });

    dropdown.addEventListener("click", (e) => e.stopPropagation());
}
