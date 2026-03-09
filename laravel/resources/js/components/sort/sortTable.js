export class TableSorter {
    /**
     * @param {Array} data - Array of objects to sort
     * @param {string} defaultKey - Key used for the initial/default sort
     * @param {string} defaultDir - Default direction ('asc' or 'desc')
     * @param {Function} onSort - Callback function to re-render UI
     */
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

    /**
     * Main method for handling sort clicks with a 3-step toggle:
     * 1. ASC (or Default) -> 2. DESC (or Alternate) -> 3. RESET TO DEFAULT
     * @param {string} key - Key from dataset
     */
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

        if (this.onSort) {
            this.onSort(this.getSortedData());
        }
    }

    /**
     * Returns the appropriate FontAwesome icon based on state
     * @param {string} key 
     * @returns {string} HTML string
     */
    getIcon(key) {
        if (this.config.key !== key) return '<i class="fa-solid fa-sort"></i>';
        return this.config.direction === 'asc' 
            ? '<i class="fa-solid fa-sort-up"></i>' 
            : '<i class="fa-solid fa-sort-down"></i>';
    }

    /**
     * Core sorting logic
     * @returns {Array} Sorted array
     */
    getSortedData() {
        return this.data.sort((a, b) => {
            let aVal = a[this.config.key] ?? '';
            let bVal = b[this.config.key] ?? '';

            if (!isNaN(aVal) && !isNaN(bVal) && aVal !== '' && bVal !== '') {
                aVal = parseFloat(aVal);
                bVal = parseFloat(bVal);
            } else {
                aVal = aVal.toString().toLowerCase();
                bVal = bVal.toString().toLowerCase();
            }

            if (aVal < bVal) return this.config.direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return this.config.direction === 'asc' ? 1 : -1;
            
            if (this.config.key !== 'time' && a.time && b.time) {
                return a.time.localeCompare(b.time);
            }

            return 0;
        });
    }

    /**
     * Renders a sortable table header
     * @param {string} label - Display name
     * @param {string} key - Data key
     * @returns {string} HTML string
     */
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