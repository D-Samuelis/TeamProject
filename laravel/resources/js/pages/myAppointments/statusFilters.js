import { APPOINTMENT_FILTERS_KEY } from '../../config/storageKeys.js';

const statuses = [
    { id: 'reserved',  label: 'Reserved',  color: 'yellow', active: true },
    { id: 'confirmed', label: 'Confirmed', color: 'green',   active: true },
    { id: 'cancelled', label: 'Cancelled', color: 'red',    active: true },
    { id: 'noshow',    label: 'No Show',   color: 'black',  active: true },
    { id: 'show',      label: 'Show',      color: 'blue',  active: true },
];

export function initStatusFilters() {
    const container = document.getElementById('filterList');
    if (!container) return;

    const savedFilters = localStorage.getItem(APPOINTMENT_FILTERS_KEY);
    if (savedFilters) {
        const activeIds = JSON.parse(savedFilters);
        statuses.forEach(s => {
            s.active = activeIds.includes(s.id);
        });
    }

    container.innerHTML = '';

    statuses.forEach(status => {
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
            
            const activeIds = statuses.filter(s => s.active).map(s => s.id);
            localStorage.setItem(APPOINTMENT_FILTERS_KEY, JSON.stringify(activeIds));
            
            window.dispatchEvent(new CustomEvent('filtersChanged', { detail: statuses }));
        });

        container.appendChild(filterItem);
    });

    window.dispatchEvent(new CustomEvent('filtersChanged', { detail: statuses }));
}