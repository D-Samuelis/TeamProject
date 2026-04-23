import { TableSorter } from '../../components/table/tableSorter.js';
import { TableRenderer } from '../../components/table/tableRenderer.js';

let sorter = null;
let renderer = null;
let originalData = [];

export function initServicesListView(data = []) {
    const container = document.getElementById('serviceTableContainer');
    if (!container) return;

    originalData = data;

    const businessInfo = data[0]?.business ?? null;

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
                label: 'Duration', key: 'duration_minutes', sortable: true, searchable: true,
                render: (val) => `<div class="description-cell">${val ? val + ' min' : 'No duration'}</div>`
            },
            { 
                label: 'Price', key: 'price', sortable: true, searchable: true,
                render: (val) => `<div class="description-cell">${val != null ? val + '€' : 'No price'}</div>`
            },
            {
                label: 'Business', key: 'business', sortable: false, searchable: true,
                render: (val, item) => {
                    if (!item.business) return `<span class="text-muted">—</span>`;
                    return `
                        <a href="/manage/businesses/${item.business.id}" class="stat-badge stat-badge--service" style="width:fit-content; text-decoration:none;">
                            <i class="fa-solid fa-briefcase"></i>
                            <span>${item.business.name}</span>
                        </a>`;
                }
            },
            {
                label: 'Connections', key: 'id', sortable: false, searchable: false,
                render: (val, item) => {
                    const branchCount = item.branches?.length ?? 0;
                    const assetCount  = item.assets?.length  ?? 0;
                    const branchLabel = branchCount === 1 ? 'Branch'  : 'Branches';
                    const assetLabel  = assetCount  === 1 ? 'Asset'   : 'Assets';

                    return `
                        <div class="stat-badge-group js-open-service-connections"
                             data-id="${item.id}"
                             style="cursor:pointer; display:flex; gap:6px; padding-top: 0px;">
                            <div class="stat-badge stat-badge--branch" title="Branches">
                                <i class="fa-solid fa-location-dot"></i>
                                <span>${String(branchCount).padStart(2, '0')} ${branchLabel}</span>
                            </div>
                            <div class="stat-badge stat-badge--service" title="Assets">
                                <i class="fa-regular fa-gem"></i>
                                <span>${String(assetCount).padStart(2, '0')} ${assetLabel}</span>
                            </div>
                        </div>`;
                }
            },
            { 
                label: 'Status', key: 'is_active', sortable: true, 
                render: (val, item) => {
                    if (item.deleted_at) return `<span class="status-cell filter-item--red">Archived</span>`;
                    return val 
                        ? `<span class="status-cell filter-item--green">Active</span>`
                        : `<span class="status-cell filter-item--yellow">Inactive</span>`;
                }
            }
        ],
        renderActions: (item) => {
            if (item.deleted_at) {
                return `
                    <form action="${window.BE_DATA.routes.restore.replace(':id', item.id)}" method="POST">
                        <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                        <input type="hidden" name="_method" value="PATCH">
                        <button type="submit" class="button-icon" title="Restore">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </form>`;
            }

            const toggleIcon  = item.is_active ? 'fa-eye' : 'fa-eye-slash';
            const toggleTitle = item.is_active ? 'Deactivate' : 'Activate';
            const nextStatus  = item.is_active ? 0 : 1;

            return `
                <div class="business__actions">
                    <form action="${window.BE_DATA.routes.update.replace(':id', item.id)}" method="POST">
                        <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="is_active" value="${nextStatus}">
                        <button type="submit" class="button-icon button-icon--warning" title="${toggleTitle}">
                            <i class="fa-solid ${toggleIcon}" style="${!item.is_active ? 'opacity: 0.5' : ''}"></i>
                        </button>
                    </form>

                    <a href="${window.BE_DATA.routes.show.replace(':id', item.id)}" class="button-icon" title="Settings">
                        <i class="fa-solid fa-gear"></i>
                    </a>
                    
                    <button 
                        type="button" 
                        class="button-icon button-icon--danger js-archive-service-btn" 
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
    
    const initialData = originalData;
    
    sorter = new TableSorter(initialData, 'name', 'asc', (sortedData) => {
        renderer.render(container, sortedData, sorter);
    });

    renderer.render(container, sorter.getSortedData(), sorter);

    window.addEventListener('serviceFiltersChanged', (event) => {
        const statuses = event.detail.statuses;
        
        const activeFilters = statuses.reduce((acc, s) => {
            acc[s.id] = s.active;
            return acc;
        }, {});

        const filteredData = originalData.filter(item => {
            if (item.deleted_at) return activeFilters.archived;
            if (item.is_active)  return activeFilters.active;
            return activeFilters.inactive;
        });

        sorter.setData(filteredData);
        renderer.render(container, sorter.getSortedData(), sorter);
        updateCounts(filteredData);
        
        const searchInput = document.querySelector(tableConfig.searchId);
        if (searchInput && searchInput.value) {
            searchInput.dispatchEvent(new Event('input'));
        }
    });
}

function updateCounts(data) {
    const stats = {
        all:      data.length,
        active:   data.filter(b =>  b.is_active && !b.deleted_at).length,
        inactive: data.filter(b => !b.is_active && !b.deleted_at).length,
        archived: data.filter(b =>  b.deleted_at).length
    };

    updateStatElement('countAll',      stats.all);
    updateStatElement('countActive',   stats.active);
    updateStatElement('countInactive', stats.inactive);
    updateStatElement('countDeleted',  stats.archived);
}

function updateStatElement(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}