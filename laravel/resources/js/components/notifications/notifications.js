export default function initNotificationsMenu() {
    const notificationsMenuButtton = document.getElementById('notificationsMenu');
    const notificationsMenuContent = document.getElementById('notificationsMenuContent');
    const settingsOverlay = document.getElementById('settingsOverlay');

    if (!notificationsMenuButtton || !notificationsMenuContent) {
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

    // Hover icon
    notificationsMenuButtton.addEventListener('mouseenter', () => {
        isHoveringButton = true;
        openMenu();
    });

    notificationsMenuButtton.addEventListener('mouseleave', () => {
        isHoveringButton = false;
        if (!isHoveringContent) closeMenu();
    });

    // Hover content
    notificationsMenuContent.addEventListener('mouseenter', () => {
        isHoveringContent = true;
        openMenu();
    });

    notificationsMenuContent.addEventListener('mouseleave', () => {
        isHoveringContent = false;
        if (!isHoveringButton) closeMenu();
    });

    // Close on overlay click
    settingsOverlay?.addEventListener('click', closeMenu);
}