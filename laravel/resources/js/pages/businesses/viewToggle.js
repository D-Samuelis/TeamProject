const BUSINESS_VIEW_KEY = 'business_view_preference';

export function initBusinessViewSwitcher() {
    const teamView     = document.getElementById('businessTeamView');
    const servicesView = document.getElementById('businessServicesView');

    if (!teamView || !servicesView) return;

    const savedView = localStorage.getItem(BUSINESS_VIEW_KEY) || 'team';

    function switchView(target) {
        const isTeam = target === 'team';

        teamView.classList.toggle('hidden', !isTeam);
        servicesView.classList.toggle('hidden', isTeam);

        document.querySelectorAll('#showTeam').forEach(b => {
            b.classList.toggle('active', isTeam);
        });
        document.querySelectorAll('#showServices').forEach(b => {
            b.classList.toggle('active', !isTeam);
        });

        localStorage.setItem(BUSINESS_VIEW_KEY, target);
    }

    switchView(savedView);

    document.addEventListener('click', (e) => {
        if (e.target.closest('#showTeam'))     switchView('team');
        if (e.target.closest('#showServices')) switchView('services');
    });
}