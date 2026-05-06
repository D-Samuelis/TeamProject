/**
 * serviceAssigner.js
 * Vanilla JS logic for linking/unlinking services.
 */

// ─── Helpers ─────────────────────────────────────────────────────────────────

function buildRoute(template, serviceId) {
    return template.replace(':serviceId', serviceId);
}

async function apiFetch(url, method) {
    const res = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.BE_DATA.csrf,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
    });

    if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        throw new Error(err.message || `Request failed (${res.status})`);
    }

    return res.json().catch(() => ({}));
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function showToast(message, type = 'success') {
    const el = document.createElement('div');
    el.textContent = message;
    Object.assign(el.style, {
        position: 'fixed', bottom: '24px', right: '24px',
        background: type === 'success' ? 'var(--color-success, #1d9e75)' : 'var(--color-danger, #e24b4a)',
        color: '#fff', padding: '10px 18px', borderRadius: '6px',
        fontSize: '14px', zIndex: 9999, boxShadow: '0 4px 12px rgba(0,0,0,.15)',
        transition: 'opacity .3s',
    });
    document.body.appendChild(el);
    setTimeout(() => {
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
    }, 3000);
}

// ─── DOM Helpers ─────────────────────────────────────────────────────────────

/**
 * Prepne zobrazenie selectu na "Empty State" správu, ak už nie sú žiadne options.
 */
function updateSelectVisibility() {
    const selectEl = document.getElementById('serviceMultiselect');
    const wrapper = document.getElementById('multiselect-wrapper');
    const emptyMsg = document.getElementById('empty-select-message');
    
    if (!selectEl || !wrapper || !emptyMsg) return;

    if (selectEl.options.length === 0) {
        wrapper.style.display = 'none';
        emptyMsg.style.display = 'block';
    } else {
        wrapper.style.display = 'block';
        emptyMsg.style.display = 'none';
    }
}

/**
 * Vytvorí kompaktnú kartu služby.
 */
function buildServiceCard(service) {
    const unassignUrl = buildRoute(window.BE_DATA.routes.unassignService, service.id);
    const showUrl = window.BE_DATA.routes.showService?.replace(':serviceId', service.id) ?? '#';

    const div = document.createElement('div');
    div.className = 'service-row'; // Hlavná trieda riadku
    div.dataset.id = service.id;
    
    div.innerHTML = `
        <a href="${showUrl}" class="service-card-link">
            <i class="fa-solid fa-bell-concierge" style="margin-right: 12px; color: var(--color-primary); font-size: 16px;"></i>
            <span class="service-card__title">${escapeHtml(service.name)}</span>
        </a>
        <div class="service-card__actions">
            <button type="button" class="button-icon--danger js-unassign-btn" 
                    data-id="${service.id}" data-url="${unassignUrl}" title="Unlink">
                <i class="fa-solid fa-link-slash"></i>
            </button>
        </div>
    `;
    return div;
}

function setEmptyState(list) {
    if (list.querySelectorAll('.service-row').length === 0) {
        if (!list.querySelector('.rule-panel__empty')) {
            const p = document.createElement('p');
            p.className = 'rule-panel__empty';
            p.style.padding = '15px';
            p.textContent = 'No services linked to this branch yet.';
            list.appendChild(p);
        }
    }
}

function clearEmptyState(list) {
    list.querySelector('.rule-panel__empty')?.remove();
}

/**
 * Vráti option späť do selectu (ak tam ešte nie je) po unlinknutí.
 */
function addOptionToSelect(selectEl, service) {
    if (!selectEl) return;
    const exists = Array.from(selectEl.options).some(opt => opt.value === String(service.id));
    if (!exists) {
        const option = new Option(service.name, service.id);
        selectEl.add(option);
        // Ak sme predtým mali "Empty State" text, po pridaní prvej option musíme vrátiť select do DOM
        // (v tomto scenári však predpokladáme, že select v DOM zostáva alebo sa celá sekcia refreshne)
    }
}

// ─── Init ─────────────────────────────────────────────────────────────────────

export function initServiceAssigner() {
    const selectEl  = document.getElementById('serviceMultiselect');
    const list      = document.getElementById('linkedServicesList');
    const btnAssign = document.getElementById('btnAssignServices');

    if (!list) return;

    // ── Logic pre Disabled Button ──
    if (selectEl && btnAssign) {
        selectEl.addEventListener('change', () => {
            const selectedCount = Array.from(selectEl.selectedOptions).length;
            btnAssign.disabled = selectedCount === 0;
        });
    }

    // ── Assign ──
    if (btnAssign) {
        btnAssign.addEventListener('click', async () => {
            const selectedOptions = Array.from(selectEl.selectedOptions);
            const selectedIds = selectedOptions.map(opt => opt.value);

            if (selectedIds.length === 0) return;

            btnAssign.disabled = true;
            const originalContent = btnAssign.innerHTML;
            btnAssign.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Linking...';

            const errors = [];

            for (const id of selectedIds) {
                const service = window.BE_DATA.allServices.find(s => String(s.id) === String(id));
                if (!service) continue;

                try {
                    await apiFetch(buildRoute(window.BE_DATA.routes.assignService, id), 'POST');
                    clearEmptyState(list);
                    list.appendChild(buildServiceCard(service));
                    
                    // Odstránenie zo selectu
                    const optIdx = Array.from(selectEl.options).findIndex(o => o.value === String(id));
                    if (optIdx !== -1) selectEl.remove(optIdx);
                } catch (e) {
                    errors.push(service?.name ?? `#${id}`);
                }
            }

            btnAssign.innerHTML = originalContent;
            btnAssign.disabled = true; // Reset po spracovaní
            
            updateSelectVisibility();

            if (errors.length) {
                showToast(`Failed to link: ${errors.join(', ')}`, 'error');
            } else {
                showToast(`${selectedIds.length} service(s) linked.`);
            }
        });
    }

    // ── Unassign (Event Delegation) ──
    list.addEventListener('click', async (e) => {
        const btn = e.target.closest('.js-unassign-btn');
        if (!btn) return;

        const serviceId = btn.dataset.id;
        const url       = btn.dataset.url;
        const card      = btn.closest('.service-row');

        btn.disabled = true;
        const originalIcon = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        try {
            await apiFetch(url, 'DELETE');
            card?.remove();

            const service = window.BE_DATA.allServices.find(s => String(s.id) === String(serviceId));
            
            const currentSelect = document.getElementById('serviceMultiselect');
            if (currentSelect && service) {
                addOptionToSelect(currentSelect, service);
                updateSelectVisibility();
            } else {
                location.reload(); 
            }

            setEmptyState(list);
            showToast('Service unlinked.');
        } catch (err) {
            btn.disabled = false;
            btn.innerHTML = originalIcon;
            showToast(err.message || 'Failed to unlink.', 'error');
        }
    });

    // ── Legacy Form Support ──
    list.addEventListener('submit', async (e) => {
        const form = e.target.closest('.js-unassign-form');
        if (!form) return;
        e.preventDefault();

        const card = form.closest('.service-row');
        const btn = form.querySelector('button[type="submit"]');
        const serviceId = card?.dataset?.id;

        if (btn) btn.disabled = true;

        try {
            await apiFetch(form.action, 'DELETE');
            card?.remove();

            const service = window.BE_DATA.allServices.find(s => String(s.id) === String(serviceId));
            const currentSelect = document.getElementById('serviceMultiselect');
            if (currentSelect && service) {
                addOptionToSelect(currentSelect, service);
                updateSelectVisibility();
            }

            setEmptyState(list);
            showToast('Service unlinked.');
        } catch (err) {
            if (btn) btn.disabled = false;
            showToast(err.message || 'Failed to unlink.', 'error');
        }
    });
}