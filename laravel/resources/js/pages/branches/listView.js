import { TableSorter } from "../../components/table/tableSorter.js";
import { TableRenderer } from "../../components/table/tableRenderer.js";
<<<<<<< HEAD
import { initPaginator } from "../../components/displays/paginator.js";
=======
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf
import { Toast } from "../../components/displays/toast.js";
import { apiFetch } from "../../utils/apiFetch.js";

let sorter = null;
let renderer = null;
let originalData = [];
let activeFilters = null;

<<<<<<< HEAD
export function initBranchListView(data = [], meta = {}) {
=======
export function initBranchListView(data = []) {
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf
    const container = document.getElementById("branchTableContainer");
    if (!container) return;

    // Handle highlighting from URL
    const urlParams = new URLSearchParams(window.location.search);
    const highlightedBranchId = urlParams.get("branch");

    originalData = data;
    updateBranchCounts(originalData);

    const tableConfig = {
        searchId: "#branchSearchInput",
        rowClass: "branch-table__row",
        columns: [
            {
                label: "Branch Name",
                key: "name",
                sortable: true,
                searchable: true,
                render: (val, item) => `
                    <div class="name-cell">
                        ${val}
                        ${item.deleted_at ? '<span class="today-badge" style="background: var(--status-red); margin-left: 8px;">Archived</span>' : ""}
                    </div>`,
            },
            {
                label: "City",
                key: "city",
                sortable: true,
                searchable: true,
                render: (val) => {
                    const city = val || "No city";
                    return `<div class="description-cell">
                                <i class="fa-solid fa-location-dot" style="font-size: 10px; margin-right: 4px;"></i>
                                ${city}
                            </div>`;
                },
            },
            {
                label: "Type",
                key: "type",
                sortable: true,
                searchable: true,
                render: (val) =>
                    `<div class="description-cell">${val ? val.charAt(0).toUpperCase() + val.slice(1) : "Standard"}</div>`,
            },
            {
                label: "Business",
                key: "business.name",
                sortable: false,
                searchable: true,
                render: (val, item) => {
                    if (!item.business)
                        return `<span class="text-muted">—</span>`;
                    return `
                        <a href="/manage/businesses/${item.business.id}?branch=${item.id}" class="stat-badge stat-badge--service" style="width:fit-content; text-decoration:none;">
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
                    const serviceCount = item.services?.length ?? 0;
                    const serviceLabel =
                        serviceCount === 1 ? "Service" : "Services";
                    return `
                        <div class="stat-badge-group js-open-branch-connections" data-id="${item.id}" style="cursor:pointer; display:flex; gap:6px; padding-top: 0px;">
                            <div class="stat-badge stat-badge--branch" title="Services">
                                <i class="fa-solid fa-bell-concierge"></i>
                                <span>${String(serviceCount).padStart(2, "0")} ${serviceLabel}</span>
                            </div>
                        </div>`;
                },
            },
            {
                label: "Status",
                key: "is_active",
                sortable: true,
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
                        <button type="button" class="button-icon button-icon--success js-restore-branch-btn" title="Restore" data-id="${item.id}">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>`;
            }

            const toggleIcon = item.is_active ? "fa-eye" : "fa-eye-slash";
            const toggleTitle = item.is_active
                ? "Deactivate Branch"
                : "Activate Branch";
            const nextStatus = item.is_active ? 0 : 1;

            return `
                <div class="business__actions">
                    <button type="button" class="button-icon button-icon--warning js-toggle-active-btn" 
                            title="${toggleTitle}" data-id="${item.id}" data-next="${nextStatus}" data-bizid="${item.business_id}">
                        <i class="fa-solid ${toggleIcon}" style="${!item.is_active ? "opacity: 0.5" : ""}"></i>
                    </button>
                    <a href="${window.BE_DATA.routes.show.replace(":id", item.id)}" class="button-icon"><i class="fa-solid fa-gear"></i></a>
                    <button type="button" 
                            class="button-icon button-icon--danger js-archive-branch-btn" 
                            data-modal-target="archive-branch-modal" 
                            data-id="${item.id}" 
                            data-name="${item.name}" 
                            title="Archive">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>`;
        },
        onRowRender: (tr, item) => {
            if (item.deleted_at) tr.classList.add("is-archived");
            if (
                highlightedBranchId &&
                String(item.id) === String(highlightedBranchId)
            ) {
                tr.classList.add("highlighted-row");
                setTimeout(
                    () =>
                        tr.scrollIntoView({
                            behavior: "smooth",
                            block: "center",
                        }),
                    200,
                );
            }
        },
    };

    renderer = new TableRenderer(tableConfig);

    // Default view: hide archived unless filters say otherwise
    const initialData = originalData.filter((b) => !b.deleted_at);

    sorter = new TableSorter(initialData, "name", "asc", (sortedData) => {
        renderer.render(container, sortedData, sorter);
    });

    renderer.render(container, sorter.getSortedData(), sorter);

<<<<<<< HEAD
    initPaginator(meta, (page) => {
        const url = new URL(window.location.href);
        url.searchParams.set("page", page);
        window.location.href = url.toString();
    });

    // ── Delegated handlers ──────────────────────────────────────────────────

=======
    // ── Delegated handlers ──────────────────────────────────────────────────

>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf
    container.addEventListener("click", async (e) => {
        const restoreBtn = e.target.closest(".js-restore-branch-btn");
        if (restoreBtn) {
            await handleRestore(restoreBtn);
            return;
        }

        const toggleBtn = e.target.closest(".js-toggle-active-btn");
        if (toggleBtn) {
            await handleToggleActive(toggleBtn);
        }

        // Archive is handled by initArchiveBranchModal via document listener — nothing to do here.
    });

    // ── Filter listener ─────────────────────────────────────────────────────

    window.addEventListener("branchFiltersChanged", (e) => {
        const statuses = e.detail.statuses;

        activeFilters = statuses.reduce((acc, s) => {
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

        const record = originalData.find((b) => String(b.id) === String(id));
        if (record) record.deleted_at = null;

        Toast.success(
            "Branch restored",
            response?.message || "The branch is now active again.",
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
    const businessId = btn.dataset.bizid;
    btn.disabled = true;

    try {
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

        const record = originalData.find((b) => String(b.id) === String(id));
        if (record) record.is_active = nextStatus;

        const title = nextStatus ? "Branch activated" : "Branch deactivated";
        const type = nextStatus ? "success" : "warning";
        const fallback = nextStatus
            ? "The branch is now active."
            : "The branch is now inactive.";

        Toast[type](title, response?.message || fallback);

        rerender();
    } catch (err) {
        Toast.error("Update failed", err.message);
        btn.disabled = false;
    }
}

// ── Helpers ─────────────────────────────────────────────────────────────────

function rerender() {
    updateBranchCounts(originalData);
    sorter.setData(applyFilters());

    const container = document.getElementById("branchTableContainer");
    if (container) renderer.render(container, sorter.getSortedData(), sorter);
}

function applyFilters() {
    if (!activeFilters) {
        return originalData.filter((b) => !b.deleted_at);
    }

    return originalData.filter((item) => {
        if (item.deleted_at) return activeFilters.archived;
        if (item.is_active) return activeFilters.active;
        return activeFilters.inactive;
    });
}

function updateBranchCounts(data) {
    const stats = {
        all: data.length,
        active: data.filter((b) => b.is_active && !b.deleted_at).length,
        inactive: data.filter((b) => !b.is_active && !b.deleted_at).length,
        archived: data.filter((b) => b.deleted_at).length,
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
