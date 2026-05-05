import { TableSorter } from '../../components/table/tableSorter.js';
import { TableRenderer } from '../../components/table/tableRenderer.js';
import { initPaginator } from './appointmentPaginator.js';

export function initAppointmentListView(data = [], meta = {}) {
    const container = document.getElementById('appointmentTableContainer');
    if (!container) return;

    const headerCorner = document.querySelector('#listView .business__header-corner');
    if (headerCorner) renderCorner(headerCorner);

    const mappedData = data.map(item => ({
        ...item,
        service_name:  item.service?.name  || 'N/A',
        service_price: item.service?.price || '0',
    }));

    const tableConfig = {
        searchId:  '#appointmentSearchInput',
        tableClass: 'appointments-table',
        rowClass:   'appointment-table__row',
        columns: [
            {
                label: 'Date', key: 'date', sortable: true, searchable: true,
                render: (val) => `<span>${new Date(val).toDateString()}</span>`
            },
            {
                label: 'Time', key: 'start_at', sortable: true, searchable: true,
                render: (val) => `<span>${new Date(val).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>`
            },
            {
                label: 'Service', key: 'service_name', sortable: true, searchable: true,
                render: (val) => `<span>${val}</span>`
            },
            {
                label: 'Duration', key: 'duration', sortable: true, searchable: true,
                render: (val) => `<span>${val || 'N/A'}m</span>`
            },
            {
                label: 'Price', key: 'service_price', sortable: true, searchable: true,
                render: (val) => `<span>${val}€</span>`
            },
            {
                label: 'Status', key: 'status', sortable: true,
                render: (val) => `<span class="status-cell status--${val}">${val}</span>`
            },
        ],
        renderActions: (item) => `
            <div class="business__actions">
                <button class="button-icon button-icon--danger" title="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>`,
    };

    const renderer = new TableRenderer(tableConfig);
    const sorter   = new TableSorter(mappedData, 'date', 'desc', (sortedData) => {
        renderer.render(container, sortedData, sorter);
    });

    renderer.render(container, sorter.getSortedData(), sorter);

    initPaginator(meta, (page) => {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    });
}


/**
 * Renders the top-left corner with the view switcher (Columns / List)
 * @param {HTMLElement} parent
 */
function renderCorner(parent) {
    parent.innerHTML = '';

    const listView     = document.getElementById('listView');
    const isListActive = listView && !listView.classList.contains('hidden');

    const switcherContainer = document.createElement('div');
    switcherContainer.className = 'view-switcher';
    switcherContainer.innerHTML = `
        <button class="view-switcher__btn ${!isListActive ? 'active' : ''}" id="showTimeline">
            <i class="fa-solid fa-table-columns"></i> Columns
        </button>
        <button class="view-switcher__btn ${isListActive ? 'active' : ''}" id="showList">
            <i class="fa-solid fa-list"></i> List
        </button>
    `;

    parent.appendChild(switcherContainer);
}
