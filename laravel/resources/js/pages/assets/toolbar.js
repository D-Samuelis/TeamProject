import { Toolbar } from '../../components/toolbar/Toolbar.js';
import { initAssetStatusFilters } from './statusFilters.js';
import { openSidebar, closeSidebar } from '../../chatbot/main.js';
import { BEXI_SIDEBAR_KEY } from '../../config/storageKeys.js';

export function initToolbar() {
    const config = window.BE_DATA?.toolbar || {};
    const actions = { left: '', center: '', right: '' };

    // --- LEFT (Statusy a Connections) ---
    const tplStatus = document.getElementById('tpl-status-filters');
    if (tplStatus) {
        actions.left += `
            <div class="toolbar__status-filters" id="toolbarStatusBtn">
                Status <i class="fa-solid fa-chevron-down"></i>
                <div class="toolbar__status-dropdown" id="toolbarStatusDropdown" style="display:none">
                    ${tplStatus.innerHTML}
                </div>
            </div>`;
    }

    const tplConnections = document.getElementById('tpl-connections');
    if (tplConnections) {
        actions.left += `
            <div class="toolbar__status-filters" id="toolbarConnectionsBtn">
                Connections <i class="fa-solid fa-chevron-down"></i>
                <div class="toolbar__status-dropdown" id="toolbarConnectionsDropdown" style="display:none">
                    <div class="toolbar__connections-container">
                        ${tplConnections.innerHTML}
                    </div>
                </div>
            </div>`;
    }

    console.log(config.centerGroups);

    // --- CENTER ---
    if (Array.isArray(config.centerGroups)) {
        actions.center = config.centerGroups.map(group => {
            const buttonsHtml = group.actions.map(action => {
                const btnHtml = `
                    <button type="${action.isForm ? 'submit' : 'button'}" 
                        class="toolbar__action-button ${action.class || ''}" 
                        ${action.modal ? `data-modal-target="${action.modal}"` : ''}
                        ${action.id ? `data-id="${action.id}"` : ''}>
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

    // --- RIGHT ACTION (Bexi) ---
    if (config.rightAction) {
        actions.right = `
            <button type="button" class="toolbar__action-button toolbar__action-button--bexi" id="bexiToggleBtn">
                <i class="fa-solid fa-message"></i> <span>${config.rightAction.label}</span>
            </button>
        `;
    }

    // Vykreslenie do DOMu cez tvoju Toolbar komponentu
    Toolbar.setActions(actions);

    // Re-inicializácia eventov (Bexi, Dropdowny)
    initBexiToggle();
    if (tplStatus) {
        setupDropdown('toolbarStatusBtn', 'toolbarStatusDropdown', () => {
            initAssetStatusFilters('toolbarStatusDropdown'); 
        });
    }
    if (tplConnections) {
        setupDropdown('toolbarConnectionsBtn', 'toolbarConnectionsDropdown');
    }
}

function setupDropdown(btnId, dropdownId, onOpen) {
    const btn = document.getElementById(btnId);
    const dropdown = document.getElementById(dropdownId);
    if (!btn || !dropdown) return;

    btn.onclick = (e) => {
        e.stopPropagation();
        const isVisible = dropdown.style.display === 'block';
        
        document.querySelectorAll('.toolbar__status-dropdown').forEach(d => {
            if (d !== dropdown) d.style.display = 'none';
        });

        dropdown.style.display = isVisible ? 'none' : 'block';
        
        if (!isVisible && onOpen) onOpen();
    };

    document.addEventListener('click', (e) => {
        if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    dropdown.onclick = (e) => {
        e.stopPropagation();
    };
}

function initBexiToggle() {
    const btn = document.getElementById('bexiToggleBtn');
    if (!btn) return;

    const icon = btn.querySelector('i');
    const label = btn.querySelector('span');
    
    const isStoredOpen = localStorage.getItem(BEXI_SIDEBAR_KEY) === 'true';

    if (isStoredOpen) {
        label.textContent = 'Close Bexi';
        icon.className = 'fa-solid fa-xmark';
        btn.classList.add('is-active');

        openSidebar(); 
    }

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const willOpen = !btn.classList.contains('is-active');

        if (willOpen) {
            label.textContent = 'Close Bexi';
            icon.className = 'fa-solid fa-xmark';
            btn.classList.add('is-active');
            
            localStorage.setItem(BEXI_SIDEBAR_KEY, 'true');
            openSidebar(); 
        } else {
            label.textContent = 'Ask Bexi';
            icon.className = 'fa-solid fa-message';
            btn.classList.remove('is-active');
            
            localStorage.setItem(BEXI_SIDEBAR_KEY, 'false');
            closeSidebar();
        }
    });
}