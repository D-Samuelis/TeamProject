import { BRANCH_FILTERS_KEY } from '../../config/storageKeys.js'; // Uisti sa, že kľúč existuje v storageKeys.js

const branchStatuses = [
    { id: 'active',   label: 'Active',   color: 'green',  active: true },
    { id: 'inactive', label: 'Inactive', color: 'yellow', active: true },
    { id: 'archived', label: 'Archived', color: 'red',    active: false }
];

export function initBranchStatusFilters(containerId = 'toolbarStatusDropdown') {
    const container = document.getElementById(containerId);
    if (!container) return;

    // Načítanie zo storage
    const savedFilters = localStorage.getItem(BRANCH_FILTERS_KEY);
    if (savedFilters) {
        try {
            const activeIds = JSON.parse(savedFilters);
            branchStatuses.forEach(s => {
                s.active = activeIds.includes(s.id);
            });
        } catch (e) {
            console.error("Error parsing branch filters", e);
        }
    }

    container.innerHTML = '';

    branchStatuses.forEach(status => {
        const filterItem = document.createElement('div');
        filterItem.className = `filter-item filter-item--${status.color} ${status.active ? 'is-active' : ''}`;
        
        filterItem.innerHTML = `
            <div class="filter-item__checkbox">
                <i class="fa-solid fa-check"></i>
            </div>
            <span class="filter-item__label">${status.label}</span>
        `;

        filterItem.addEventListener('click', (e) => {
            e.stopPropagation();

            status.active = !status.active;
            filterItem.classList.toggle('is-active');
            
            const activeIds = branchStatuses
                .filter(s => s.active)
                .map(s => s.id);
            localStorage.setItem(BRANCH_FILTERS_KEY, JSON.stringify(activeIds));
            
            // Odoslanie eventu, aby ListView vedel, že má prefiltrovať DOM
            window.dispatchEvent(new CustomEvent('branchFiltersChanged', { 
                detail: { statuses: branchStatuses } 
            }));
        });

        container.appendChild(filterItem);
    });

    // Prvotný trigger po načítaní
    window.dispatchEvent(new CustomEvent('branchFiltersChanged', { 
        detail: { statuses: branchStatuses } 
    }));
}