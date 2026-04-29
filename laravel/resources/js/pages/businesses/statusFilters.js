import { BUSINESS_FILTERS_KEY } from '../../config/storageKeys.js';

const businessStatuses = [
    { id: 'published', label: 'Published', color: 'green', active: true },
    { id: 'hidden',    label: 'Hidden',    color: 'yellow', active: true },
    { id: 'deleted',   label: 'Archived',  color: 'red',   active: false }
];

export function initBusinessStatusFilters(containerId = 'statusList') {
    const container = document.getElementById(containerId);
    if (!container) return;

    const savedFilters = localStorage.getItem(BUSINESS_FILTERS_KEY);
    if (savedFilters) {
        try {
            const activeIds = JSON.parse(savedFilters);
            businessStatuses.forEach(s => {
                s.active = activeIds.includes(s.id);
            });
        } catch (e) {
            console.error("Error parsing business filters", e);
        }
    }

    container.innerHTML = '';

    businessStatuses.forEach(status => {
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
            
            const activeIds = businessStatuses
                .filter(s => s.active)
                .map(s => s.id);
            localStorage.setItem(BUSINESS_FILTERS_KEY, JSON.stringify(activeIds));
            
            window.dispatchEvent(new CustomEvent('businessFiltersChanged', { 
                detail: { statuses: businessStatuses } 
            }));
        });

        container.appendChild(filterItem);
    });

    window.dispatchEvent(new CustomEvent('businessFiltersChanged', { 
        detail: { statuses: businessStatuses } 
    }));
}