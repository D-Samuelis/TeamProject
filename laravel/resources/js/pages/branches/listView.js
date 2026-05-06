import { TableSorter } from '../../components/table/tableSorter.js';
import { TableRenderer } from '../../components/table/tableRenderer.js';

let sorter = null;
let renderer = null;
let originalData = [];

export function initBranchListView(data = []) {
    const container = document.getElementById('branchTableContainer');
    if (!container) return;

    const urlParams = new URLSearchParams(window.location.search);
    const highlightedBranchId = urlParams.get('branch');

    originalData = data;
    updateBranchCounts(originalData);

    const tableConfig = {
        searchId: '#branchSearchInput',
        rowClass: 'branch-table__row',
        columns: [
            { 
                label: 'Branch Name', key: 'name', sortable: true, searchable: true,
                render: (val, item) => `
                    <div class="name-cell">
                        ${val}
                        ${item.deleted_at ? '<span class="today-badge" style="background: var(--status-red); margin-left: 8px;">Archived</span>' : ''}
                    </div>`
            },
            {
                label: 'City', key: 'city', sortable: true, searchable: true,
                render: (val) => {
                    const city = val || 'No city';
                    return `<div class="description-cell">
                                <i class="fa-solid fa-location-dot" style="font-size: 10px; margin-right: 4px;"></i> 
                                ${city}
                            </div>`;
                }
            },
            {
                label: 'Type', key: 'type', sortable: true, searchable: true,
                render: (val) => `<div class="description-cell">${val ? val.charAt(0).toUpperCase() + val.slice(1) : 'Standard'}</div>`
            },
            { 
                label: 'Business', key: 'business.name', sortable: false, searchable: true,
                render: (val, item) => {
                    if (!item.business) return `<span class="text-muted">—</span>`;
                    return `
                        <a href="/manage/businesses/${item.business.id}?branch=${item.id}" class="stat-badge stat-badge--service" style="width:fit-content; text-decoration:none;">
                            <i class="fa-solid fa-briefcase"></i>
                            <span>${item.business.name}</span>
                        </a>`;
                }
            },
            {
                label: 'Connections', key: 'id', sortable: false, searchable: false,
                render: (val, item) => {
                    const serviceCount = item.services?.length ?? 0;
                    const serviceLabel = serviceCount === 1 ? 'Service' : 'Services';
                    return `
                        <div class="stat-badge-group js-open-branch-connections"
                             data-id="${item.id}"
                             style="cursor:pointer; display:flex; gap:6px; padding-top: 0px;">
                            <div class="stat-badge stat-badge--branch" title="Services">
                                <i class="fa-solid fa-bell-concierge"></i>
                                <span>${String(serviceCount).padStart(2, '0')} ${serviceLabel}</span>
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
                    <div class="business__actions">
                        <form action="${window.BE_DATA.routes.restore.replace(':id', item.id)}" method="POST">
                            <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                            <input type="hidden" name="_method" value="PATCH">
                            <button type="submit" class="button-icon" title="Restore">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </form>
                    </div>`;
            }

            const toggleIcon  = item.is_active ? 'fa-eye' : 'fa-eye-slash';
            const nextStatus  = item.is_active ? 0 : 1;

            return `
                <div class="business__actions">
                    <form action="${window.BE_DATA.routes.update.replace(':id', item.id)}" method="POST">
                        <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="is_active" value="${nextStatus}">
                        <input type="hidden" name="business_id" value="${item.business_id}">
                        <button type="submit" class="button-icon button-icon--warning">
                            <i class="fa-solid ${toggleIcon}" style="${!item.is_active ? 'opacity: 0.5' : ''}"></i>
                        </button>
                    </form>
                    <a href="${window.BE_DATA.routes.show.replace(':id', item.id)}" class="button-icon"><i class="fa-solid fa-gear"></i></a>
                    <button type="button" class="button-icon button-icon--danger js-archive-branch-btn" data-id="${item.id}" data-name="${item.name}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>`;
        },
        onRowRender: (tr, item) => {
            if (item.deleted_at) tr.classList.add('is-archived');
            if (highlightedBranchId && String(item.id) === String(highlightedBranchId)) {
                tr.classList.add('highlighted-row');
                setTimeout(() => tr.scrollIntoView({ behavior: 'smooth', block: 'center' }), 200);
            }
        }
    };

    renderer = new TableRenderer(tableConfig);
    sorter = new TableSorter(originalData, 'name', 'asc', (sortedData) => {
        renderer.render(container, sortedData, sorter);
    });

    renderer.render(container, sorter.getSortedData(), sorter);

    window.addEventListener('branchFiltersChanged', (e) => {
        const activeStatuses = e.detail.statuses.filter(s => s.active).map(s => s.id);
        
        const filteredData = originalData.filter(item => {
            let status = 'inactive';
            if (item.deleted_at) status = 'archived';
            else if (item.is_active) status = 'active';
            
            return activeStatuses.includes(status);
        });

        sorter.data = filteredData; 
        renderer.render(container, sorter.getSortedData(), sorter);
    });
}

function updateBranchCounts(data) {
    const stats = {
        all:      data.length,
        active:   data.filter(b =>  b.is_active && !b.deleted_at).length,
        inactive: data.filter(b => !b.is_active && !b.deleted_at).length,
        archived: data.filter(b =>  b.deleted_at).length
    };
    const mapping = { 'countAll': stats.all, 'countActive': stats.active, 'countInactive': stats.inactive, 'countDeleted': stats.archived };
    Object.entries(mapping).forEach(([id, val]) => {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    });
}