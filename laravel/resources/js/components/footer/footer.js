document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('settingsOverlay');
    const footer = document.querySelector('footer');
    if (!footer) return;

    let navPanel = document.getElementById('footer-nav-panel');
    if (!navPanel) {
        navPanel = document.createElement('div');
        navPanel.id = 'footer-nav-panel';
        navPanel.className = 'footer-nav-panel';
        navPanel.innerHTML = `
            <div class="footer-nav-columns">
                <div class="footer-nav-col left">
                    <div class="footer-nav-title">Booking</div>
                    <a href="/">Book Appointment</a><br>
                    <a href="/my-appointments">My Appointments</a>
                </div>
                <div class="footer-nav-col center">
                    <div class="footer-nav-title">Management</div>
                    <a href="/dashboard">Dashboard</a>
                </div>
                <div class="footer-nav-col right">
                    <div class="footer-nav-title">Contact</div>
                    <a href="mailto:bexora.management@gmail.com">bexora.management@gmail.com</a>
                </div>
            </div>
            <div class="trademark-in-footer">
                © 2026 BEXORA
            </div>
        `;
        document.body.appendChild(navPanel);
    }


    function showFooterNav() {
        navPanel.classList.remove('hidden');
        navPanel.classList.add('visible');
        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.classList.add('visible');
        }
    }

    function hideFooterNav() {
        navPanel.classList.remove('visible');
        if (overlay) {
            overlay.classList.remove('visible');
            overlay.classList.add('hidden');
        }
    }

    function toggleFooterNav() {
        const isVisible = navPanel.classList.contains('visible');
        if (!isVisible) {
            showFooterNav();
        } else {
            hideFooterNav();
        }
    }

    footer.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleFooterNav();
    });

    navPanel.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    overlay?.addEventListener('click', hideFooterNav);

    document.addEventListener('click', () => {
        if (navPanel.classList.contains('visible')) {
            hideFooterNav();
        }
    });
});