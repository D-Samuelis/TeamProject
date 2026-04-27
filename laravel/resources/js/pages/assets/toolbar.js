import { Toolbar } from '../../components/toolbar/Toolbar.js';

import { initAssetStatusFilters } from './statusFilters.js';

export function initToolbar() {
    console.log("toolbar init");
    Toolbar.setActions({
        left: `
            <div class="toolbar__status-filters" id="toolbarStatusFiltersBtn">
                Status
                <i class="fa-solid fa-chevron-down"></i>
                <div class="toolbar__status-dropdown" id="toolbarStatusDropdown">
                    <div class="toolbar__status-list" id="toolbarStatusList"></div>
                </div>
            </div>
        `,
        center: `
            <button class="toolbar__action-button" data-modal-target="create-asset-modal">
                <i class="fa-solid fa-plus"></i> Create Asset
            </button>
        `,
        right: `
            <button class="toolbar__action-button">
                <i class="fa-solid fa-gear"></i> Settings
            </button>
        `
    });

    const btn = document.getElementById('toolbarStatusFiltersBtn');
    const dropdown = document.getElementById('toolbarStatusDropdown');

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        
        if (dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
        } else {
            dropdown.style.display = 'block';
            initAssetStatusFilters('toolbarStatusList');
        }
    });

    document.addEventListener('click', (e) => {
        if (!btn.contains(e.target)) dropdown.style.display = 'none';
    });
}