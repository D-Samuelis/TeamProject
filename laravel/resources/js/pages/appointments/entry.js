import { initCalendar } from './calendar.js';
import { initCalendarFilters } from './calendarFilter.js';
import { initViewToggle } from './viewToggle.js';
import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initAppointmentDetailModal } from './modals/initAppointmentDetailModal.js';
import { initAppointmentWatchers } from './watcher.js';
import { initUserSearch } from '../../components/displays/userSearch.js';

document.addEventListener('DOMContentLoaded', () => {
    initCalendar();
    initCalendarFilters();
    initViewToggle();
    initAppointmentDetailModal();
    initCollapsibleList('filterList');
    initUserSearch();

    initAppointmentWatchers();
});
