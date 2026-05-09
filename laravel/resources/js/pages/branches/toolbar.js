import { Toolbar } from '../../components/toolbar/Toolbar.js';
import { openSidebar, closeSidebar } from '../../chatbot/main.js';
import { BEXI_SIDEBAR_KEY } from '../../config/storageKeys.js';

export function initToolbar() {
    renderToolbar();
}

function renderToolbar() {
    const config = window.BE_DATA?.toolbar || {};
    const actions = { left: "", center: "", right: "" };

    if (Array.isArray(config.centerGroups)) {
        actions.center = config.centerGroups
            .map((group, index) => {
                const divider =
                    index > 0 || group.hasDivider
                        ? `<div class="toolbar__divider"></div>`
                        : "";
                return `${divider}<div class="toolbar__group">${renderButtons(group.actions)}</div>`;
            })
            .join("");
    }

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
    setupEvents();
    setupBexiEvent();
}

function renderButtons(buttons) {
    return buttons
        .map((action) => {
            const btnHtml = `
            <button type="${action.isForm ? "submit" : "button"}" 
                class="toolbar__action-button ${action.class || ""}" 
                ${action.modal ? `data-modal-target="${action.modal}"` : ""}
                ${action.id ? `data-id="${action.id}"` : ""}
                ${action.name ? `data-name="${action.name}"` : ""}>
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

                return `<form class="js-toolbar-form" action="${action.action}" method="POST" style="display:inline;">
                        <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                        <input type="hidden" name="_method" value="PATCH">
                        ${hiddens}
                        ${btnHtml}
                    </form>`;
            }
            return btnHtml;
        })
        .join("");
}

function setupEvents() {
    const bexiBtn = document.getElementById('bexiToggleBtn');
    if (bexiBtn) {
        bexiBtn.onclick = (e) => {
            e.stopPropagation();
            const isOpen = bexiBtn.classList.contains("is-active");
            if (!isOpen) {
                bexiBtn.querySelector("span").textContent = "Close Bexi";
                bexiBtn.querySelector("i").className = "fa-solid fa-xmark";
                bexiBtn.classList.add("is-active");
                localStorage.setItem(BEXI_SIDEBAR_KEY, "true");
                openSidebar();
            } else {
                bexiBtn.querySelector("span").textContent =
                    window.BE_DATA.toolbar.rightAction.label;
                bexiBtn.querySelector("i").className = "fa-solid fa-message";
                bexiBtn.classList.remove("is-active");
                localStorage.setItem(BEXI_SIDEBAR_KEY, "false");
                closeSidebar();
            }
        };

        if (localStorage.getItem(BEXI_SIDEBAR_KEY) === 'true') bexiBtn.classList.add('is-active');
    }
}

function setupBexiEvent() {
    const bexiBtn = document.getElementById('bexiToggleBtn');
    if (!bexiBtn) return;

    bexiBtn.onclick = (e) => {
        e.stopPropagation();
        const isOpen = bexiBtn.classList.contains('is-active');
        
        if (!isOpen) {
            bexiBtn.querySelector('span').textContent = 'Close Bexi';
            bexiBtn.querySelector('i').className = 'fa-solid fa-xmark';
            bexiBtn.classList.add('is-active');
            localStorage.setItem(BEXI_SIDEBAR_KEY, 'true');
            openSidebar();
        } else {
            bexiBtn.querySelector('span').textContent = window.BE_DATA.toolbar.rightAction.label;
            bexiBtn.querySelector('i').className = 'fa-solid fa-message';
            bexiBtn.classList.remove('is-active');
            localStorage.setItem(BEXI_SIDEBAR_KEY, 'false');
            closeSidebar();
        }
    };

    if (localStorage.getItem(BEXI_SIDEBAR_KEY) === 'true') {
        bexiBtn.classList.add('is-active');
    }

    document.querySelectorAll(".js-toolbar-form").forEach((form) => {
        form.addEventListener("submit", async (e) => {
            e.preventDefault(); // This stops the raw JSON from appearing

            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true; // Prevent double-clicks

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: new FormData(form),
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                });

                const result = await response.json();

                if (response.ok) {
                    sessionStorage.setItem(
                        "pending_toast",
                        JSON.stringify({
                            type: "success",
                            title: "Branch Restored",
                            message:
                                result.message ||
                                "The branch has been successfully restored.",
                        }),
                    );
                    window.location.reload();
                } else {
                    alert(result.message || "Something went wrong");
                    submitBtn.disabled = false;
                }
            } catch (error) {
                console.error("Action failed", error);
                submitBtn.disabled = false;
            }
        });
    });
}
