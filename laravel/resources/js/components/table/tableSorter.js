export class TableSorter {
    constructor(data, defaultKey = 'date', defaultDir = 'desc', onSort) {
        this.data = [...data];
        this.onSort = onSort;
        this.defaultKey = defaultKey;
        this.defaultDir = defaultDir;
        
        this.config = {
            key: defaultKey,
            direction: defaultDir
        };
    }

    setData(newData) {
        this.data = [...newData];
        this.sortInternal();
    }

    handleSort(key) {
        if (this.config.key === key) {
            if (this.config.direction === 'asc') {
                this.config.direction = 'desc';
            } else if (this.config.direction === 'desc' && key !== this.defaultKey) {
                this.config.key = this.defaultKey;
                this.config.direction = this.defaultDir;
            } else {
                this.config.direction = 'asc';
            }
        } else {
            this.config.key = key;
            this.config.direction = 'asc';
        }

        this.sortInternal();

        if (this.onSort) {
            this.onSort(this.getSortedData());
        }
    }

    sortInternal() {
        this.data.sort((a, b) => {
            let aVal = a[this.config.key] ?? '';
            let bVal = b[this.config.key] ?? '';

            if (this.config.key === 'date') {
                aVal = new Date(aVal).getTime();
                bVal = new Date(bVal).getTime();
            } 
            else if (!isNaN(aVal) && !isNaN(bVal) && aVal !== '' && bVal !== '') {
                aVal = parseFloat(aVal);
                bVal = parseFloat(bVal);
            } 
            else {
                aVal = aVal.toString().toLowerCase();
                bVal = bVal.toString().toLowerCase();
            }

            if (aVal < bVal) return this.config.direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return this.config.direction === 'asc' ? 1 : -1;
            
            if (this.config.key === 'date' && a.time && b.time) {
                return a.time.localeCompare(b.time);
            }

            return 0;
        });
    }

    getIcon(key) {
        if (this.config.key !== key) return '<i class="fa-solid fa-sort"></i>';
        return this.config.direction === 'asc' 
            ? '<i class="fa-solid fa-sort-up"></i>' 
            : '<i class="fa-solid fa-sort-down"></i>';
    }

    getSortedData() {
        return this.data;
    }

    renderTh(label, key) {
        const activeClass = this.config.key === key ? 'is-active' : '';
        return `
            <th class="sortable-th ${activeClass}" data-sort="${key}">
                <div class="th-content">
                    <span>${label}</span>
                    <span class="sort-icon">${this.getIcon(key)}</span>
                </div>
            </th>
        `;
    }
}