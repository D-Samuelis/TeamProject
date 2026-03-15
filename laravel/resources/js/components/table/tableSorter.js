export class TableSorter {
    constructor(data, defaultKey = 'date', defaultDir = 'desc', onSort) {
        this.originalDataOrder = [...data];
        this.data = [...data];
        this.onSort = onSort;
        this.defaultKey = defaultKey;
        this.defaultDir = defaultDir;
        
        this.statusPriorityIndex = -1;
        this.businessStatuses = ['is_published', 'is_hidden', 'is_deleted'];
        this.appointmentStatuses = ['confirmed', 'reserved', 'show', 'no-show', 'cancelled'];

        this.config = {
            key: defaultKey,
            direction: defaultDir
        };

        this.sortInternal();
    }

    setData(newData) {
        this.data = [...newData];
        this.sortInternal();
    }

    handleSort(key) {
        const isStatusKey = (key === 'status' || key === 'is_published');
        const statuses = key === 'status' ? this.appointmentStatuses : this.businessStatuses;

        if (this.config.key === key) {
            if (isStatusKey) {
                if (this.statusPriorityIndex >= statuses.length - 1) {
                    this.resetToDefault();
                } else {
                    this.statusPriorityIndex++;
                    this.sortInternal();
                }
            } else {
                // Sort in order: ASC -> DESC -> RESET
                if (this.config.direction === 'asc') {
                    this.config.direction = 'desc';
                    this.sortInternal();
                } else if (this.config.direction === 'desc' && key !== this.defaultKey) {
                    this.resetToDefault();
                } else {
                    this.config.direction = 'asc';
                    this.sortInternal();
                }
            }
        } else {
            // handle click on a different column - switch to that column with default direction
            this.config.key = key;
            this.statusPriorityIndex = isStatusKey ? 0 : -1;
            // dates default to desc, others default to asc
            this.config.direction = (key === 'date') ? 'desc' : 'asc';
            this.sortInternal();
        }

        if (this.onSort) this.onSort(this.getSortedData());
    }

    resetToDefault() {
        this.config.key = this.defaultKey;
        this.config.direction = this.defaultDir;
        this.statusPriorityIndex = -1;
        this.sortInternal();
    }

    sortInternal() {
        this.data.sort((a, b) => {
            // Priroty sort for statuses
            if (this.statusPriorityIndex !== -1 && (this.config.key === 'status' || this.config.key === 'is_published')) {
                const isApp = this.config.key === 'status';
                const statusList = isApp ? this.appointmentStatuses : this.businessStatuses;
                const priorityStatus = statusList[this.statusPriorityIndex];

                const getWeight = (item) => {
                    if (isApp) {
                        const s = (item.status || '').toLowerCase().replace(/[^a-z]/g, '');
                        return s === priorityStatus.replace(/[^a-z]/g, '') ? 1 : 0;
                    } else {
                        if (priorityStatus === 'is_deleted') return item.deleted_at ? 1 : 0;
                        if (priorityStatus === 'is_published') return (item.is_published && !item.deleted_at) ? 1 : 0;
                        if (priorityStatus === 'is_hidden') return (!item.is_published && !item.deleted_at) ? 1 : 0;
                        return 0;
                    }
                };

                const weightA = getWeight(a);
                const weightB = getWeight(b);

                if (weightA !== weightB) return weightB - weightA;

                // default sort (by date desc)
                const dateA = new Date(a.date || 0).getTime();
                const dateB = new Date(b.date || 0).getTime();
                return dateB - dateA;
            }

            // standard sort
            let aVal = a[this.config.key] ?? '';
            let bVal = b[this.config.key] ?? '';

            if (this.config.key === 'date') {
                aVal = new Date(aVal || 0).getTime();
                bVal = new Date(bVal || 0).getTime();
            } else if (!isNaN(aVal) && aVal !== '' && !isNaN(parseFloat(aVal))) {
                aVal = parseFloat(aVal);
                bVal = parseFloat(bVal);
            } else {
                aVal = aVal.toString().toLowerCase();
                bVal = bVal.toString().toLowerCase();
            }

            if (aVal < bVal) return this.config.direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return this.config.direction === 'asc' ? 1 : -1;
            
            // date
            if (this.config.key === 'date' && a.time && b.time) {
                return a.time.localeCompare(b.time);
            }

            return 0;
        });
    }

    getIcon(key) {
        if (this.config.key !== key) return '<i class="fa-solid fa-sort" style="opacity: 0.3"></i>';
        
        if (key === 'status' || key === 'is_published') {
            return '<i class="fa-solid fa-arrows-rotate" style="color: var(--color-blue)"></i>';
        }

        return this.config.direction === 'asc' 
            ? '<i class="fa-solid fa-sort-up" style="color: var(--color-blue)"></i>' 
            : '<i class="fa-solid fa-sort-down" style="color: var(--color-blue)"></i>';
    }

    getSortedData() {
        return this.data;
    }

    renderTh(label, key) {
        const isActive = this.config.key === key;
        const activeClass = isActive ? 'is-active' : '';
        let statusLabel = '';

        if (isActive && this.statusPriorityIndex !== -1) {
            const list = (key === 'status') ? this.appointmentStatuses : this.businessStatuses;
            statusLabel = ` <span style="font-size: 0.75em; font-weight: 400; opacity: 0.7">(${list[this.statusPriorityIndex].replace('is_', '')})</span>`;
            console.log('Active status label:', statusLabel);
        }

        return `
            <th class="sortable-th ${activeClass}" data-sort="${key}">
                <div class="th-content">
                    <span>${label}${statusLabel}</span>
                    <span class="sort-icon">${this.getIcon(key)}</span>
                </div>
            </th>
        `;
    }
}