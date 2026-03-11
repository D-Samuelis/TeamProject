import { TableSorter } from '../../components/sort/sortTable.js';
import { initListSearch } from '../../components/sort/searchBar.js';

/**
 * Global sorter instance to manage table state
 * @type {TableSorter|null}
 */
let sorter = null;

/**
 * Main entry point for the list layout
 * @param {Array} appointments - Array of appointment objects
 */
export function initListView(appointments = []) {
    const listView = document.getElementById('listView');
    if (!listView) return;

    listView.innerHTML = '';
    
    const headerWrapper = document.createElement('div');
    headerWrapper.className = 'list-view__header-wrapper';
    
    const bodyWrapper = document.createElement('div');
    bodyWrapper.className = 'list-view__body-wrapper';
    bodyWrapper.id = 'listBody';

    listView.appendChild(headerWrapper);
    listView.appendChild(bodyWrapper);


    sorter = new TableSorter(appointments, 'date', 'desc', (sortedData) => {
        const body = document.getElementById('listBody');
        renderTable(body, sortedData);
        applyCurrentSearch();
    });

    renderCorner(headerWrapper);
    renderListHeaderInfo(headerWrapper, appointments.length);
    renderSearchBar(headerWrapper);

    renderTable(bodyWrapper, sorter.getSortedData());
}


function applyCurrentSearch() {
    const input = document.getElementById('appointmentSearch');
    if (input && input.value) {
        input.dispatchEvent(new Event('input'));
    }
}

/**
 * Renders the top-left corner with view switcher
 */
function renderCorner(parent) {
    const corner = document.createElement('div');
    corner.className = 'timeline__header-corner';
    corner.innerHTML = `
        <div class="view-switcher">
            <button class="view-switcher__btn" id="showTimeline">
                <i class="fa-solid fa-table-columns"></i> Columns
            </button>
            <button class="view-switcher__btn active" id="showList">
                <i class="fa-solid fa-list"></i> List
            </button>
        </div>
    `;
    parent.appendChild(corner);
}

/**
 * Renders the date info in the header
 */
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

/**
 * Renders the Search Bar using the General Utility
 * @param {HTMLElement} parent
 */
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

    setTimeout(() => {
        initListSearch('#appointmentSearch', '.appointments-table__row', '.js-search-data');
    }, 0);
}

/**
 * Maps status string to CSS class
 */
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

/**
 * Renders the table into the scrollable body
 * @param {HTMLElement} parent
 * @param {Array} appointments - Sorted appointments
 */
function renderTable(parent, appointments) {
    parent.innerHTML = '';
    const tableContainer = document.createElement('div');
    tableContainer.className = 'list-view__table-container';

    const todayStr = new Date().toLocaleDateString('en-US', { 
        month: 'short', day: 'numeric', year: 'numeric' 
    });

    if (appointments.length === 0) {
        tableContainer.innerHTML = `<div class="list-empty"><p>No appointments found</p></div>`;
    } else {
        tableContainer.innerHTML = `
            <table class="appointments-table">
                <thead>
                    <tr>
                        ${sorter.renderTh('Date', 'date')}
                        ${sorter.renderTh('Time', 'time')}
                        ${sorter.renderTh('Duration', 'duration')}
                        ${sorter.renderTh('Service', 'service')}
                        ${sorter.renderTh('Status', 'status')}
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="listTableBody">
                    ${appointments.map(app => {
                        const isToday = app.date === todayStr;
                        return `
                            <tr class="appointments-table__row ${isToday ? 'is-today' : ''}">
                                <td class="js-search-data">
                                    <div class="date-cell">
                                        ${app.date}
                                        ${isToday ? '<span class="today-badge">Today</span>' : ''}
                                    </div>
                                </td>
                                <td class="js-search-data"><div class="time-cell">${app.time}</div></td>
                                <td class="js-search-data"><div class="duration-cell">${app.duration}</div></td>
                                <td class="js-search-data"><span class="service-cell">${app.service}</span></td>
                                <td>
                                    <span class="status-cell ${getStatusClass(app.status)}">
                                        ${app.status}
                                    </span>
                                </td>
                                <td class="controls-cell text-right">
                                    <button class="button-icon"><i class="fa-solid fa-pen"></i></button>
                                    <button class="button-icon button-icon--danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        `;
    }
    parent.appendChild(tableContainer);

    parent.querySelectorAll('.sortable-th').forEach(th => {
        th.addEventListener('click', () => {
            sorter.handleSort(th.dataset.sort);
        });
    });
}