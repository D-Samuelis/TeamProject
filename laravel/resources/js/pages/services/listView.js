import { TableSorter } from "../../components/table/tableSorter.js";
import { TableRenderer } from "../../components/table/tableRenderer.js";
import { initPaginator } from "../../components/displays/paginator.js";
import { Toast } from "../../components/displays/toast.js";
import { apiFetch } from "../../utils/apiFetch.js";

let sorter = null;
let renderer = null;
let originalData = [];
let activeFilters = null;

export function initServicesListView(data = [], meta = {}) {
    const container = document.getElementById("serviceTableContainer");
    if (!container) return;

    originalData = data;

    updateCounts(originalData);

    const tableConfig = {
        searchId: "#serviceSearchInput",
        rowClass: "service-table__row",
        columns: [
            {
                label: "Service Name",
                key: "name",
                sortable: true,
                searchable: true,
                render: (val, item) => `
                    <div class="name-cell">
                        ${val}
                        ${item.deleted_at ? '<span class="today-badge" style="background: var(--status-red)">Archived</span>' : ""}
                    </div>`,
            },
            {
                label: "Description",
                key: "description",
                sortable: false,
                searchable: true,
                render: (val) =>
                    `<div class="description-cell">${val || "No description"}</div>`,
            },
            {
                label: "Category",
                key: "category",
                sortable: false,
                searchable: true,
                render: (val, item) =>
                    `<div class="description-cell">${item.category?.name || "No category"}</div>`,
            },
            {
                label: "Duration",
                key: "duration_minutes",
                sortable: true,
                searchable: true,
                render: (val) =>
                    `<div class="description-cell">${val ? val + " min" : "No duration"}</div>`,
            },
            {
                label: "Price",
                key: "price",
                sortable: true,
                searchable: true,
                render: (val) =>
                    `<div class="description-cell">${val != null ? val + "€" : "No price"}</div>`,
            },
            {
                label: "Business",
                key: "business",
                sortable: false,
                searchable: true,
                render: (val, item) => {
                    if (!item.business)
                        return `<span class="text-muted">—</span>`;
                    return `
                        <a href="/manage/businesses/${item.business.id}" class="stat-badge stat-badge--service" style="width:fit-content; text-decoration:none;">
                            <i class="fa-solid fa-briefcase"></i>
                            <span>${item.business.name}</span>
                        </a>`;
                },
            },
            {
                label: "Connections",
                key: "id",
                sortable: false,
                searchable: false,
                render: (val, item) => {
                    const branchCount = item.branches?.length ?? 0;
                    const assetCount = item.assets?.length ?? 0;
                    const branchLabel = branchCount === 1 ? "Branch" : "Branches";
                    const assetLabel = assetCount === 1 ? "Asset" : "Assets";

                    return `
                        <div class="stat-badge-group js-open-service-connections"
                             data-id="${item.id}"
                             style="cursor:pointer; display:flex; gap:6px; padding-top: 0px;">
                            <div class="stat-badge stat-badge--branch" title="Branches">
                                <i class="fa-solid fa-location-dot"></i>
                                <span>${String(branchCount).padStart(2, "0")} ${branchLabel}</span>
                            </div>
                            <div class="stat-badge stat-badge--service" title="Assets">
                                <i class="fa-regular fa-gem"></i>
                                <span>${String(assetCount).padStart(2, "0")} ${assetLabel}</span>
                            </div>
                        </div>`;
                },
            },
            {
                label: "Status",
                key: "is_active",
                sortable: false,
                render: (val, item) => {
                    if (item.deleted_at)
                        return `<span class="status-cell filter-item--red">Archived</span>`;
                    return val
                        ? `<span class="status-cell filter-item--green">Active</span>`
                        : `<span class="status-cell filter-item--yellow">Inactive</span>`;
                },
            },
        ],
        renderActions: (item) => {
            if (item.deleted_at) {
                return `
                    <div class="business__actions">
                        <button type="button" class="button-icon button-icon--success js-restore-service-btn" title="Restore" data-id="${item.id}">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>`;
            }

            const toggleIcon = item.is_active ? "fa-eye" : "fa-eye-slash";
            const toggleTitle = item.is_active ? "Deactivate" : "Activate";
            const nextStatus = item.is_active ? 0 : 1;

            return `
                <div class="business__actions">
                    <button type="button" class="button-icon button-icon--warning js-toggle-active-btn"
                            title="${toggleTitle}" data-id="${item.id}" data-next="${nextStatus}">
                        <i class="fa-solid ${toggleIcon}" style="${!item.is_active ? "opacity: 0.5" : ""}"></i>
                    </button>

                    <a href="${window.BE_DATA.routes.show.replace(":id", item.id)}" class="button-icon" title="Settings">
                        <i class="fa-solid fa-gear"></i>
                    </a>

                    <button
                        type="button"
                        class="button-icon button-icon--danger js-archive-service-btn"
                        title="Archive"
                        data-modal-target="archive-service-modal"
                        data-id="${item.id}"
                        data-name="${item.name}"
                        data-business_id="${item.business_id || (item.business ? item.business.id : "")}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>`;
        },
        onRowRender: (tr, item) => {
            if (item.deleted_at) tr.classList.add("is-archived");
        },
    };

    renderer = new TableRenderer(tableConfig);

    const initialData = originalData;

    sorter = new TableSorter(initialData, "name", "asc", (sortedData) => {
        renderer.render(container, sortedData, sorter);
    });

    renderer.render(container, sorter.getSortedData(), sorter);

    // ── Delegated handlers ──────────────────────────────────────────────────

    container.addEventListener("click", async (e) => {
        const restoreBtn = e.target.closest(".js-restore-service-btn");
        if (restoreBtn) {
            await handleRestore(restoreBtn);
            return;
        }

        const toggleBtn = e.target.closest(".js-toggle-active-btn");
        if (toggleBtn) {
            await handleToggleActive(toggleBtn);
        }

        // Archive is handled by initArchiveServiceModal via document listener.
    });

    // ── Filter listener ─────────────────────────────────────────────────────

    window.addEventListener("serviceFiltersChanged", (e) => {
        const statuses = e.detail.statuses;

        const activeFilters = statuses.reduce((acc, s) => {
            acc[s.id] = s.active;
            return acc;
        }, {});

        const filteredData = applyFilters();

        sorter.setData(filteredData);
        renderer.render(container, sorter.getSortedData(), sorter);

        const searchInput = document.querySelector(tableConfig.searchId);
        if (searchInput && searchInput.value) {
            searchInput.dispatchEvent(new Event("input"));
        }
    });

    initPaginator(meta, (page) => {
        const url = new URL(window.location.href);
        url.searchParams.set("page", page);
        window.location.href = url.toString();
    });
}

// ── Action handlers ─────────────────────────────────────────────────────────

async function handleRestore(btn) {
    const id = btn.dataset.id;
    btn.disabled = true;

    try {
        await apiFetch(window.BE_DATA.routes.restore.replace(":id", id), {
            method: "POST",
            body: JSON.stringify({ _method: "PATCH" }),
        });

        sessionStorage.setItem(
            "pending_toast",
            JSON.stringify({
                type: "success",
                title: "Service restored",
                message: "The service is now active again.",
            }),
        );
        window.location.reload();
    } catch (err) {
        Toast.error("Restore failed", err.message);
        btn.disabled = false;
    }
}

async function handleToggleActive(btn) {
    const id = btn.dataset.id;
    const nextStatus = Number(btn.dataset.next);

    const record = originalData.find((s) => String(s.id) === String(id));
    if (!record) return;

    btn.disabled = true;

    try {
        const businessId = record.business_id || record.business?.id;

        const response = await apiFetch(
            window.BE_DATA.routes.update.replace(":id", id),
            {
                method: "POST",
                body: JSON.stringify({
                    _method: "PUT",
                    is_active: nextStatus,
                    business_id: businessId,
                }),
            },
        );

        record.is_active = nextStatus;

        const title = nextStatus ? "Service activated" : "Service deactivated";
        const type = nextStatus ? "success" : "warning";
        const fallback = nextStatus ? 'The service is now inactive and won\'t be bookable.' : 'The service is now active and available for booking.';

        Toast[type](title, response?.message || fallback);

        rerender();
    } catch (err) {
        Toast.error("Update failed", err.message);
        btn.disabled = false;
    }
}

// ── Helpers ─────────────────────────────────────────────────────────────────

function rerender() {
    updateCounts(originalData);
    sorter.setData(applyFilters());

    const container = document.getElementById("serviceTableContainer");
    if (container) renderer.render(container, sorter.getSortedData(), sorter);
}

function applyFilters() {
    if (!activeFilters) {
        return originalData.filter((s) => !s.deleted_at);
    }

    return originalData.filter((item) => {
        if (item.deleted_at) return activeFilters.archived;
        if (item.is_active) return activeFilters.active;
        return activeFilters.inactive;
    });
}

// ── Action handlers ─────────────────────────────────────────────────────────

async function handleRestore(btn) {
    const id = btn.dataset.id;
    btn.disabled = true;

    try {
        const response = await apiFetch(
            window.BE_DATA.routes.restore.replace(":id", id),
            {
                method: "POST",
                body: JSON.stringify({ _method: "PATCH" }),
            },
        );

        const record = originalData.find((s) => String(s.id) === String(id));
        if (record) record.deleted_at = null;

        Toast.success(
            "Service restored",
            response?.message || "The service is now active again.",
        );
        rerender();
    } catch (err) {
        Toast.error("Restore failed", err.message);
        btn.disabled = false;
    }
}

async function handleToggleActive(btn) {
    const id = btn.dataset.id;
    const nextStatus = Number(btn.dataset.next);

    const record = originalData.find((s) => String(s.id) === String(id));
    if (!record) return;

    btn.disabled = true;

    try {
        const businessId = record.business_id || record.business?.id;

        const response = await apiFetch(
            window.BE_DATA.routes.update.replace(":id", id),
            {
                method: "POST",
                body: JSON.stringify({
                    _method: "PUT",
                    is_active: nextStatus,
                    business_id: businessId,
                }),
            },
        );

        record.is_active = nextStatus;

        const title = nextStatus ? "Service activated" : "Service deactivated";
        const type = nextStatus ? "success" : "warning";
        const fallback = nextStatus
            ? "The service is now active."
            : "The service is now inactive.";

        Toast[type](title, response?.message || fallback);

        rerender();
    } catch (err) {
        Toast.error("Update failed", err.message);
        btn.disabled = false;
    }
}

// ── Helpers ─────────────────────────────────────────────────────────────────

function rerender() {
    updateCounts(originalData);
    sorter.setData(applyFilters());

    const container = document.getElementById("serviceTableContainer");
    if (container) renderer.render(container, sorter.getSortedData(), sorter);
}

function applyFilters() {
    if (!activeFilters) {
        return originalData.filter((s) => !s.deleted_at);
    }

    return originalData.filter((item) => {
        if (item.deleted_at) return activeFilters.archived;
        if (item.is_active) return activeFilters.active;
        return activeFilters.inactive;
    });
}

function updateCounts(data) {
    const stats = {
        all: data.length,
        active: data.filter((s) => s.is_active && !s.deleted_at).length,
        inactive: data.filter((s) => !s.is_active && !s.deleted_at).length,
        archived: data.filter((s) => s.deleted_at).length,
    };

    const mapping = {
        countAll: stats.all,
        countActive: stats.active,
        countInactive: stats.inactive,
        countDeleted: stats.archived,
    };

    Object.entries(mapping).forEach(([id, val]) => {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    });
}
