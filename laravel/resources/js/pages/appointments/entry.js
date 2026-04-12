import { initCalendar } from './calendar.js';
import { initCalendarFilters } from './calendarFilter.js';
import { initStatusFilters } from './statusFilters.js';
import { initViewToggle } from './viewToggle.js';
import { initCollapsibleList } from '../../components/miniLists/miniList.js';

document.addEventListener('DOMContentLoaded', () => {
    initCalendar();
    initCalendarFilters();
    
    initStatusFilters();
    initCollapsibleList('filterList');
    
    initViewToggle(); 
});

window.addEventListener('dateChanged', (e) => {
    const selectedDate = new Date(e.detail);
    const allAppointments = window.BE_DATA.appointments || [];
    
    // Ak je zapnutý timeline view, prekresli ho na nový dátum
    const timelineView = document.getElementById('timelineView');
    if (timelineView && !timelineView.classList.contains('hidden')) {
        initTimelineLayout(allAppointments, selectedDate, 3);
    }
});