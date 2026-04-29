import { Toolbar } from '../../components/toolbar/Toolbar.js';
import { initBusinessStatusFilters } from './statusFilters.js';
import { openSidebar, closeSidebar } from '../../chatbot/main.js';
import { BEXI_SIDEBAR_KEY } from '../../config/storageKeys.js';

export function initToolbar() {
    const params = new URLSearchParams(window.location.search);
    const branchId = params.get('branch');
    
    if (branchId) {
        const item = document.querySelector(`.branch-filter-item[data-branch-id="${branchId}"]`);
        if (item) item.classList.add('is-active');
    }

    renderToolbar();

    const sidebar = document.querySelector('.business__sidebar');
    if (sidebar) {
        sidebar.addEventListener('click', (e) => {
            const item = e.target.closest('.branch-filter-item');
            if (!item) return;

            document.querySelectorAll('.branch-filter-item').forEach(el => el.classList.remove('is-active'));
            item.classList.add('is-active');

            renderToolbar();
        });
    }
}

function renderToolbar() {
    const config = window.BE_DATA?.toolbar || {};
    const actions = { left: '', center: '', right: '' };

    const activeEl = document.querySelector('.branch-filter-item.is-active') || 
                     document.querySelector('.branch-filter-item.active');
    
    const isRealBranch = activeEl && activeEl.dataset.filter !== 'all';
    
    let branchActions = [];

    if (isRealBranch) {
        try {
            let branchData = {};
            
            if (activeEl.dataset.branch) {
                branchData = JSON.parse(activeEl.dataset.branch);
            } else {
                branchData = {
                    id: activeEl.dataset.branchId,
                    name: activeEl.querySelector('.member-name')?.textContent.trim() || 'Branch',
                    is_active: activeEl.querySelector('.member-role')?.textContent.includes('Active') ? 1 : 0
                };
            }

            branchActions = [
                {
                    label: `Status: ${branchData.is_active ? 'Active' : 'Inactive'}`,
                    icon: branchData.is_active ? 'fa-circle text-green' : 'fa-circle text-yellow',
                    isForm: true,
                    action: window.BE_DATA.routes.branchUpdate.replace(':id', branchData.id),
                    hiddenFields: [
                        { name: 'business_id', value: window.BE_DATA.business.id },
                        { name: 'is_active', value: branchData.is_active ? 0 : 1 },
                        { name: '_method', value: 'PUT' }
                    ]
                },
                {
                    label: 'Manage Branch',
                    icon: 'fa-gear',
                    modal: 'edit-branch-modal',
                    branchData: branchData 
                },
                {
                    label: 'Archive Branch',
                    icon: 'fa-box-archive',
                    class: 'delete-action',
                    modal: 'archive-branch-modal',
                    id: branchData.id,
                    name: branchData.name
                }
            ];
        } catch (e) {
            console.error("Toolbar render error:", e);
        }
    }

    // --- LEFT (Status filtre biznisu) ---
    const tplStatus = document.getElementById('tpl-business-filters');
    if (tplStatus) {
        actions.left = `
            <div class="toolbar__status-filters" id="toolbarStatusBtn">
                Status <i class="fa-solid fa-chevron-down"></i>
                <div class="toolbar__status-dropdown" id="toolbarStatusDropdown" style="display:none">
                    ${tplStatus.innerHTML}
                </div>
            </div>`;
    }

    // --- CENTER (Kombinácia Branch akcií a globálnych akcií) ---
    let centerHtml = '';
    
    if (branchActions.length > 0) {
        centerHtml += `<div class="toolbar__group">${renderButtons(branchActions)}</div>`;
        
        if (Array.isArray(config.centerGroups) && config.centerGroups.length > 0) {
            centerHtml += `<div class="toolbar__divider"></div>`;
        }
    }

    if (Array.isArray(config.centerGroups)) {
        centerHtml += config.centerGroups.map((group, index) => {
            const showDivider = group.hasDivider || (index > 0);
            const dividerHtml = showDivider ? '<div class="toolbar__divider"></div>' : '';
            
            return `${dividerHtml}<div class="toolbar__group">${renderButtons(group.actions)}</div>`;
        }).join('');
    }
    actions.center = centerHtml;

    // --- RIGHT (Bexi Chatbot) ---
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
    rebindEvents(tplStatus);
}

function renderButtons(buttons) {
    return buttons.map(action => {
        const btnHtml = `
            <button type="${action.isForm ? 'submit' : 'button'}" 
                class="toolbar__action-button ${action.class || ''}" 
                ${action.modal ? `data-modal-target="${action.modal}"` : ''}
                ${action.id ? `data-id="${action.id}"` : ''}
                ${action.name ? `data-name="${action.name}"` : ''}
                ${action.branchData ? `data-branch='${JSON.stringify(action.branchData)}'` : ''}>
                <i class="fa-solid ${action.icon}"></i> ${action.label}
            </button>
        `;

        if (action.isForm) {
            const hiddens = (action.hiddenFields || []).map(f => 
                `<input type="hidden" name="${f.name}" value="${f.value}">`
            ).join('');
            return `<form action="${action.action}" method="POST" style="display:inline;">
                        <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">${hiddens}${btnHtml}
                    </form>`;
        }
        return btnHtml;
    }).join('');
}

function rebindEvents(tplStatus) {
    // Dropdown
    const btn = document.getElementById('toolbarStatusBtn');
    const dropdown = document.getElementById('toolbarStatusDropdown');
    if (btn && dropdown) {
        btn.onclick = (e) => {
            e.stopPropagation();
            const isVisible = dropdown.style.display === 'block';
            dropdown.style.display = isVisible ? 'none' : 'block';
            if (!isVisible && tplStatus) initBusinessStatusFilters('toolbarStatusDropdown');
        };
    }

    // Bexi
    const bexiBtn = document.getElementById('bexiToggleBtn');
    if (bexiBtn) {
        bexiBtn.onclick = (e) => {
            e.stopPropagation();
            const willOpen = !bexiBtn.classList.contains('is-active');
            if (willOpen) {
                bexiBtn.querySelector('span').textContent = 'Close Bexi';
                bexiBtn.querySelector('i').className = 'fa-solid fa-xmark';
                bexiBtn.classList.add('is-active');
                localStorage.setItem(BEXI_SIDEBAR_KEY, 'true');
                openSidebar();
            } else {
                bexiBtn.querySelector('span').textContent = 'Ask Bexi';
                bexiBtn.querySelector('i').className = 'fa-solid fa-message';
                bexiBtn.classList.remove('is-active');
                localStorage.setItem(BEXI_SIDEBAR_KEY, 'false');
                closeSidebar();
            }
        };
        if (localStorage.getItem(BEXI_SIDEBAR_KEY) === 'true') bexiBtn.classList.add('is-active');
    }

    document.addEventListener('click', (e) => {
        if (btn && !btn.contains(e.target) && dropdown && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}