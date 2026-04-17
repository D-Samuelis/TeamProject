import { initCalendar } from './calendar.js';
import { initCalendarFilters } from './calendarFilter.js';
import { initStatusFilters } from './statusFilters.js';
import { initViewToggle } from './viewToggle.js';
import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initAppointmentDetailModal } from './modals/initAppointmentDetailModal.js';
import { initAppointmentWatchers } from './watcher.js';

document.addEventListener('DOMContentLoaded', () => {
    initCalendar();
    initCalendarFilters();
    initStatusFilters();
    initViewToggle();
    initAppointmentDetailModal();
    initCollapsibleList('filterList');

    initAppointmentWatchers(); 
});