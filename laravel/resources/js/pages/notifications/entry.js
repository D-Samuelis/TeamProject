document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('notificationDetailModal');
    const items = document.querySelectorAll('.notifications-history__item--clickable');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!modal || !items.length) return;

    const closeTriggers = modal.querySelectorAll('.modal-close-trigger');
    const messageEl = document.getElementById('notificationModalMessage');
    const dateEl = document.getElementById('notificationModalDate');

    const openModal = ({ message, date, status }) => {
        messageEl.textContent = message || 'Notification';
        dateEl.textContent = date || '-';

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    };

    const markNotificationAsRead = async (item) => {
        const isUnread = item.dataset.isUnread === '1';
        const notificationId = item.dataset.notificationId;

        if (!isUnread || !notificationId || !csrfToken) return;

        try {
            const response = await fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) return;

            item.dataset.isUnread = '0';
            item.dataset.status = 'Read';

            item.classList.remove('notifications-history__item--unread');
            item.classList.add('notifications-history__item--read');

            const badge = item.querySelector('.notifications-history__badge');
            if (badge) {
                badge.remove();
            }

            const message = item.querySelector('.notifications-history__message');
            if (message) {
                message.classList.remove('notifications-history__message--unread');
            }

            const actions = item.querySelector('.notifications-history__item-actions');
            if (actions) {
                actions.innerHTML = `
                    <span class="notifications-history__status notifications-history__status--read">Read</span>
                `;
            }

            statusEl.textContent = 'Read';

            const badgeCount = document.getElementById('notificationCount');
            if (badgeCount) {
                const currentCount = parseInt(badgeCount.textContent || '0', 10);
                const newCount = Math.max(0, currentCount - 1);

                if (newCount > 0) {
                    badgeCount.textContent = String(newCount);
                } else {
                    badgeCount.remove();
                }
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    };

    items.forEach((item) => {
        item.addEventListener('click', async (e) => {
            if (e.target.closest('form') || e.target.closest('button')) return;

            openModal({
                message: item.dataset.message,
                date: item.dataset.date,
            });

            await markNotificationAsRead(item);
        });
    });

    closeTriggers.forEach((trigger) => {
        trigger.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    const urlParams = new URLSearchParams(window.location.search);
    const openNotificationId = urlParams.get('open');

    if (openNotificationId) {
        const item = document.querySelector(
            `.notifications-history__item--clickable[data-notification-id="${openNotificationId}"]`
        );

        if (item) {
            openModal({
                message: item.dataset.message,
                date: item.dataset.date,
            });

            markNotificationAsRead(item);

            const cleanUrl = `${window.location.pathname}`;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    }
});