import { Toolbar } from '../../components/toolbar/Toolbar.js';
import { initAssetStatusFilters } from '../assets/statusFilters.js';
import { initServiceStatusFilters } from '../services/statusFilters.js';
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
                    <div id="statusFilterContainer">${tplStatus.innerHTML}</div>
                </div>
            </div>`;
    }

    const tplConnections = document.getElementById('tpl-connections');
    
    if (tplConnections) {
        // Ak je tplConnections div, musíme nájsť template vo vnútri
        const templateElement = tplConnections.tagName === 'TEMPLATE' 
            ? tplConnections 
            : tplConnections.querySelector('template');

        if (templateElement) {
            // Použijeme pomocný div na pretransformovanie document-fragmentu na HTML reťazec
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

    // --- CENTER (Dynamické akcie: Create, Edit, Status Toggle) ---
    if (Array.isArray(config.centerGroups)) {
        actions.center = config.centerGroups.map(group => {
            const buttonsHtml = group.actions.map(action => {
                // Príprava dát pre modaly (ak existujú)
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

    // 1. Vykreslenie do DOM
    Toolbar.setActions(actions);

    // 2. Inicializácia eventov
    initBexiToggle();
    
    if (tplStatus) {
        setupDropdown('toolbarStatusBtn', 'toolbarStatusDropdown', () => {
            // Ak máme v BE_DATA "service", sme v sekcii služieb
            if (window.BE_DATA.service || window.BE_DATA.services) {
                initServiceStatusFilters('statusFilterContainer'); 
            } else {
                initAssetStatusFilters('statusFilterContainer');
            }
        });
    }
    
    if (tplConnections) {
        setupDropdown('toolbarConnectionsBtn', 'toolbarConnectionsDropdown');
    }
}

// --- POMOCNÉ FUNKCIE (Nemenia sa) ---
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
    dropdown.onclick = (e) => e.stopPropagation();
}

function initBexiToggle() {
    const btn = document.getElementById('bexiToggleBtn');
    if (!btn) return;
    btn.addEventListener('click', (e) => {
        const icon = btn.querySelector('i');
        const label = btn.querySelector('span');
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