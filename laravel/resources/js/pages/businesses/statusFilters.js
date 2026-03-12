const businessStatuses = [
    { id: 'published', label: 'Published', color: 'green', active: true },
    { id: 'hidden',    label: 'Hidden',    color: 'black', active: true },
    { id: 'deleted',   label: 'Archived',  color: 'red',   active: false }
];

export function initBusinessStatusFilters() {
    const container = document.getElementById('statusList');
    if (!container) return;

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

        filterItem.addEventListener('click', () => {
            status.active = !status.active;
            filterItem.classList.toggle('is-active');
            
            // Odoslanie eventu, na ktorý bude počúvať listView
            window.dispatchEvent(new CustomEvent('businessFiltersChanged', { 
                detail: { statuses: businessStatuses } 
            }));
        });

        container.appendChild(filterItem);
    });
}