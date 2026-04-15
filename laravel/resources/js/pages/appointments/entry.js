import { initCalendar } from './calendar.js';
import { initCalendarFilters } from './calendarFilter.js';
import { initStatusFilters } from './statusFilters.js';
import { initViewToggle } from './viewToggle.js';
import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initAppointmentDetailModal } from './modals/initAppointmentDetailModal.js';
import { initTimelineLayout } from './timeLineLayout.js';

document.addEventListener('DOMContentLoaded', () => {
    initCalendar();
    initCalendarFilters();
    
    initStatusFilters();
    initCollapsibleList('filterList');
    
    initViewToggle(); 
    initAppointmentDetailModal();
});

window.addEventListener('dateChanged', (e) => {
    const selectedDate = new Date(e.detail);
    const allAppointments = window.BE_DATA.appointments || [];
    
    const timelineView = document.getElementById('timelineView');
    if (timelineView && !timelineView.classList.contains('hidden')) {
        initTimelineLayout(allAppointments, selectedDate, 3);
    }
});