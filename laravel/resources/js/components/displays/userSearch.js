export function initUserSearch() {
    const input    = document.getElementById('userSearchInput');
    const dropdown = document.getElementById('userSearchDropdown');
    const hidden   = document.getElementById('userIdInput');
    const badge    = document.getElementById('selectedUserBadge');
    const label    = document.getElementById('selectedUserLabel');
    const clearBtn = document.getElementById('clearUserBtn');

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
        if (!e.target.closest('.user-search')) closeDropdown();
    });

    async function fetchUsers(q) {
        try {
            const res   = await fetch(`/manage/users/search?q=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.BE_DATA.csrf }
            });
            const users = await res.json();
            renderDropdown(users);
        } catch {
            closeDropdown();
        }
    }

    function renderDropdown(users) {
        dropdown.innerHTML = '';

        if (!users.length) {
            dropdown.innerHTML = `<div class="user-search__empty">No users found</div>`;
            dropdown.classList.remove('hidden');
            return;
        }

        users.forEach(user => {
            const item = document.createElement('div');
            item.className = 'user-search__item';
            item.innerHTML = `
                <span class="user-search__name">${user.name}</span>
                <span class="user-search__email">${user.email}</span>
            `;
            item.addEventListener('click', () => selectUser(user));
            dropdown.appendChild(item);
        });

        dropdown.classList.remove('hidden');
    }

    function selectUser(user) {
        hidden.value      = user.id;
        label.textContent = `${user.name} (${user.email})`;
        input.value       = '';
        badge.classList.remove('hidden');
        closeDropdown();
    }

    function closeDropdown() {
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
    }
}
