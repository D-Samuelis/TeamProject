import { TableSorter } from '../../components/table/tableSorter.js';
import { TableRenderer } from '../../components/table/tableRenderer.js';

export function initAppointmentListView(data = []) {
    const container = document.getElementById('appointmentTableContainer');
    if (!container) return;

    const tableConfig = {
        searchId: '#appointmentSearchInput',
        rowClass: 'appointment-table__row',
        columns: [
            { 
                label: 'Date', key: 'date', sortable: true, searchable: true,
                render: (val) => `<strong>${new Date(val).toDateString([])}</strong>`
            },
            { 
                label: 'Time', key: 'start_at', sortable: true, searchable: true,
                render: (val) => `<strong>${new Date(val).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</strong>`
            },
            { 
                label: 'Service', key: 'service_name', sortable: true, searchable: true,
                render: (val, item) => `<span>${item.service?.name || 'N/A'}</span>`
            },
            { 
                label: 'Deruation', key: 'duration', sortable: true, searchable: true,
                render: (val) => `<span>${val || 'N/A'}m</span>`
            },
            { 
                label: 'Status', key: 'status', sortable: true, 
                render: (val) => `<span class="status-cell status--${val}">${val}</span>`
            }
        ],
        renderActions: (item) => `
            <div class="business__actions">
                
                <button class="button-icon button-icon--danger"><i class="fa-solid fa-trash"></i></button>
            </div>`
    };

    const renderer = new TableRenderer(tableConfig);
    const sorter = new TableSorter(data, 'start_at', 'asc', (sortedData) => {
        renderer.render(container, sortedData, sorter);
    });

    renderer.render(container, sorter.getSortedData(), sorter);
}