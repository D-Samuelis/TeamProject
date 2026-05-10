<<<<<<< HEAD
import { Toolbar } from '../../components/toolbar/Toolbar.js';
import { openSidebar, closeSidebar } from '../../chatbot/main.js';
import { BEXI_SIDEBAR_KEY } from '../../config/storageKeys.js';

export function initToolbar() {
    const config = window.BE_DATA?.toolbar || {};
    const actions = { left: '', center: '', right: '' };

    const tplConnections = document.getElementById('tpl-connections');
    
    if (tplConnections) {
        const templateElement = tplConnections.tagName === 'TEMPLATE' 
            ? tplConnections 
            : tplConnections.querySelector('template');

        if (templateElement) {
            const tempDiv = document.createElement('div');
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
        actions.center = config.centerGroups.map(group => {
            const buttonsHtml = group.actions.map(action => {
                const dataAttr = action.serviceData ? `data-service='${JSON.stringify(action.serviceData)}'` : '';
                const assetAttr = action.assetData ? `data-asset='${JSON.stringify(action.assetData)}'` : '';

                const btnHtml = `
                    <button type="${action.isForm ? 'submit' : 'button'}" 
                        class="toolbar__action-button ${action.class || ''}" 
                        ${action.modal ? `data-modal-target="${action.modal}"` : ''}
                        ${action.id ? `data-id="${action.id}"` : ''}
                        ${dataAttr} ${assetAttr}>
                        <i class="fa-solid ${action.icon}"></i> ${action.label}
                    </button>`;

                if (action.isForm) {
                    const hiddenFields = (action.hiddenFields || []).map(f => 
                        `<input type="hidden" name="${f.name}" value="${f.value}">`
                    ).join('');

                    return `
                        <form action="${action.action}" method="POST" style="display:inline;">
                            <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                            ${hiddenFields}
                            ${btnHtml}
                        </form>`;
                }
                return btnHtml;
            }).join('');

            const divider = group.hasDivider ? '<div class="toolbar__divider"></div>' : '';
            return `${divider}<div class="toolbar__group">${buttonsHtml}</div>`;
        }).join('');
    }

    // --- RIGHT ACTION (Bexi) ---
    if (config.rightAction) {
        const isBexiOpen = localStorage.getItem(BEXI_SIDEBAR_KEY) === 'true';
        actions.right = `
            <button type="button" class="toolbar__action-button toolbar__action-button--bexi ${isBexiOpen ? 'is-active' : ''}" id="bexiToggleBtn">
                <i class="fa-solid ${isBexiOpen ? 'fa-xmark' : 'fa-message'}"></i> 
                <span>${isBexiOpen ? 'Close Bexi' : config.rightAction.label}</span>
            </button>`;
    }

    Toolbar.setActions(actions);

    initBexiToggle();
    
    if (tplConnections) {
        setupDropdown('toolbarConnectionsBtn', 'toolbarConnectionsDropdown');
    }
}

function setupDropdown(btnId, dropdownId, onOpen) {
    const btn = document.getElementById(btnId);
    const dropdown = document.getElementById(dropdownId);
    if (!btn || !dropdown) return;
=======
import { Toolbar } from "../../components/toolbar/Toolbar.js";
import { initAssetStatusFilters } from "../assets/statusFilters.js";
import { initServiceStatusFilters } from "../services/statusFilters.js";
import {
buildBexiButtonHtml,
buildStatusDropdownHtml,
renderCenterGroupsHtml,
initBexiToggle,
initStatusDropdown,
initToolbarForms,
} from "../../components/toolbar/toolbarHelpers.js";

export function initToolbar() {
renderToolbar();
}

function renderToolbar() {
const config = window.BE_DATA?.toolbar || {};
const actions = { left: "", center: "", right: "" };
>>>>>>> 9b2034c ([FEAT] Adjustments to the Toasts + fixed Service Toasts/actions.)

// ── LEFT: Status Filters ──
const tplStatus = document.getElementById('tpl-status-filters');
if (tplStatus && config.showStatus !== false) {
actions.left += buildStatusDropdownHtml("tpl-status-filters");
}

// ── LEFT: Connections (Using the specific original logic) ──
const tplConnections = document.getElementById("tpl-connections");
if (tplConnections) {
actions.left += buildConnectionsDropdownHtml(tplConnections);
}

// ── CENTER ──
actions.center = renderCenterGroupsHtml(Array.isArray(config.centerGroups) ? config.centerGroups : []);

// ── RIGHT ──
if (config.rightAction) {
actions.right = buildBexiButtonHtml(config.rightAction.label);
}

// Vykreslenie do DOM
Toolbar.setActions(actions);

// Bind Events
bindEvents(config.rightAction);
}

function bindEvents(rightActionConfig) {
// Re-introducing the dynamic Asset vs Service check from your working script
initStatusDropdown((containerId) => {
if (window.BE_DATA.service || window.BE_DATA.services) {
initServiceStatusFilters(containerId);
} else {
initAssetStatusFilters(containerId);
}
});

initBexiToggle(rightActionConfig);
initToolbarForms();
initConnectionsDropdown();
}

// ── Connections Helpers ──

function buildConnectionsDropdownHtml(tplConnections) {
// EXACT MATCH of your original logic to find the template
const templateElement = tplConnections.tagName === 'TEMPLATE'
? tplConnections
: tplConnections.querySelector('template');

if (!templateElement) return "";

const tempDiv = document.createElement('div');
tempDiv.appendChild(templateElement.content.cloneNode(true));

return `
<div class="toolbar__status-filters" id="toolbarConnectionsBtn">
    Connections <i class="fa-solid fa-chevron-down"></i>
    <div class="toolbar__status-dropdown toolbar__status-dropdown--wide" id="toolbarConnectionsDropdown"
        style="display:none">
        ${tempDiv.innerHTML}
    </div>
</div>`;
}

function initConnectionsDropdown() {
const btn = document.getElementById("toolbarConnectionsBtn");
const dropdown = document.getElementById("toolbarConnectionsDropdown");
if (!btn || !dropdown) return;

// Standard dropdown toggle logic
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
