import { SERVICE_FILTERS_KEY } from '../../config/storageKeys.js';

const serviceStatuses = [
    { id: 'active',   label: 'Active',   color: 'green',  active: true },
    { id: 'inactive', label: 'Inactive', color: 'yellow', active: true },
    { id: 'archived', label: 'Archived', color: 'red',    active: false }
];

/**
 * @param {string} containerId - ID elementu, kam sa filtre vykreslia
 */
export function initServiceStatusFilters(containerId = 'statusList') {
    const container = document.getElementById(containerId);
    if (!container) return;

    // 1. Načítanie uložených filtrov z localStorage
    const savedFilters = localStorage.getItem(SERVICE_FILTERS_KEY);
    if (savedFilters) {
        try {
            const activeIds = JSON.parse(savedFilters);
            serviceStatuses.forEach(s => {
                s.active = activeIds.includes(s.id);
            });
        } catch (e) {
            console.error("Chyba pri parsovaní SERVICE_FILTERS_KEY", e);
        }
    }

    // 2. Vyčistenie a vykreslenie
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

        // 3. Event Listener
        filterItem.addEventListener('click', (e) => {
            // Dôležité pre Toolbar: klik na filter nesmie zavrieť dropdown
            e.stopPropagation();

            status.active = !status.active;
            filterItem.classList.toggle('is-active');
            
            const activeIds = serviceStatuses
                .filter(s => s.active)
                .map(s => s.id);
            
            localStorage.setItem(SERVICE_FILTERS_KEY, JSON.stringify(activeIds));
            
            // Notifikácia pre Manager (tabuľku), že sa zmenili filtre
            window.dispatchEvent(new CustomEvent('serviceFiltersChanged', { 
                detail: { statuses: serviceStatuses } 
            }));
        });

        container.appendChild(filterItem);
    });

    // Počiatočná notifikácia po načítaní stránky
    window.dispatchEvent(new CustomEvent('serviceFiltersChanged', { 
        detail: { statuses: serviceStatuses } 
    }));
}