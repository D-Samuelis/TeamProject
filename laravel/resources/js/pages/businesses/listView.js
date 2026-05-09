import { TableSorter } from "../../components/table/tableSorter.js";
import { TableRenderer } from "../../components/table/tableRenderer.js";
import { Toast } from "../../components/displays/toast.js";
import { apiFetch } from "../../utils/apiFetch.js";

let sorter = null;
let renderer = null;
let originalData = [];
let activeFilters = null; // tracks current filter state so rerender() respects it

export function initBusinessListView(data = []) {
    const container = document.getElementById("businessTableContainer");
    if (!container) return;

    originalData = data;

    updateCounts(originalData);

    const tableConfig = {
        searchId: "#businessSearchInput",
        rowClass: "business-table__row",
        columns: [
            {
                label: "Business Name",
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
                label: "Status",
                key: "is_published",
                sortable: true,
                render: (val, item) => {
                    if (item.deleted_at)
                        return `<span class="status-cell filter-item--red">Deleted</span>`;
                    return val
                        ? `<span class="status-cell filter-item--green">Published</span>`
                        : `<span class="status-cell filter-item--yellow">Hidden</span>`;
                },
            },
        ],
        renderActions: (item) => {
            if (item.deleted_at) {
                return `
                    <div class="business__actions">
                        <button
                            type="button"
                            class="button-icon button-icon--success js-restore-business-btn"
                            title="Restore"
                            data-id="${item.id}">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>

                        <button
                            type="button"
                            class="button-icon js-show-business-btn"
                            title="Settings"
                            data-id="${item.id}"
                            data-href="${window.BE_DATA.routes.show.replace(":id", item.id)}">
                            <i class="fa-solid fa-gear"></i>
                        </button>
                    </div>`;
            }

            const toggleIcon = item.is_published ? "fa-eye" : "fa-eye-slash";
            const toggleTitle = item.is_published
                ? "Hide Business"
                : "Publish Business";
            const nextStatus = item.is_published ? 0 : 1;

            return `
                <div class="business__actions">
                    <button
                        type="button"
                        class="button-icon button-icon--warning js-toggle-publish-btn"
                        title="${toggleTitle}"
                        data-id="${item.id}"
                        data-next="${nextStatus}">
                        <i class="fa-solid ${toggleIcon}" style="${!item.is_published ? "opacity: 0.5" : ""}"></i>
                    </button>

                    <button
                        type="button"
                        class="button-icon js-show-business-btn"
                        title="Settings"
                        data-id="${item.id}"
                        data-href="${window.BE_DATA.routes.show.replace(":id", item.id)}">
                        <i class="fa-solid fa-gear"></i>
                    </button>
                    
                    <button 
                        type="button" 
                        class="button-icon button-icon--danger js-archive-business-btn"
                        data-modal-target="archive-business-modal"
                        data-id="${item.id}"
                        data-name="${item.name}"
                        title="Archive">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>`;
        },
        onRowRender: (tr, item) => {
            if (item.deleted_at) tr.classList.add("is-archived");
        },
    };

    renderer = new TableRenderer(tableConfig);

    const initialData = originalData.filter((b) => !b.deleted_at);

    sorter = new TableSorter(initialData, "name", "asc", (sortedData) => {
        renderer.render(container, sortedData, sorter);
    });

    renderer.render(container, sorter.getSortedData(), sorter);

    // ── Delegated handlers ──────────────────────────────────────────────────

    container.addEventListener("click", async (e) => {
        const showBtn = e.target.closest(".js-show-business-btn");
        if (showBtn) {
            window.location.href = showBtn.dataset.href;
            return;
        }

        const restoreBtn = e.target.closest(".js-restore-business-btn");
        if (restoreBtn) {
            await handleRestore(restoreBtn);
            return;
        }

        const toggleBtn = e.target.closest(".js-toggle-publish-btn");
        if (toggleBtn) {
            await handleTogglePublish(toggleBtn);
        }
    });

    // ── Filter listener ─────────────────────────────────────────────────────

    window.addEventListener("businessFiltersChanged", (event) => {
        const statuses = event.detail.statuses;

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
        await apiFetch(window.BE_DATA.routes.restore.replace(":id", id), {
            method: "POST",
            body: JSON.stringify({ _method: "PATCH" }),
        });

        const record = originalData.find((b) => String(b.id) === String(id));
        if (record) record.deleted_at = null;

        Toast.success("Business restored", "The business is now active again.");
        rerender();
    } catch (err) {
        Toast.error("Restore failed", err.message);
        btn.disabled = false;
    }
}

async function handleTogglePublish(btn) {
    const id = btn.dataset.id;
    const nextStatus = Number(btn.dataset.next);
    btn.disabled = true;

    try {
        await apiFetch(window.BE_DATA.routes.update.replace(":id", id), {
            method: "POST",
            body: JSON.stringify({ _method: "PUT", is_published: nextStatus }),
        });

        const record = originalData.find((b) => String(b.id) === String(id));
        if (record) record.is_published = nextStatus;

        const msg = nextStatus
            ? "Business is now published."
            : "Business is now hidden.";
        Toast.success("Status updated", msg);
        rerender();
    } catch (err) {
        Toast.error("Failed to update status", err.message);
        btn.disabled = false;
    }
}

// ── Re-render helper ────────────────────────────────────────────────────────

function rerender() {
    updateCounts(originalData);
    sorter.setData(applyFilters());

    const container = document.getElementById("businessTableContainer");
    if (container) renderer.render(container, sorter.getSortedData(), sorter);
}

// ── Filter helper ───────────────────────────────────────────────────────────

function applyFilters() {
    if (!activeFilters) {
        // no filter event has fired yet — default view hides deleted
        return originalData.filter((b) => !b.deleted_at);
    }

    return originalData.filter((item) => {
        if (item.deleted_at) return activeFilters.deleted;
        if (item.is_published) return activeFilters.published;
        return activeFilters.hidden;
    });
}

// ── Count display ───────────────────────────────────────────────────────────

function updateCounts(data) {
    const stats = {
        all: data.length,
        published: data.filter((b) => b.is_published && !b.deleted_at).length,
        hidden: data.filter((b) => !b.is_published && !b.deleted_at).length,
        deleted: data.filter((b) => b.deleted_at).length,
    };

    updateStatElement("countAll", stats.all);
    updateStatElement("countPublished", stats.published);
    updateStatElement("countHidden", stats.hidden);
    updateStatElement("countDeleted", stats.deleted);
}

function updateStatElement(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}
