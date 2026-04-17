import { renderCalendar } from './calendar.js';
import { initTimelineLayout } from './timeLineLayout.js';
import { initAppointmentListView } from './listView.js';
import { APP_VIEW_PREFERENCE_KEY } from '../../config/storageKeys.js';

export function initAppointmentWatchers() {
    window.addEventListener('dateChanged', (e) => {
        handleDateChange(e.detail);
    });
}

/**
 * Coordinates updates when the date changes
 * @param {string} isoDateString 
 */
function handleDateChange(isoDateString) {
    const selectedDate = new Date(isoDateString);
    const allAppointments = window.BE_DATA?.appointments || [];
    const timelineView = document.getElementById('timelineView');
    const savedView = localStorage.getItem(APP_VIEW_PREFERENCE_KEY) || 'timeline';

    if (savedView === 'timeline' && timelineView && !timelineView.classList.contains('hidden')) {
        initTimelineLayout(allAppointments, selectedDate, 3);
    } else if (savedView === 'list') {
        initAppointmentListView(allAppointments);
    }

    renderCalendar(selectedDate.getFullYear(), selectedDate.getMonth() + 1);
}