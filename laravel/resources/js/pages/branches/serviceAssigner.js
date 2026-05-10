import { Toast } from "../../components/displays/toast.js";
import { apiFetch } from "../../utils/apiFetch.js";

// ─── Helpers ─────────────────────────────────────────────────────────────────

function buildRoute(template, serviceId) {
    return template.replace(":serviceId", serviceId);
}

function escapeHtml(str) {
    const div = document.createElement("div");
    div.textContent = str;
    return div.innerHTML;
}

// ─── DOM helpers ──────────────────────────────────────────────────────────────

/** Switch the select panel to the "all linked" empty-state when no options remain. */
function updateSelectVisibility() {
    const selectEl = document.getElementById("serviceMultiselect");
    const wrapper = document.getElementById("multiselect-wrapper");
    const emptyMsg = document.getElementById("empty-select-message");

    if (!selectEl || !wrapper || !emptyMsg) return;

    const isEmpty = selectEl.options.length === 0;
    wrapper.style.display = isEmpty ? "none" : "block";
    emptyMsg.style.display = isEmpty ? "block" : "none";
}

function buildServiceCard(service) {
    const unassignUrl = buildRoute(
        window.BE_DATA.routes.unassignService,
        service.id,
    );
    const showUrl =
        window.BE_DATA.routes.showService?.replace(":serviceId", service.id) ??
        "#";

    const div = document.createElement("div");
    div.className = "service-row";
    div.dataset.id = service.id;
    div.dataset.name = service.name; // add this
    div.innerHTML = `
        <a href="${showUrl}" class="service-card-link">
            <i class="fa-solid fa-bell-concierge"
               style="margin-right:12px;color:var(--color-primary);font-size:16px;"></i>
            <span class="service-card__title">${escapeHtml(service.name)}</span>
        </a>
        <div class="service-card__actions">
            <button type="button" class="button-icon--danger js-unassign-btn"
                    data-id="${service.id}" data-url="${unassignUrl}" title="Unlink">
                <i class="fa-solid fa-link-slash"></i>
            </button>
        </div>`;
    return div;
}

function setEmptyState(list) {
    if (
        !list.querySelector(".service-row") &&
        !list.querySelector(".rule-panel__empty")
    ) {
        const p = document.createElement("p");
        p.className = "rule-panel__empty";
        p.style.padding = "15px";
        p.textContent = "No services linked to this branch yet.";
        list.appendChild(p);
    }
}

function clearEmptyState(list) {
    list.querySelector(".rule-panel__empty")?.remove();
}

function addOptionToSelect(selectEl, service) {
    if (!selectEl) return;
    const exists = Array.from(selectEl.options).some(
        (opt) => opt.value === String(service.id),
    );
    if (!exists) selectEl.add(new Option(service.name, service.id));
}

// ─── Init ─────────────────────────────────────────────────────────────────────

export function initServiceAssigner() {
    const selectEl = document.getElementById("serviceMultiselect");
    const list = document.getElementById("linkedServicesList");
    const btnAssign = document.getElementById("btnAssignServices");

    if (!list) return;

    // Disable assign button until the user selects at least one option
    if (selectEl && btnAssign) {
        selectEl.addEventListener("change", () => {
            btnAssign.disabled = selectEl.selectedOptions.length === 0;
        });
    }

    // ── Assign ────────────────────────────────────────────────────────────
    if (btnAssign) {
        btnAssign.addEventListener("click", async () => {
            const selectedOptions = Array.from(selectEl.selectedOptions);
            if (!selectedOptions.length) return;

            btnAssign.disabled = true;
            const originalContent = btnAssign.innerHTML;
            btnAssign.innerHTML =
                '<i class="fa-solid fa-spinner fa-spin"></i> Linking…';

            const errors = [];
            const linkedNames = [];

            for (const opt of selectedOptions) {
                const service = window.BE_DATA.allServices.find(
                    (s) => String(s.id) === String(opt.value),
                );
                if (!service) continue;

                try {
                    await apiFetch(
                        buildRoute(
                            window.BE_DATA.routes.assignService,
                            service.id,
                        ),
                        { method: "POST" },
                    );

                    clearEmptyState(list);
                    list.appendChild(buildServiceCard(service));
                    linkedNames.push(service.name);

                    const optIdx = Array.from(selectEl.options).findIndex(
                        (o) => o.value === String(service.id),
                    );
                    if (optIdx !== -1) selectEl.remove(optIdx);
                } catch {
                    errors.push(service.name);
                }
            }

            btnAssign.innerHTML = originalContent;
            btnAssign.disabled = true;
            updateSelectVisibility();

            if (errors.length) {
                Toast.error(
                    "Linking failed",
                    `Could not link: ${errors.join(", ")}`,
                );
            }

            if (linkedNames.length) {
                Toast.success(
                    "Services linked",
                    `Service ${linkedNames.join(", ")} linked to this branch successfully.`,
                );
            }
        });
    }

    // ── Unassign (event delegation) ───────────────────────────────────────
    list.addEventListener("click", async (e) => {
        const btn = e.target.closest(".js-unassign-btn");
        if (!btn) return;

        const serviceId = btn.dataset.id;
        const url = btn.dataset.url;
        const card = btn.closest(".service-row");

        btn.disabled = true;
        const originalIcon = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        try {
            await apiFetch(url, { method: "DELETE" });
            card?.remove();

            const service = window.BE_DATA.allServices.find(
                (s) => String(s.id) === String(serviceId),
            );
            const currentSelect = document.getElementById("serviceMultiselect");
            if (currentSelect && service) {
                addOptionToSelect(currentSelect, service);
                updateSelectVisibility();
            } else {
                location.reload();
            }

            setEmptyState(list);
            Toast.success(
                "Service unlinked",
                "The service has been removed from this branch.",
            );
        } catch (err) {
            btn.disabled = false;
            btn.innerHTML = originalIcon;
            Toast.error(
                "Unlink failed",
                err.message || "Failed to unlink service.",
            );
        }
    });

    // ── Legacy form support (Blade-rendered unassign forms) ───────────────
    list.addEventListener("submit", async (e) => {
        const form = e.target.closest(".js-unassign-form");
        if (!form) return;
        e.preventDefault();

        const card = form.closest(".service-row");
        const btn = form.querySelector('button[type="submit"]');
        const serviceId = card?.dataset?.id;
        const serviceName = card?.dataset?.name;

        if (btn) btn.disabled = true;

        try {
            await apiFetch(form.action, { method: "DELETE" });
            card?.remove();

            const service = window.BE_DATA.allServices.find(
                (s) => String(s.id) === String(serviceId),
            );
            const currentSelect = document.getElementById("serviceMultiselect");
            if (currentSelect && service) {
                addOptionToSelect(currentSelect, service);
                updateSelectVisibility();
            }

            setEmptyState(list);
            Toast.warning(
                "Service unlinked",
                `The service '${serviceName}' has been removed from this branch.`,
            );
        } catch (err) {
            if (btn) btn.disabled = false;
            Toast.error(
                "Unlink failed",
                err.message || "Failed to unlink service.",
            );
        }
    });
}
