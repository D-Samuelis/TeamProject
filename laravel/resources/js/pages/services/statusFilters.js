import { SERVICE_FILTERS_KEY } from '../../config/storageKeys.js';

const serviceStatuses = [
    { id: 'active',   label: 'Active',   color: 'green',  active: true },
    { id: 'inactive', label: 'Inactive', color: 'yellow', active: true },
    { id: 'archived', label: 'Archived', color: 'red',    active: false }
];

export function initServiceStatusFilters() {
    const container = document.getElementById('statusList');
    if (!container) return;

    const savedFilters = localStorage.getItem(SERVICE_FILTERS_KEY);
    if (savedFilters) {
        const activeIds = JSON.parse(savedFilters);
        serviceStatuses.forEach(s => {
            s.active = activeIds.includes(s.id);
        });
    }

    container.innerHTML = '';

    serviceStatuses.forEach(status => {
        const filterItem = document.createElement('div');
        filterItem.className = `filter-item filter-item--${status.color} ${status.active ? 'is-active' : ''}`;
        
        filterItem.innerHTML = `
            <div class="filter-item__checkbox">
                <i class="fa-solid fa-check"></i>
            </div>
            <span class="filter-item__label">${status.label}</span>
        `;

        filterItem.addEventListener('click', () => {
            status.active = !status.active;
            filterItem.classList.toggle('is-active');
            
            const activeIds = serviceStatuses
                .filter(s => s.active)
                .map(s => s.id);
            localStorage.setItem(SERVICE_FILTERS_KEY, JSON.stringify(activeIds));
            
            window.dispatchEvent(new CustomEvent('serviceFiltersChanged', { 
                detail: { statuses: serviceStatuses } 
            }));
        });

        container.appendChild(filterItem);
    });

    window.dispatchEvent(new CustomEvent('serviceFiltersChanged', { 
        detail: { statuses: serviceStatuses } 
    }));
}