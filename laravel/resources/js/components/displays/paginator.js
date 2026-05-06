export function initPaginator(meta, onPageChange) {
    const container = document.getElementById('paginationContainer');
    if (!container || meta.last_page <= 1) {
        if (container) container.innerHTML = '';
        return;
    }

    renderPages(container, meta, onPageChange);
}

function renderPages(container, meta, onPageChange) {
    container.innerHTML = '';

    const { current_page, last_page } = meta;

    const pages = getPageNumbers(current_page, last_page);

    pages.forEach(page => {
        if (page === '...') {
            const ellipsis = document.createElement('span');
            ellipsis.className = 'pagination__ellipsis';
            ellipsis.textContent = '…';
            container.appendChild(ellipsis);
            return;
        }

        const btn = document.createElement('button');
        btn.className = 'pagination__btn' + (page === current_page ? ' pagination__btn--active' : '');
        btn.textContent = page;
        btn.disabled = page === current_page;
        btn.addEventListener('click', () => onPageChange(page));
        container.appendChild(btn);
    });
}

// Always show first, last, current, and 1 neighbour on each side
function getPageNumbers(current, last) {
    const pages = new Set([1, last, current]);

    if (current - 1 > 1)    pages.add(current - 1);
    if (current + 1 < last) pages.add(current + 1);

    const sorted = [...pages].sort((a, b) => a - b);
    const result = [];

    sorted.forEach((page, i) => {
        if (i > 0 && page - sorted[i - 1] > 1) result.push('...');
        result.push(page);
    });

    return result;
}
