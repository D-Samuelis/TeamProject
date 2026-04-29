import { Toolbar } from '../../components/toolbar/Toolbar.js';
import { openSidebar, closeSidebar } from '../../chatbot/main.js';
import { BEXI_SIDEBAR_KEY } from '../../config/storageKeys.js';

export function initToolbar() {
    const config = window.BE_DATA?.toolbar || {};
    const actions = { left: '', center: '', right: '' };

    // --- LEFT (Business špecifické filtre ak sú potrebné) ---
    // Ak budeš mať filtre pre Business Index (napr. Active/Archived), pridaj tpl sem
    const tplFilters = document.getElementById('tpl-business-filters');
    if (tplFilters) {
        actions.left += `
            <div class="toolbar__status-filters" id="toolbarBusinessBtn">
                Filters <i class="fa-solid fa-chevron-down"></i>
                <div class="toolbar__status-dropdown" id="toolbarBusinessDropdown" style="display:none">
                    ${tplFilters.innerHTML}
                </div>
            </div>`;
    }

    // --- CENTER (Dynamické renderovanie z BE_DATA) ---
    if (Array.isArray(config.centerGroups)) {
        actions.center = config.centerGroups.map(group => {
            const buttonsHtml = group.actions.map(action => {
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
                    return `
                        <form action="${action.action}" method="POST" style="display:inline;">
                            <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                            ${btnHtml}
                        </form>
                    `;
                }
                return btnHtml;
            }).join('');

            const divider = group.hasDivider ? '<div class="toolbar__divider"></div>' : '';
            return `<div class="toolbar__group">${divider}${buttonsHtml}</div>`;
        }).join('');
    }

    // --- RIGHT (Bexi) ---
    if (config.rightAction) {
        actions.right = `
            <button type="button" class="toolbar__action-button toolbar__action-button--bexi" id="bexiToggleBtn">
                <i class="fa-solid fa-message"></i> <span>${config.rightAction.label}</span>
            </button>
        `;
    }

    // Renderovanie cez komponentu
    Toolbar.setActions(actions);

    // Inicializácia eventov
    initBexiToggle();
    if (tplFilters) {
        setupDropdown('toolbarBusinessBtn', 'toolbarBusinessDropdown');
    }
}

function setupDropdown(btnId, dropdownId) {
    const btn = document.getElementById(btnId);
    const dropdown = document.getElementById(dropdownId);
    if (!btn || !dropdown) return;

    btn.onclick = (e) => {
        e.stopPropagation();
        const isVisible = dropdown.style.display === 'block';
        dropdown.style.display = isVisible ? 'none' : 'block';
    };

    document.addEventListener('click', (e) => {
        if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}

function initBexiToggle() {
    const btn = document.getElementById('bexiToggleBtn');
    if (!btn) return;

    const icon = btn.querySelector('i');
    const label = btn.querySelector('span');
    
    const updateUI = (isOpen) => {
        if (isOpen) {
            label.textContent = 'Close Bexi';
            icon.className = 'fa-solid fa-xmark';
            btn.classList.add('is-active');
            openSidebar();
        } else {
            label.textContent = 'Ask Bexi';
            icon.className = 'fa-solid fa-message';
            btn.classList.remove('is-active');
            closeSidebar();
        }
    };

    // Initial state
    if (localStorage.getItem(BEXI_SIDEBAR_KEY) === 'true') {
        updateUI(true);
    }

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const newState = !btn.classList.contains('is-active');
        localStorage.setItem(BEXI_SIDEBAR_KEY, newState);
        updateUI(newState);
    });
}