import { TableSorter } from '../../components/table/tableSorter.js';
import { TableRenderer } from '../../components/table/tableRenderer.js';

let sorter = null;
let renderer = null;
let originalData = [];

export function initServicesListView(data = []) {
    const container = document.getElementById('serviceTableContainer');
    if (!container) return;

    originalData = data;

    updateCounts(originalData);

    const tableConfig = {
        searchId: '#serviceSearchInput',
        rowClass: 'service-table__row',
        columns: [
            { 
                label: 'Service Name', key: 'name', sortable: true, searchable: true,
                render: (val, item) => `
                    <div class="name-cell">
                        ${val}
                        ${item.deleted_at ? '<span class="today-badge" style="background: var(--status-red)">Archived</span>' : ''}
                    </div>`
            },
            { 
                label: 'Description', key: 'description', sortable: false, searchable: true,
                render: (val) => `<div class="description-cell">${val || 'No description'}</div>`
            },
            { 
                label: 'Duration', key: 'duration_minutes', sortable: false, searchable: true,
                render: (val) => `<div class="description-cell">${val + " min" || 'No duration'}</div>`
            },
            { 
                label: 'Price', key: 'price', sortable: false, searchable: true,
                render: (val) => `<div class="description-cell">${val + '€' || 'No price'}</div>`
            },
            { 
                label: 'Status', key: 'is_published', sortable: true, 
                render: (val, item) => {
                    if (item.deleted_at) return `<span class="status-cell filter-item--red">Deleted</span>`;
                    return val 
                        ? `<span class="status-cell filter-item--green">Published</span>`
                        : `<span class="status-cell filter-item--yellow">Hidden</span>`;
                }
            }
        ],
        renderActions: (item) => {
            if (item.deleted_at) {
                return `
                    <form action="${window.BE_DATA.routes.restore.replace(':id', item.id)}" method="POST">
                        <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                        <input type="hidden" name="_method" value="PATCH">
                        <button type="submit" class="button-icon" title="Restore"><i class="fa-solid fa-rotate-left"></i></button>
                    </form>`;
            }

            const toggleIcon = item.is_published ? 'fa-eye' : 'fa-eye-slash';
            const toggleTitle = item.is_published ? 'Hide Business' : 'Publish Business';
            const nextStatus = item.is_published ? 0 : 1;

            return `
                <div class="business__actions">
                    <form action="${window.BE_DATA.routes.update.replace(':id', item.id)}" method="POST">
                        <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="is_published" value="${nextStatus}">
                        <button type="submit" class="button-icon button-icon--warning" title="${toggleTitle}">
                            <i class="fa-solid ${toggleIcon}" style="${!item.is_published ? 'opacity: 0.5' : ''}"></i>
                        </button>
                    </form>

                    <a href="${window.BE_DATA.routes.show.replace(':id', item.id)}" class="button-icon" title="Settings">
                        <i class="fa-solid fa-gear"></i>
                    </a>
                    
                    <button 
                        type="button" 
                        class="button-icon button-icon--danger js-archive-business-btn" 
                        title="Archive"
                        data-id="${item.id}"
                        data-name="${item.name}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>`;
        },
        onRowRender: (tr, item) => {
            if (item.deleted_at) tr.classList.add('is-archived');
        }
    };

    renderer = new TableRenderer(tableConfig);
    
    const initialData = originalData.filter(b => !b.deleted_at);
    
    sorter = new TableSorter(initialData, 'name', 'asc', (sortedData) => {
        renderer.render(container, sortedData, sorter);
    });

    renderer.render(container, sorter.getSortedData(), sorter);

    window.addEventListener('businessFiltersChanged', (event) => {
        const statuses = event.detail.statuses;
        
        const activeFilters = statuses.reduce((acc, s) => {
            acc[s.id] = s.active;
            return acc;
        }, {});

        const filteredData = originalData.filter(item => {
            if (item.deleted_at) return activeFilters.deleted;
            if (item.is_published) return activeFilters.published;
            return activeFilters.hidden;
        });

        sorter.setData(filteredData);
        renderer.render(container, sorter.getSortedData(), sorter);
        
        const searchInput = document.querySelector(tableConfig.searchId);
        if (searchInput && searchInput.value) {
            searchInput.dispatchEvent(new Event('input'));
        }
    });
}

/**
 * Update the counts in the header based on the current dataset
 */
function updateCounts(data) {
    const stats = {
        all: data.length,
        published: data.filter(b => b.is_published && !b.deleted_at).length,
        hidden: data.filter(b => !b.is_published && !b.deleted_at).length,
        deleted: data.filter(b => b.deleted_at).length
    };

    updateStatElement('countAll', stats.all);
    updateStatElement('countPublished', stats.published);
    updateStatElement('countHidden', stats.hidden);
    updateStatElement('countDeleted', stats.deleted);
}

function updateStatElement(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}