import { Toolbar } from '../../components/toolbar/Toolbar.js';
import { openSidebar, closeSidebar } from '../../chatbot/main.js';
import { BEXI_SIDEBAR_KEY } from '../../config/storageKeys.js';
import { initBranchStatusFilters } from './statusFilters.js';

export function initToolbar() {
    renderToolbar();
}

function renderToolbar() {
    const config = window.BE_DATA?.toolbar || {};
    const actions = { left: '', center: '', right: '' };

    // --- LEFT (Status filtre - ak sú definované v tpl) ---
    const tplStatus = document.getElementById('tpl-status-filters'); // Predpokladám tpl pre Branch
    if (tplStatus) {
        actions.left = `
            <div class="toolbar__status-filters" id="toolbarStatusBtn">
                Status <i class="fa-solid fa-chevron-down"></i>
                <div class="toolbar__status-dropdown" id="toolbarStatusDropdown" style="display:none">
                    ${tplStatus.innerHTML}
                </div>
            </div>`;
    }

    // --- CENTER (Dynamické skupiny tlačidiel z BE_DATA) ---
    if (Array.isArray(config.centerGroups)) {
        actions.center = config.centerGroups.map((group, index) => {
            const divider = (index > 0 || group.hasDivider) ? `<div class="toolbar__divider"></div>` : '';
            return `${divider}<div class="toolbar__group">${renderButtons(group.actions)}</div>`;
        }).join('');
    }

    // --- RIGHT (Bexi Chatbot s podporou storage) ---
    if (config.rightAction) {
        const isBexiOpen = localStorage.getItem(BEXI_SIDEBAR_KEY) === 'true';
        actions.right = `
            <button type="button" class="toolbar__action-button toolbar__action-button--bexi" id="bexiToggleBtn">
                <i class="fa-solid ${isBexiOpen ? 'fa-xmark' : 'fa-message'}"></i> 
                <span>${isBexiOpen ? 'Close Bexi' : config.rightAction.label}</span>
            </button>
        `;
    }

    Toolbar.setActions(actions);
    setupEvents();
}

function renderButtons(buttons) {
    return buttons.map(action => {
        const btnHtml = `
            <button type="${action.isForm ? 'submit' : 'button'}" 
                class="toolbar__action-button ${action.class || ''}" 
                ${action.modal ? `data-modal-target="${action.modal}"` : ''}
                ${action.id ? `data-id="${action.id}"` : ''}
                ${action.name ? `data-name="${action.name}"` : ''}>
                <i class="fa-solid ${action.icon}"></i> ${action.label}
            </button>
        `;

        if (action.isForm) {
            const hiddens = (action.hiddenFields || []).map(f => 
                `<input type="hidden" name="${f.name}" value="${f.value}">`
            ).join('');

            return `<form action="${action.action}" method="POST" style="display:inline;">
                        <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                        <input type="hidden" name="_method" value="PATCH">
                        ${hiddens}
                        ${btnHtml}
                    </form>`;
        }
        return btnHtml;
    }).join('');
}

function setupEvents() {
    // Bexi Toggle Event
    const bexiBtn = document.getElementById('bexiToggleBtn');
    if (bexiBtn) {
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
        // Inicializácia stavu pri loade
        if (localStorage.getItem(BEXI_SIDEBAR_KEY) === 'true') bexiBtn.classList.add('is-active');
    }

    // Status Dropdown Event
    const statusBtn = document.getElementById('toolbarStatusBtn');
    const dropdown = document.getElementById('toolbarStatusDropdown');
    
    if (statusBtn && dropdown) {
        statusBtn.onclick = (e) => {
            e.stopPropagation();
            const isVisible = dropdown.style.display === 'block';
            
            if (!isVisible) {
                dropdown.style.display = 'block';
                initBranchStatusFilters('toolbarStatusDropdown');
            } else {
                dropdown.style.display = 'none';
            }
        };

        document.addEventListener('click', (e) => {
            if (!statusBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }
}