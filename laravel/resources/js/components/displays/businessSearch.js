export function initBusinessSearch() {
    const input    = document.getElementById('businessSearchInput');
    const dropdown = document.getElementById('businessSearchDropdown');
    const hidden   = document.getElementById('businessIdInput');
    const badge    = document.getElementById('selectedBusinessBadge');
    const label    = document.getElementById('selectedBusinessLabel');
    const clearBtn = document.getElementById('clearBusinessBtn');

    if (!input) return;

    let debounceTimer = null;

    input.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const q = input.value.trim();

        if (q.length < 2) {
            closeDropdown();
            return;
        }

        debounceTimer = setTimeout(() => fetchUsers(q), 300);
    });

    clearBtn?.addEventListener('click', () => {
        hidden.value     = '';
        label.textContent = '';
        input.value      = '';
        badge.classList.add('hidden');
        closeDropdown();
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.business-search')) closeDropdown();
    });

    async function fetchUsers(q) {
        try {
            const res   = await fetch(`/manage/businesses/search?q=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.BE_DATA.csrf }
            });
            const businesses = await res.json();
            renderDropdown(businesses);
        } catch {
            closeDropdown();
        }
    }

    function renderDropdown(businesses) {
        dropdown.innerHTML = '';

        if (!businesses.length) {
            dropdown.innerHTML = `<div class="user-search__empty">No Business found</div>`;
            dropdown.classList.remove('hidden');
            return;
        }

        businesses.forEach(business => {
            const item = document.createElement('div');
            item.className = 'user-search__item';
            item.innerHTML = `
                <span class="user-search__name">${business.name}</span>
            `;
            item.addEventListener('click', () => selectBusiness(business));
            dropdown.appendChild(item);
        });

        dropdown.classList.remove('hidden');
    }

    function selectBusiness(business) {
        hidden.value      = business.id;
        label.textContent = business.name;
        input.value       = '';
        badge.classList.remove('hidden');
        closeDropdown();
    }

    function closeDropdown() {
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
    }
}
