import { initCalendar } from './calendar.js';
import { initCalendarFilters } from './calendarFilter.js';
import { initTimelineLayout } from './timeLineLayout.js';
import { initViewToggle } from './viewToggle.js';
import { initCollapsibleList } from './miniList.js';
import { renderPendingAppointments } from './appointmentsRenderer.js';
import { initStatusFilters } from './filters.js';

document.addEventListener('DOMContentLoaded', async () => {
    initCalendar();
    initCalendarFilters();
    initTimelineLayout();
    initViewToggle();

    initStatusFilters();
    initCollapsibleList('filterList');

    renderPendingAppointments();
    initCollapsibleList('pendingList');
});