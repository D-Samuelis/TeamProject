import { Toolbar } from "../../components/toolbar/Toolbar.js";
import { openSidebar, closeSidebar } from "../../chatbot/main.js";
import { BEXI_SIDEBAR_KEY } from "../../config/storageKeys.js";
import { buildBexiButtonHtml } from "../../components/toolbar/toolBarHelpers.js";

export function initToolbar() {
    renderMinimalToolbar();
}

function renderMinimalToolbar() {
    const config = window.BE_DATA?.toolbar?.rightAction || { label: 'Ask Bexi' };
    
    const actions = {
        left: "",
        center: "",
        right: buildBexiButtonHtml(config.label)
    };

    Toolbar.setActions(actions);
    
    setupBexiEvent();
}

function setupBexiEvent() {
    const bexiBtn = document.getElementById("bexiToggleBtn");
    if (!bexiBtn) return;

    const shouldBeOpen = localStorage.getItem(BEXI_SIDEBAR_KEY) === "true";
    if (shouldBeOpen) {
        setBexiActiveState(bexiBtn, true);
        setTimeout(() => openSidebar(), 100);
    }

    bexiBtn.onclick = (e) => {
        e.stopPropagation();
        const isActive = bexiBtn.classList.contains("is-active");

        if (!isActive) {
            setBexiActiveState(bexiBtn, true);
            localStorage.setItem(BEXI_SIDEBAR_KEY, "true");
            openSidebar();
        } else {
            setBexiActiveState(bexiBtn, false);
            localStorage.setItem(BEXI_SIDEBAR_KEY, "false");
            closeSidebar();
        }
    };
}

function setBexiActiveState(btn, isActive) {
    const label = window.BE_DATA?.toolbar?.rightAction?.label || "Ask Bexi";
    const span = btn.querySelector("span");
    const icon = btn.querySelector("i");

    if (isActive) {
        btn.classList.add("is-active");
        if (span) span.textContent = "Close Bexi";
        if (icon) icon.className = "fa-solid fa-xmark";
    } else {
        btn.classList.remove("is-active");
        if (span) span.textContent = label;
        if (icon) icon.className = "fa-solid fa-message";
    }
}