export default function initNotificationsMenu() {
    const notificationsMenuButton = document.getElementById('notificationsMenu');
    const notificationsMenuContent = document.getElementById('notificationsMenuContent');
    const notificationList = document.getElementById('notificationList');
    const notificationCount = document.getElementById('notificationCount');
    const settingsOverlay = document.getElementById('settingsOverlay');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!notificationsMenuButton || !notificationsMenuContent) {
        return;
    }

    let isHoveringButton = false;
    let isHoveringContent = false;

    function openMenu() {
        notificationsMenuContent.classList.remove('hidden');
        settingsOverlay?.classList.remove('hidden');
    }

    function closeMenu() {
        notificationsMenuContent.classList.add('hidden');
        settingsOverlay?.classList.add('hidden');
    }

    function updateBadgeCount() {
        if (!notificationCount) return;

        const currentCount = parseInt(notificationCount.textContent || '0', 10);
        const newCount = Math.max(0, currentCount - 1);

        if (newCount > 0) {
            notificationCount.textContent = String(newCount);
        } else {
            notificationCount.remove();
        }
    }

    function renderEmptyStateIfNeeded() {
        if (!notificationList) return;

        const items = notificationList.querySelectorAll('.notifications-menu__item');
        if (items.length === 0) {
            notificationList.innerHTML = '<p class="notifications-menu__empty">You have no new notifications.</p>';
        }
    }

    async function markAsRead(notificationId) {
        if (!notificationId || !csrfToken) return false;

        try {
            const response = await fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            });

            return response.ok;
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
            return false;
        }
    }

    notificationsMenuButton.addEventListener('mouseenter', () => {
        isHoveringButton = true;
        openMenu();
    });

    notificationsMenuButton.addEventListener('mouseleave', () => {
        isHoveringButton = false;
        if (!isHoveringContent) closeMenu();
    });

    notificationsMenuContent.addEventListener('mouseenter', () => {
        isHoveringContent = true;
        openMenu();
    });

    notificationsMenuContent.addEventListener('mouseleave', () => {
        isHoveringContent = false;
        if (!isHoveringButton) closeMenu();
    });

    settingsOverlay?.addEventListener('click', closeMenu);

    notificationsMenuContent.addEventListener('click', async (e) => {
        const dismissButton = e.target.closest('.notifications-menu__dismiss');
        const clickableBody = e.target.closest('.notifications-menu__item-body--clickable');

        if (dismissButton) {
            e.preventDefault();
            e.stopPropagation();

            const notificationId = dismissButton.dataset.notificationId;
            const item = document.getElementById(`notification-${notificationId}`);

            const success = await markAsRead(notificationId);

            if (success && item) {
                item.remove();
                updateBadgeCount();
                renderEmptyStateIfNeeded();
            }

            return;
        }

        if (clickableBody) {
            const item = clickableBody.closest('.notifications-menu__item');
            if (!item) return;

            const notificationId = item.dataset.notificationId;
            const targetUrl = item.dataset.url;

            const success = await markAsRead(notificationId);

            if (success) {
                window.location.href = targetUrl;
            }
        }
    });
}