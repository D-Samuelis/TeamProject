import { APP_VIEW_PREFERENCE_KEY } from '../../config/storageKeys.js';

export function initViewToggle() {
    const timelineView = document.getElementById('timelineView');
    const listView = document.getElementById('listView');
    const savedView = localStorage.getItem(APP_VIEW_PREFERENCE_KEY) || 'timeline';

    function applyView(targetKey) {
        const isTimeline = targetKey === 'timeline';
        if (timelineView) timelineView.classList.toggle('hidden', !isTimeline);
        if (listView) listView.classList.toggle('hidden', isTimeline);
        
        localStorage.setItem(APP_VIEW_PREFERENCE_KEY, targetKey);
        syncActiveButtons(isTimeline);

        window.dispatchEvent(new CustomEvent('viewChanged', { detail: targetKey }));
    }

    function syncActiveButtons(isTimeline) {
        document.querySelectorAll('#showTimeline').forEach(b => b.classList.toggle('active', isTimeline));
        document.querySelectorAll('#showList').forEach(b => b.classList.toggle('active', !isTimeline));
    }

    applyView(savedView);

    document.addEventListener('click', (e) => {
        const btnTimeline = e.target.closest('#showTimeline');
        const btnList = e.target.closest('#showList');

        if (btnTimeline) { e.preventDefault(); applyView('timeline'); }
        if (btnList) { e.preventDefault(); applyView('list'); }
    });
}