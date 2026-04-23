import { Modal } from '../../../components/displays/modal.js';

export function initCreateServiceModal() {
    const createBtn = document.querySelector('[data-modal-target="create-service-modal"]');

    if (!createBtn) return;

    createBtn.addEventListener('click', (e) => {
        e.preventDefault();

        const { csrf, routes, businesses = [], branches = [] } = window.BE_DATA;

        Modal.showCustom({
            title: 'Create New Service',
            confirmText: 'Create Service',
            action: 'create',
            rules: {
                name:             { required: { value: true, message: 'Service name is required' } },
                duration_minutes: { required: { value: true, message: 'Duration is required' } },
                price:            { required: { value: true, message: 'Price is required' } },
            },
            body: `
                <form id="modalForm" method="POST" action="${routes.store}">
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="business_id" id="business_id_input">

                    <div class="modal-form__group" style="position:relative;">
                        <label class="modal-form__label">Business</label>
                        <div class="searchable-select-wrapper">
                            <input
                                type="text"
                                id="business_search"
                                class="modal-form__input"
                                placeholder="Search and select business..."
                                autocomplete="off"
                            >
                            <div id="business_dropdown" class="custom-dropdown" style="display:none;">
                                ${businesses.map(b => `
                                    <div class="dropdown-item" data-value="${b.id}">${b.name}</div>
                                `).join('')}
                            </div>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Name</label>
                        <div class="input-wrapper">
                            <input type="text" name="name" class="modal-form__input" placeholder=" " required autofocus>
                        </div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__label">Description (Optional)</label>
                        <div class="input-wrapper">
                            <textarea name="description" class="modal-form__input" placeholder=" " style="min-height:100px;"></textarea>
                        </div>
                    </div>

                    <div class="modal-form__row">
                        <div class="modal-form__group">
                            <label class="modal-form__label">Duration (minutes)</label>
                            <div class="input-wrapper">
                                <input type="number" name="duration_minutes" min="1" class="modal-form__input" placeholder=" " required>
                            </div>
                        </div>
                        <div class="modal-form__group">
                            <label class="modal-form__label">Price</label>
                            <div class="input-wrapper">
                                <input type="number" name="price" min="0" step="0.01" class="modal-form__input" placeholder=" " required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-form__group" style="position:relative;">
                        <label class="modal-form__label">Branches</label>
                        <div id="branch-combobox-wrapper" style="margin-top: .75rem;">
                            <p id="branch_placeholder" style="font-size:11px;color:var(--color-text-light);padding:20px;text-align:center;border:1px solid var(--color-border-light);border-radius:4px;background:var(--color-bg-light);margin:0;">
                                <i class="fa-solid fa-arrow-up" style="margin-right:5px;"></i> Firstly, choose a business to see available branches
                            </p>
                            <div class="searchable-select-wrapper" id="branch_select_wrapper" style="display:none;">
                                <div class="combobox-multi-tags" id="branch_tags">
                                    <input
                                        type="text"
                                        id="branch_search"
                                        class="modal-form__input"
                                        placeholder="Search branches..."
                                        autocomplete="off"
                                    >
                                </div>
                                <div id="branch_dropdown" class="custom-dropdown" style="display:none;"></div>
                            </div>
                        </div>
                        <div id="branch_hidden_inputs"></div>
                    </div>

                    <div class="modal-form__group">
                        <label class="modal-form__checkbox-label">
                            <input type="checkbox" name="is_active" value="1" checked>
                            Active
                        </label>
                    </div>
                </form>
            `,
            onConfirm: async (modal) => {
                const submitBtn = modal.querySelector('.btn-confirm');
                console.log("onConfirm konečne beží!"); 
                
                const form = modal.querySelector('#modalForm');
                // Vyčistíme staré chyby (aj tie z modalu)
                Modal.clearFieldErrors(modal);

                const errors = {};
                const businessId = modal.querySelector('#business_id_input').value;
                const branches = modal.querySelectorAll('input[name="branch_ids[]"]');

                // Ručná validácia pre tvoje špeciálne prvky
                if (!businessId) {
                    errors['business_id'] = ['Business is required'];
                }
                if (branches.length === 0) {
                    errors['branch_ids'] = ['At least one branch must be selected'];
                }

                if (Object.keys(errors).length > 0) {
                    // Zobrazíme naše chyby
                    Modal.showFieldErrors(modal, errors);
                    
                    // Špeciálny vizuálny feedback pre searchable selecty, 
                    // pretože showFieldErrors ich nemusí trafiť (keďže sú hidden)
                    if (errors['business_id']) {
                        modal.querySelector('#business_search').classList.add('input-error');
                    }
                    if (errors['branch_ids']) {
                        modal.querySelector('#branch_search').classList.add('input-error');
                    }
                    return;
                }
 
                if (submitBtn) submitBtn.disabled = true;
 
                try {
                    const res = await fetch(routes.store, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: new FormData(form),
                    });
 
                    if (res.ok) {
                        window.location.reload();
                        return;
                    }
 
                    if (res.status === 422) {
                        const json = await res.json();
                        Modal.showFieldErrors(modal, json.errors);
                    } else {
                        alert('Server error. Please try again.');
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    alert('Network error. Check your connection.');
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                }
            },
        });
 
        requestAnimationFrame(() => {
            setupBusinessSelect(businesses, branches);
        });
    });
}

// ─────────────────────────────────────────────────────────────
//  Business searchable select
// ─────────────────────────────────────────────────────────────
function setupBusinessSelect(businesses, branches) {
    const searchInput = document.getElementById('business_search');
    const dropdown    = document.getElementById('business_dropdown');
    const hiddenInput = document.getElementById('business_id_input');
    const items       = dropdown.querySelectorAll('.dropdown-item');
 
    if (!searchInput) return;
 
    searchInput.addEventListener('focus', () => {
        dropdown.style.display = 'block';
        items.forEach(item => item.style.display = 'block');
    });
 
    searchInput.addEventListener('input', () => {
        const filter = searchInput.value.toLowerCase();
        dropdown.style.display = 'block';
 
        items.forEach(item => {
            item.style.display = item.textContent.toLowerCase().includes(filter) ? 'block' : 'none';
        });
 
        if (!searchInput.value) {
            hiddenInput.value = '';
            lockBranchSelect();
        }
    });
 
    items.forEach(item => {
        item.addEventListener('click', () => {
            searchInput.value = item.textContent;
            hiddenInput.value = item.dataset.value;
            dropdown.style.display = 'none';
            unlockBranchSelect(branches, parseInt(item.dataset.value));
        });
    });
 
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.searchable-select-wrapper')) {
            dropdown.style.display = 'none';
        }
    });
}
 
// ─────────────────────────────────────────────────────────────
//  Branch lock / unlock
// ─────────────────────────────────────────────────────────────
function lockBranchSelect() {
    const wrapper     = document.getElementById('branch_select_wrapper');
    const placeholder = document.getElementById('branch_placeholder');
 
    if (wrapper) wrapper.style.display = 'none';
 
    if (placeholder) {
        placeholder.style.display = 'block';
        placeholder.innerHTML     = '<i class="fa-solid fa-arrow-up" style="margin-right:5px;"></i> Firstly, choose a business to see available branches';
    }
 
    document.querySelectorAll('.combobox-tag').forEach(t => t.remove());
    document.getElementById('branch_hidden_inputs').innerHTML = '';
}
 
function unlockBranchSelect(allBranches, businessId) {
    const wrapper     = document.getElementById('branch_select_wrapper');
    const placeholder = document.getElementById('branch_placeholder');
    const searchInput = document.getElementById('branch_search');
 
    if (wrapper) wrapper.style.display = 'block';
 
    if (searchInput) searchInput.value = '';
 
    document.querySelectorAll('.combobox-tag').forEach(t => t.remove());
    document.getElementById('branch_hidden_inputs').innerHTML = '';
 
    const available = allBranches.filter(b => b.business_id === businessId);
 
    if (placeholder) {
        placeholder.style.display = available.length === 0 ? 'block' : 'none';
        placeholder.innerHTML     = '<i class="fa-solid fa-circle-info" style="margin-right:5px;"></i> No branches available for this business';
    }
 
    if (available.length > 0) {
        setupBranchMultiSelect(available);
    }
}
 
// ─────────────────────────────────────────────────────────────
//  Branch multi searchable select
// ─────────────────────────────────────────────────────────────
function setupBranchMultiSelect(available) {
    const searchInput  = document.getElementById('branch_search');
    const dropdown     = document.getElementById('branch_dropdown');
    const tagsEl       = document.getElementById('branch_tags');
    const hiddenInputs = document.getElementById('branch_hidden_inputs');
    const placeholder  = document.getElementById('branch_placeholder');
 
    if (!searchInput) return;
 
    let selected = [];
 
    function renderDropdown(filter = '') {
        const q        = filter.toLowerCase();
        const filtered = available.filter(b =>
            b.name.toLowerCase().includes(q) &&
            !selected.find(s => s.id === b.id)
        );
 
        dropdown.innerHTML = filtered.length
            ? filtered.map(b => `
                <div class="dropdown-item" data-value="${b.id}">
                    ${b.name}${b.city ? ` <span style="font-size:12px;color:#aaa;">(${b.city})</span>` : ''}
                </div>`).join('')
            : `<div class="dropdown-item" style="color:#999;font-style:italic;pointer-events:none;">No results</div>`;
    }
 
    function addTag(branch) {
        selected.push(branch);
 
        const hidden   = document.createElement('input');
        hidden.type    = 'hidden';
        hidden.name    = 'branch_ids[]';
        hidden.value   = branch.id;
        hidden.id      = `branch_hidden_${branch.id}`;
        hiddenInputs.appendChild(hidden);
 
        const tag      = document.createElement('div');
        tag.className  = 'combobox-tag';
        tag.dataset.id = branch.id;
        tag.innerHTML  = `${branch.name}<button type="button" data-remove="${branch.id}">&times;</button>`;
        tagsEl.appendChild(tag);
 
        if (placeholder) placeholder.style.display = 'none';
    }
 
    function removeTag(id) {
        selected = selected.filter(s => s.id !== id);
        document.querySelector(`.combobox-tag[data-id="${id}"]`)?.remove();
        document.getElementById(`branch_hidden_${id}`)?.remove();
        renderDropdown(searchInput.value);
    }
 
    searchInput.addEventListener('focus', () => {
        renderDropdown(searchInput.value);
        dropdown.style.display = 'block';
    });
 
    searchInput.addEventListener('input', () => {
        renderDropdown(searchInput.value);
        dropdown.style.display = 'block';
    });
 
    dropdown.addEventListener('mousedown', (e) => {
        const item = e.target.closest('.dropdown-item');
        if (!item || item.style.pointerEvents === 'none') return;
 
        const id     = parseInt(item.dataset.value);
        const branch = available.find(b => b.id === id);
        if (branch) {
            addTag(branch);
            searchInput.value = '';
            dropdown.style.display = 'none';
            renderDropdown('');
        }
    });
 
    tagsEl.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-remove]');
        if (btn) removeTag(parseInt(btn.dataset.remove));
    });
 
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#branch-combobox-wrapper')) {
            dropdown.style.display = 'none';
        }
    });
}
 








