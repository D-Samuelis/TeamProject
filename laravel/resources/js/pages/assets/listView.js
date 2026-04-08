import { TableSorter } from '../../components/table/tableSorter.js';
import { TableRenderer } from '../../components/table/tableRenderer.js';

let sorter = null;
let renderer = null;
let originalData = [];

export function initAssetListView(data = []) {
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
                render: (val) => `
                    <div class="name-cell">
                        ${val}
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
                    const bCount = item.branches?.length || 0;
                    const sCount = item.services?.length || 0;
                    
                    return `
                        <div class="stat-badge-group js-open-connections" data-id="${item.id}" style="cursor:pointer; display: flex; gap: 6px;">
                            <div class="stat-badge stat-badge--branch" title="Connected Branches">
                                <i class="fa-solid fa-code-branch"></i>
                                <span>${String(bCount).padStart(2, '0')}</span>
                            </div>
                            <div class="stat-badge stat-badge--service" title="Connected Services">
                                <i class="fa-solid fa-bell-concierge"></i>
                                <span>${String(sCount).padStart(2, '0')}</span>
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
            return `
                <div class="business__actions">
                    <a href="${window.BE_DATA.routes.show.replace(':id', item.id)}" class="button-icon" title="Settings">
                        <i class="fa-solid fa-gear"></i>
                    </a>
                    
                    <button 
                        type="button" 
                        class="button-icon button-icon--danger js-delete-asset-btn" 
                        title="Delete Asset"
                        data-id="${item.id}"
                        data-name="${item.name}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>`;
        }
    };

    renderer = new TableRenderer(tableConfig);
    
    sorter = new TableSorter(originalData, 'name', 'asc', (sortedData) => {
        renderer.render(container, sortedData, sorter);
    });

    renderer.render(container, sorter.getSortedData(), sorter);
}

function updateAssetCounts(data) {
    const stats = {
        all: data.length,
        published: data.length,
        hidden: 0,
        deleted: 0
    };

    const mapping = {
        'countAll': stats.all,
        'countPublished': stats.published,
        'countHidden': stats.hidden,
        'countDeleted': stats.deleted
    };

    Object.entries(mapping).forEach(([id, val]) => {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    });
}