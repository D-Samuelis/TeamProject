import { APP_VIEW_PREFERENCE_KEY } from '../../config/storageKeys.js';

export function initViewToggle() {
    const views = {
        timeline: {
            btn: document.getElementById('showTimeline'),
            container: document.getElementById('timelineView')
        },
        list: {
            btn: document.getElementById('showList'),
            container: document.getElementById('listView')
        }
    };

    if (!views.timeline.btn || !views.list.btn) return;

    const savedView = localStorage.getItem(APP_VIEW_PREFERENCE_KEY) || 'timeline';

    function switchView(targetKey) {
        Object.keys(views).forEach(key => {
            const isTarget = key === targetKey;
            
            views[key].container?.classList.toggle('hidden', !isTarget);
            views[key].btn?.classList.toggle('active', isTarget);
        });

        localStorage.setItem(APP_VIEW_PREFERENCE_KEY, targetKey);

        if (targetKey === 'timeline') {
            window.dispatchEvent(new Event('resize'));
        }
    }

    switchView(savedView);

    views.timeline.btn.addEventListener('click', () => switchView('timeline'));
    views.list.btn.addEventListener('click', () => switchView('list'));
}