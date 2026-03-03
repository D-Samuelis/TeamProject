const statuses = [
    { id: 'reserved',  label: 'Reserved',  color: 'yellow', active: true },
    { id: 'confirmed', label: 'Confirmed', color: 'blue',   active: true },
    { id: 'cancelled', label: 'Cancelled', color: 'red',    active: true },
    { id: 'noshow',    label: 'No Show',   color: 'black',  active: true },
    { id: 'show',      label: 'Show',      color: 'green',  active: true },
];

export function initStatusFilters() {
    const container = document.getElementById('filterList');
    if (!container) return;

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
            
            console.log('Active filters:', statuses.filter(s => s.active).map(s => s.id));
            window.dispatchEvent(new CustomEvent('filtersChanged', { detail: statuses }));
        });

        container.appendChild(filterItem);
    });
}