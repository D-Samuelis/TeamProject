export default function initProfileMenu() {
    const profileMenuButton = document.getElementById('profileButton');
    const content = document.getElementById('profileMenuContent');
    const overlay = document.getElementById('settingsOverlay');
    const navNotif = document.getElementById('notificationsMenu');
    
    const info = document.getElementById('profileInfo');
    const icon = document.getElementById('profileIcon');

    if (!profileMenuButton || !content) return;

    let isHoveringButton = false;
    let isHoveringContent = false;

    function showProfile() {
        content.classList.remove('hidden');
        overlay?.classList.remove('hidden');
        navNotif?.classList.add('hidden');
        
        info?.classList.add('profile-wanish');
        icon?.classList.add('profile-move-icon');
    }

    function hideProfile() {
        content.classList.add('hidden');
        overlay?.classList.add('hidden');
        navNotif?.classList.remove('hidden');

        info?.classList.remove('profile-wanish');
        icon?.classList.remove('profile-move-icon');
    }

    // Hover icon
    profileMenuButton.addEventListener('mouseenter', () => {
        isHoveringButton = true;
        showProfile();
    });

    profileMenuButton.addEventListener('mouseleave', () => {
        isHoveringButton = false;
        
        setTimeout(() => {
            if (!isHoveringContent) hideProfile();
        }, 50); 
    });

    // Hover content
    content.addEventListener('mouseenter', () => {
        isHoveringContent = true;
        showProfile();
    });

    content.addEventListener('mouseleave', () => {
        isHoveringContent = false;
        if (!isHoveringButton) hideProfile();
    });

    // Close on overlay click
    overlay?.addEventListener('click', hideProfile);
}