import { APP_VIEW_PREFERENCE_KEY } from '../../config/storageKeys.js';
import { initListView } from './listLayout.js';
import { initTimelineLayout } from './timeLineLayout.js';

export function initViewToggle() {
    const timelineView = document.getElementById('timelineView');
    const listView = document.getElementById('listView');
    
    const savedView = localStorage.getItem(APP_VIEW_PREFERENCE_KEY) || 'timeline';

    function switchView(targetKey) {
        const isTimeline = targetKey === 'timeline';
        
        if (timelineView) timelineView.classList.toggle('hidden', !isTimeline);
        if (listView) listView.classList.toggle('hidden', isTimeline);

        localStorage.setItem(APP_VIEW_PREFERENCE_KEY, targetKey);

        if (isTimeline) {
            initTimelineLayout(new Date(), 3);
            window.dispatchEvent(new Event('resize'));
        } else {
            const mockAppointments = [
                { date: 'Mar 7, 2026', time: '10:00', duration: '1h', service: 'Fade + Beard', status: 'Confirmed' },
                { date: 'Mar 8, 2026', time: '11:30', duration: '1h 30m', service: 'Classic Cut', status: 'Reserved' }
            ];
            initListView(mockAppointments); 
        }

        syncActiveButtons(isTimeline);
    }

    function syncActiveButtons(isTimeline) {
        document.querySelectorAll('#showTimeline').forEach(b => {
            isTimeline ? b.classList.add('active') : b.classList.remove('active');
        });
        document.querySelectorAll('#showList').forEach(b => {
            isTimeline ? b.classList.remove('active') : b.classList.add('active');
        });
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