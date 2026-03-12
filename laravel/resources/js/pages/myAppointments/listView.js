import { TableSorter } from '../../components/table/tableSorter.js';
import { TableRenderer } from '../../components/table/tableRenderer.js';

let sorter = null;
let renderer = null;
let originalAppointments = [];

export function initListView(appointments = []) {
    const listView = document.getElementById('listView');
    if (!listView) return;

    originalAppointments = [...appointments];

    listView.innerHTML = '';
    
    const headerWrapper = document.createElement('div');
    headerWrapper.className = 'list-view__header-wrapper';
    
    const bodyWrapper = document.createElement('div');
    bodyWrapper.className = 'list-view__body-wrapper';
    bodyWrapper.id = 'listBody';

    listView.appendChild(headerWrapper);
    listView.appendChild(bodyWrapper);

    const tableConfig = {
        searchId: '#appointmentSearch',
        rowClass: 'appointments-table__row',
        columns: [
            { 
                label: 'Date', key: 'date', sortable: true, searchable: true,
                render: (val) => {
                    const todayStr = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    const isToday = val === todayStr;
                    return `
                        <div class="date-cell">
                            ${val}
                            ${isToday ? '<span class="today-badge">Today</span>' : ''}
                        </div>`;
                }
            },
            { label: 'Time', key: 'time', sortable: true, searchable: true, render: (val) => `<div class="time-cell">${val}</div>` },
            { label: 'Duration', key: 'duration', sortable: true, searchable: true, render: (val) => `<div class="duration-cell">${val}</div>` },
            { label: 'Service', key: 'service', sortable: true, searchable: true, render: (val) => `<span class="service-cell">${val}</span>` },
            { 
                label: 'Status', key: 'status', sortable: true, 
                render: (val) => `<span class="status-cell ${getStatusClass(val)}">${val}</span>` 
            },
        ],
        renderActions: (app) => `
            <button class="button-icon"><i class="fa-solid fa-pen"></i></button>
            <button class="button-icon button-icon--danger"><i class="fa-solid fa-trash"></i></button>
        `,
        onRowRender: (tr, app) => {
            const todayStr = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            if (app.date === todayStr) tr.classList.add('is-today');
        }
    };

    renderer = new TableRenderer(tableConfig);

    sorter = new TableSorter(appointments, 'date', 'desc', (sortedData) => {
        renderer.render(document.getElementById('listBody'), sortedData, sorter);
        applyCurrentSearch();
    });

    renderCorner(headerWrapper);
    renderListHeaderInfo(headerWrapper, appointments.length);
    renderSearchBar(headerWrapper);

    renderer.render(bodyWrapper, sorter.getSortedData(), sorter);

    window.addEventListener('filtersChanged', (event) => {
        const statuses = event.detail;
        
        const activeStatusIds = statuses
            .filter(s => s.active)
            .map(s => s.id.toLowerCase());

        const filtered = originalAppointments.filter(app => {
            const normalizedStatus = app.status.toLowerCase().replace(/[^a-z]/g, '');
            return activeStatusIds.includes(normalizedStatus);
        });

        sorter.setData(filtered);
        
        renderer.render(document.getElementById('listBody'), sorter.getSortedData(), sorter);
        
        const countEl = document.getElementById('listCount');
        if (countEl) countEl.textContent = `${filtered.length} Appointments`;

        applyCurrentSearch();
    });
}

function applyCurrentSearch() {
    const input = document.getElementById('appointmentSearch');
    if (input && input.value) {
        input.dispatchEvent(new Event('input'));
    }
}

function renderCorner(parent) {
    const corner = document.createElement('div');
    corner.className = 'timeline__header-corner';
    corner.innerHTML = `
        <div class="view-switcher">
            <button class="view-switcher__btn" id="showTimeline"><i class="fa-solid fa-table-columns"></i> Columns</button>
            <button class="view-switcher__btn active" id="showList"><i class="fa-solid fa-list"></i> List</button>
        </div>
    `;
    parent.appendChild(corner);
}

function renderListHeaderInfo(parent, count) {
    const info = document.createElement('div');
    info.className = 'list-view__info-header';
    const now = new Date();
    const dayName = now.toLocaleDateString('en-US', { weekday: 'long' });
    const monthName = now.toLocaleDateString('en-US', { month: 'short' });
    const dayNumber = now.getDate().toString().padStart(2, '0');

    info.innerHTML = `
        <div class="column-info">
            <span class="column-info__date">${dayName}, ${monthName}</span>
            <div class="column-info__stats">
                <i class="fa-regular fa-calendar-check"></i>
                <span id="listCount">${count} Appointments</span>
            </div>
        </div>
        <div class="column-date">${dayNumber}</div>
    `;
    parent.appendChild(info);
}

function renderSearchBar(parent) {
    const searchWrapper = document.createElement('div');
    searchWrapper.className = 'list-view__search-wrapper';
    searchWrapper.innerHTML = `
        <div class="search-container">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="appointmentSearch" placeholder="Filter client, service, or date...">
        </div>
    `;
    parent.appendChild(searchWrapper);
}

function getStatusClass(status) {
    const s = status.toLowerCase();
    const map = {
        'confirmed': 'filter-item--blue',
        'reserved':  'filter-item--yellow',
        'cancelled': 'filter-item--red',
        'no-show':   'filter-item--black',
        'show':      'filter-item--green'
    };
    return map[s] || 'filter-item--black';
}