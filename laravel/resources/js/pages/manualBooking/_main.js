import { initListSearch } from '../../components/table/searchBar.js';
import { TableSorter } from '../../components/table/tableSorter.js';
import { TableRenderer } from '../../components/table/tableRenderer.js';
import { Modal } from '../../components/displays/modal.js';

function escapeHtml(value) {
    if (value === null || value === undefined) return '';

    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

export function initPublicBusinessDetail() {
    const pageData = window.PUBLIC_BUSINESS_DETAIL_DATA;
    if (!pageData || !pageData.branches) return;

    initBranchNavigation();
    initBranchTables(pageData.branches);
}

function initBranchNavigation() {
    const branchLinks = document.querySelectorAll('.public-business-detail__branch-link');
    const branchPanels = document.querySelectorAll('.public-business-detail__branch-panel');

    branchLinks.forEach(link => {
        link.addEventListener('click', () => {
            const branchId = link.dataset.branchId;
 
            branchLinks.forEach(item => item.classList.remove('is-active'));
            branchPanels.forEach(panel => panel.classList.remove('is-active'));
 
            link.classList.add('is-active');
 
            const activePanel = document.querySelector(`[data-branch-panel="${branchId}"]`);
            if (activePanel) {
                activePanel.classList.add('is-active');
            }
 
            const url = new URL(window.location.href);
            url.searchParams.set('branch_id', branchId);
            window.history.replaceState({}, '', url);
        });
    });
}

function initBranchTables(branches) {
    branches.forEach(branch => {
        const container = document.getElementById(`publicBusinessDetailTable-${branch.id}`);
        if (!container) return;

        const tableConfig = {
            tableClass: 'public-business-detail__table',
            rowClass: 'public-business-detail__row',
            searchId: `#publicBusinessDetailSearch-${branch.id}`,
            columns: [
                {
                    label: 'Service',
                    key: 'name',
                    sortable: true,
                    searchable: true,
                    render: (val) => `
                        <div class="public-business-detail__service-cell">
                            <strong>${escapeHtml(val)}</strong>
                        </div>
                    `
                },
                {
                    label: 'Description',
                    key: 'description',
                    sortable: false,
                    searchable: true,
                    render: (val) => `
                        <div class="public-business-detail__description-cell">
                            ${
                                val
                                    ? `<span class="public-business-detail__description-clamp">${escapeHtml(val)}</span>`
                                    : `<span class="public-business-detail__muted">No description</span>`
                            }
                        </div>
                    `
                },
                {
                    label: 'Duration',
                    key: 'duration_minutes',
                    sortable: true,
                    searchable: false,
                    render: (val) => `${val} min`
                },
                {
                    label: 'Price',
                    key: 'price',
                    sortable: true,
                    searchable: false,
                    render: (val) => `${Number(val).toFixed(2)} €`
                },
                {
                    label: 'Location',
                    key: 'location_label',
                    sortable: true,
                    searchable: true,
                    render: (val) => escapeHtml(val)
                }
            ],
            renderActions: (item) => `
                <div class="public-business-detail__actions-wrap">
                    <button
                        type="button"
                        class="public-business-detail__action-btn public-business-detail__action-btn--icon js-service-info-btn"
                        data-branch-id="${branch.id}"
                        data-service-id="${item.id}"
                        aria-label="Service info"
                        title="Service info"
                    >
                        <span class="public-business-detail__info-circle">i</span>
                    </button>

                    <a
                        href="${item.book_url}"
                        class="public-business-detail__action-btn public-business-detail__action-btn--primary"
                    >
                        Book
                    </a>
                </div>
            `
        };

        const renderer = new TableRenderer(tableConfig);
        const sorter = new TableSorter(branch.services, 'name', 'asc', (sortedData) => {
            renderer.render(container, sortedData, sorter);
            bindInfoButtons(branch);
        });

        renderer.render(container, sorter.getSortedData(), sorter);

        initListSearch(
            tableConfig.searchId,
            `#publicBusinessDetailTable-${branch.id} .${tableConfig.rowClass}`,
            '.js-search-data'
        );

        bindInfoButtons(branch);
    });
}

function bindInfoButtons(branch) {
    const container = document.getElementById(`publicBusinessDetailTable-${branch.id}`);
    if (!container) return;

    container.querySelectorAll('.js-service-info-btn').forEach(button => {
        if (button.dataset.bound === 'true') return;

        button.dataset.bound = 'true';
        button.addEventListener('click', () => {
            const serviceId = Number(button.dataset.serviceId);
            const service = branch.services.find(item => Number(item.id) === serviceId);
            if (!service) return;

            Modal.showCustom({
                title: service.name,
                type: 'Service Info',
                action: 'info',
                confirmText: 'Book',
                cancelText: 'Close',
                body: `
    <div class="public-business-detail__modal-info">
        <div class="public-business-detail__modal-row">
            <div class="public-business-detail__modal-label">Description</div>
            <div class="public-business-detail__modal-value">
                ${service.description ? escapeHtml(service.description) : 'No description available.'}
            </div>
        </div>

        <div class="public-business-detail__modal-row">
            <div class="public-business-detail__modal-label">Duration</div>
            <div class="public-business-detail__modal-value">
                ${service.duration_minutes} min
            </div>
        </div>

        <div class="public-business-detail__modal-row">
            <div class="public-business-detail__modal-label">Price</div>
            <div class="public-business-detail__modal-value">
                ${Number(service.price).toFixed(2)} €
            </div>
        </div>

        <div class="public-business-detail__modal-row">
            <div class="public-business-detail__modal-label">Location Type</div>
            <div class="public-business-detail__modal-value">
                ${escapeHtml(service.location_label)}
            </div>
        </div>

        <div class="public-business-detail__modal-row">
            <div class="public-business-detail__modal-label">Branch</div>
            <div class="public-business-detail__modal-value">
                ${escapeHtml(service.branch_name)}
            </div>
        </div>

        <div class="public-business-detail__modal-row">
            <div class="public-business-detail__modal-label">Address</div>
            <div class="public-business-detail__modal-value">
                ${service.branch_address ? escapeHtml(service.branch_address) : 'No address available.'}
            </div>
        </div>
    </div>
`,
                onConfirm: () => {
                    window.location.href = service.book_url;
                }
            });
        });
    });
}


export function initBookingSearch() {
    const searchInput = document.querySelector('#bookingSearch');
    const bookingGrid = document.querySelector('.booking-grid');

    if (!searchInput || !bookingGrid) return;

    initListSearch(
        '#bookingSearch',
        '.booking-grid .card-link',
        '.js-search-data'
    );
}