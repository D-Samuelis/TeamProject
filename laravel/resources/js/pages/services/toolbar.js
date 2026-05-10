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
