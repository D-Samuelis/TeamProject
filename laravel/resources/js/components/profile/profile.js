export default function initProfileMenu() {
    const profileMenuButton = document.getElementById('profileButton');
    const content = document.getElementById('profileMenuContent');
    const overlay = document.getElementById('settingsOverlay');
    const navNotif = document.getElementById('notificationsMenu');

    const notificationsMenuButton = document.getElementById('notificationsMenu');
    
    const info = document.getElementById('profileInfo');
    const icon = document.getElementById('profileIcon');

    if (!profileMenuButton || !content) return;

    function toggleProfile() {
        const isHidden = content.classList.contains('hidden');
        if (isHidden) {
            showProfile();
        } else {
            hideProfile();
        }
    }

    function showProfile() {
        content.classList.remove('hidden');
        overlay?.classList.remove('hidden');
        navNotif?.classList.add('hidden');
        
        info?.classList.add('profile-wanish');
        icon?.classList.add('profile-move-icon');

        notificationsMenuButton.classList.add('hidden');
    }

    function hideProfile() {
        content.classList.add('hidden');
        overlay?.classList.add('hidden');
        navNotif?.classList.remove('hidden');

        info?.classList.remove('profile-wanish');
        icon?.classList.remove('profile-move-icon');

        notificationsMenuButton.classList.remove('hidden');
    }

    profileMenuButton.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleProfile();
    });

    content.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    overlay?.addEventListener('click', hideProfile);

    document.addEventListener('click', (e) => {
        if (!content.classList.contains('hidden')) {
            hideProfile();
        }
    });
}