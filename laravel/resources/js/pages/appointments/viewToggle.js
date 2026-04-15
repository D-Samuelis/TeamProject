import { APP_VIEW_PREFERENCE_KEY } from '../../config/storageKeys.js';
import { initAppointmentListView } from './listView.js';
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

        const allAppointments = window.BE_DATA.appointments || [];
        const currentUser = window.BE_DATA.user;
        
        const filtered = (currentUser && currentUser.id) 
            ? allAppointments.filter(app => app.user_id === currentUser.id)
            : allAppointments;

        if (isTimeline) {
            initTimelineLayout(filtered, new Date(), 3); 
            window.dispatchEvent(new Event('resize'));
        } else {
            initAppointmentListView(filtered); 
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