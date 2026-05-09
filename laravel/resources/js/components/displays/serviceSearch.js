export function initServiceSearch() {
    const input    = document.getElementById('serviceSearchInput');
    const dropdown = document.getElementById('serviceSearchDropdown');
    const hidden   = document.getElementById('serviceIdInput');
    const badge    = document.getElementById('selectedServiceBadge');
    const label    = document.getElementById('selectedServiceLabel');
    const clearBtn = document.getElementById('clearServiceBtn');

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
        if (!e.target.closest('.service-search')) closeDropdown();
    });

    async function fetchUsers(q) {
        try {
            const res   = await fetch(`/manage/services/search?q=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.BE_DATA.csrf }
            });
            const services = await res.json();
            renderDropdown(services);
        } catch {
            closeDropdown();
        }
    }

    function renderDropdown(services) {
        dropdown.innerHTML = '';

        if (!services.length) {
            dropdown.innerHTML = `<div class="user-search__empty">No service found</div>`;
            dropdown.classList.remove('hidden');
            return;
        }

        services.forEach(service => {
            const item = document.createElement('div');
            item.className = 'user-search__item';
            item.innerHTML = `
                <span class="user-search__name">${service.name}</span>
            `;
            item.addEventListener('click', () => selectService(service));
            dropdown.appendChild(item);
        });

        dropdown.classList.remove('hidden');
    }

    function selectService(service) {
        hidden.value      = service.id;
        label.textContent = service.name;
        input.value       = '';
        badge.classList.remove('hidden');
        closeDropdown();
    }

    function closeDropdown() {
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
    }
}
