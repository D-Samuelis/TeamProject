import { APP_VIEW_PREFERENCE_KEY } from '../../config/storageKeys.js';

export function initViewToggle() {
    const listView = document.getElementById('listView');
    
    const savedView = localStorage.getItem(APP_VIEW_PREFERENCE_KEY) || 'timeline';

    function switchView(targetKey) {
        const isTimeline = targetKey === 'timeline';
        
        timelineView?.classList.toggle('hidden', !isTimeline);
        listView?.classList.toggle('hidden', isTimeline);

        document.querySelectorAll('#showTimeline').forEach(b => b.classList.toggle('active', isTimeline));
        document.querySelectorAll('#showList').forEach(b => b.classList.toggle('active', !isTimeline));

        localStorage.setItem(APP_VIEW_PREFERENCE_KEY, targetKey);

        if (isTimeline) {
            window.dispatchEvent(new Event('resize'));
        }
    }

    switchView(savedView);

    document.addEventListener('click', (e) => {
        const btnTimeline = e.target.closest('#showTimeline');
        const btnList = e.target.closest('#showList');

        if (btnTimeline) {
            e.preventDefault();
            switchView('timeline');
        }
        
        if (btnList) {
            e.preventDefault();
            switchView('list');
        }
    });
}