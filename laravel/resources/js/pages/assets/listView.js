import { TableSorter } from '../../components/table/tableSorter.js';
import { TableRenderer } from '../../components/table/tableRenderer.js';
import {initPaginator} from "../../components/displays/paginator.js";

let sorter = null;
let renderer = null;
let originalData = [];

export function initAssetListView(data = [], meta = {}) {
    const container = document.getElementById('assetTableContainer');
    if (!container) return;

    originalData = data;

    updateAssetCounts(originalData);

    const tableConfig = {
        searchId: '#assetSearchInput',
        rowClass: 'asset-table__row',
        columns: [
            {
                label: 'Asset Name', key: 'name', sortable: true, searchable: true,
                render: (val, item) => `
                    <div class="name-cell">
                        ${val}
                        ${item.deleted_at ? '<span class="today-badge" style="background: var(--status-red); margin-left: 8px;">Archived</span>' : ''}
                    </div>`
            },
            {
                label: 'Description', key: 'description', sortable: false, searchable: true,
                render: (val) => `<div class="description-cell">${val || 'No description'}</div>`
            },
            {
                label: 'Connections',
                key: 'id',
                sortable: false,
                render: (val, item) => {
                    const sCount = item.services?.length || 0;
                    const serviceLabel = sCount === 1 ? 'Service' : 'Services';

                    return `
                        <div class="stat-badge-group js-open-connections" data-id="${item.id}" style="cursor:pointer; display: flex; gap: 6px;">
                            <div class="stat-badge stat-badge--service" title="${item.branch ? item.branch.name : 'No Branch'}">
                                <i class="fa-solid fa-bell-concierge"></i>
                                <span>${String(sCount).padStart(2, '0')} ${serviceLabel}</span>
                            </div>
                        </div>`;
                }
            },
            {
                label: 'Rules',
                key: 'rules',
                sortable: false,
                render: (val, item) => {
                    const rules = item.rules || [];
                    if (rules.length === 0) return `<span class="text-muted">No active rules</span>`;

                    const d = new Date();
                    const today = [d.getFullYear(), String(d.getMonth() + 1).padStart(2, '0'), String(d.getDate()).padStart(2, '0')].join('-');

                    const activeRule = [...rules]
                        .sort((a, b) => (a.priority || 0) - (b.priority || 0))
                        .find(r => {
                            const from = r.valid_from ? r.valid_from.substring(0, 10) : null;
                            const to = r.valid_to ? r.valid_to.substring(0, 10) : null;
                            return (!from || today >= from) && (!to || today <= to);
                        });

                    if (!activeRule) return `<span class="text-muted">No rule for today</span>`;

                    return `
                        <div class="active-rule-trigger js-open-rule-detail" data-id="${item.id}" style="cursor:pointer;">
                            <div class="stat-badge stat-badge--rule">
                                <i class="fa-regular fa-clock"></i>
                                <span>${activeRule.title}</span>
                            </div>
                        </div>`;
                }
            }
        ],
        renderActions: (item) => {
            if (item.deleted_at) {
                return `
                    <div class="business__actions">
                        <form action="${window.BE_DATA.routes.restoreAsset.replace(':id', item.id)}" method="POST">
                            <input type="hidden" name="_token" value="${window.BE_DATA.csrf}">
                            <input type="hidden" name="_method" value="POST">
                            <button type="submit" class="button-icon" title="Restore Asset">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </form>
                    </div>`;
            }

            return `
                <div class="business__actions">
                    <a href="${window.BE_DATA.routes.show.replace(':id', item.id)}" class="button-icon" title="Settings">
                        <i class="fa-solid fa-gear"></i>
                    </a>

                    <button
                        type="button"
                        class="button-icon button-icon--danger js-archive-asset-btn"
                        title="Delete Asset"
                        data-modal-target="archive-asset-modal"
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

    sorter = new TableSorter(originalData, 'name', 'asc', (sortedData) => {
        renderer.render(container, sortedData, sorter);
    });

    renderer.render(container, sorter.getSortedData(), sorter);

    window.addEventListener('assetFiltersChanged', (event) => {
        const statuses = event.detail.statuses;
        const activeFilters = statuses.reduce((acc, s) => {
            acc[s.id] = s.active;
            return acc;
        }, {});

        const filteredData = originalData.filter(item => {
            if (item.deleted_at) return activeFilters.deleted;
            return activeFilters.published;
        });

        sorter.setData(filteredData);
        renderer.render(container, sorter.getSortedData(), sorter);
    });

    initPaginator(meta, (page) => {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    });
}

function updateAssetCounts(data) {
    const stats = {
        all: data.length,
        published: data.filter(a => !a.deleted_at).length,
        deleted: data.filter(a => a.deleted_at).length
    };

    const mapping = {
        'countAll': stats.all,
        'countPublished': stats.published,
        'countDeleted': stats.deleted
    };

    Object.entries(mapping).forEach(([id, val]) => {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    });
}
