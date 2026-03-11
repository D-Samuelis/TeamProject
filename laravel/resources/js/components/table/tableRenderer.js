import { initListSearch } from './searchBar.js';

export class TableRenderer {
    /**
     * @param {Object} config - config for table
     */
    constructor(config) {
        this.config = {
            tableClass: 'appointments-table',
            rowClass: 'appointments-table__row',
            ...config
        };
    }

    /**
     * Renders table
     * @param {HTMLElement} container - where to draw table...
     * @param {Array} data
     * @param {Object} sorter
     */
    render(container, data, sorter = null) {
        if (!container) return;
        container.innerHTML = '';

        if (!data || data.length === 0) {
            container.innerHTML = `<div class="list-empty"><p>No results found.</p></div>`;
            return;
        }

        const table = document.createElement('table');
        table.className = this.config.tableClass;

        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');
        
        this.config.columns.forEach(col => {
            if (sorter && col.sortable) {
                headerRow.innerHTML += sorter.renderTh(col.label, col.key);
            } else {
                headerRow.innerHTML += `<th>${col.label}</th>`;
            }
        });

        if (this.config.renderActions) {
            headerRow.innerHTML += `<th class="text-right">Actions</th>`;
        }
        thead.appendChild(headerRow);
        table.appendChild(thead);

        const tbody = document.createElement('tbody');
        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.className = this.config.rowClass;
            
            if (this.config.onRowRender) {
                this.config.onRowRender(tr, item);
            }

            this.config.columns.forEach(col => {
                const td = document.createElement('td');
                if (col.searchable) td.className = 'js-search-data';
                
                td.innerHTML = col.render ? col.render(item[col.key], item) : (item[col.key] || '');
                tr.appendChild(td);
            });

            if (this.config.renderActions) {
                const tdActions = document.createElement('td');
                tdActions.className = 'controls-cell text-right';
                tdActions.innerHTML = this.config.renderActions(item);
                tr.appendChild(tdActions);
            }

            tbody.appendChild(tr);
        });

        table.appendChild(tbody);
        container.appendChild(table);

        if (sorter) {
            container.querySelectorAll('.sortable-th').forEach(th => {
                th.addEventListener('click', () => {
                    sorter.handleSort(th.dataset.sort);
                });
            });
        }

        if (this.config.searchId) {
            initListSearch(this.config.searchId, `.${this.config.rowClass}`, '.js-search-data');
        }
    }
}