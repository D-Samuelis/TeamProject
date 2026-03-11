import { TableSorter } from '../../components/table/tableSorter.js';
import { TableRenderer } from '../../components/table/tableRenderer.js';

let sorter = null;
let renderer = null;

export function initBusinessListView(data = []) {
    const container = document.getElementById('businessTableContainer');
    if (!container) return;

    const tableConfig = {
        searchId: '#businessSearchInput',
        rowClass: 'appointments-table__row',
        columns: [
            { 
                label: 'Business Name', key: 'name', sortable: true, searchable: true,
                render: (val, item) => `
                    <div class="date-cell">
                        <strong>${val}</strong>
                        ${item.deleted_at ? '<span class="today-badge" style="background: var(--color-red)">Archived</span>' : ''}
                    </div>`
            },
            { 
                label: 'Description', key: 'description', sortable: false, searchable: true,
                render: (val) => `<div class="duration-cell" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${val || 'No description'}</div>`
            },
            { 
                label: 'Status', key: 'is_published', sortable: true, 
                render: (val, item) => {
                    if (item.deleted_at) return `<span class="status-cell filter-item--red">Deleted</span>`;
                    return val 
                        ? `<span class="status-cell filter-item--green">Published</span>`
                        : `<span class="status-cell filter-item--black">Hidden</span>`;
                }
            }
        ],
        renderActions: (item) => {
            if (item.deleted_at) {
                return `
                    <form action="${window.BE_DATA.routes.restore.replace(':id', item.id)}" method="POST">
                        <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                        <button type="submit" class="button-icon" title="Restore"><i class="fa-solid fa-rotate-left"></i></button>
                    </form>`;
            }
            return `
                <div class="d-flex gap-2 justify-content-end">
                    <a href="${window.BE_DATA.routes.show.replace(':id', item.id)}" class="button-icon"><i class="fa-solid fa-gear"></i></a>
                    <form action="${window.BE_DATA.routes.delete.replace(':id', item.id)}" method="POST" onsubmit="return confirm('Archive?')">
                        <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="button-icon button-icon--danger"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </div>`;
        },
        onRowRender: (tr, item) => {
            if (item.deleted_at) tr.classList.add('is-archived');
        }
    };

    renderer = new TableRenderer(tableConfig);
    sorter = new TableSorter(data, 'name', 'asc', (sortedData) => {
        renderer.render(container, sortedData, sorter);
    });

    renderer.render(container, sorter.getSortedData(), sorter);
    
    document.getElementById('businessTotalCount').textContent = data.length;
}