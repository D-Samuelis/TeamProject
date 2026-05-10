import { Toolbar } from "../../components/toolbar/Toolbar.js";
import { initBranchStatusFilters } from "./statusFilters.js";
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

    // ── LEFT: status filter (index page only; show page sets showStatus:false) ──
    if (config.showStatus !== false) {
        actions.left = buildStatusDropdownHtml("tpl-status-filters");
    }

    // ── CENTER: groups from BE_DATA ───────────────────────────────────────
    actions.center = renderCenterGroupsHtml(
        Array.isArray(config.centerGroups) ? config.centerGroups : [],
    );

    // ── RIGHT: Bexi chatbot toggle ────────────────────────────────────────
    if (config.rightAction) {
        actions.right = buildBexiButtonHtml(config.rightAction.label);
    }

    Toolbar.setActions(actions);
    bindEvents(config.rightAction);
}

function bindEvents(rightActionConfig) {
    initStatusDropdown((containerId) => initBranchStatusFilters(containerId));
    initBexiToggle(rightActionConfig);
    initToolbarForms();
}
