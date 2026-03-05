import { initCalendar } from './calendar.js';
import { initCalendarFilters } from './calendarFilter.js';
import { initTimeSlots } from './timeSlots.js';
import { initViewToggle } from './viewToggle.js';
import { initCollapsibleList } from './miniList.js';
import { renderPendingAppointments } from './appointmentsRenderer.js';
import { initStatusFilters } from './filters.js';

document.addEventListener('DOMContentLoaded', async () => {
    initCalendar();
    initCalendarFilters();
    initTimeSlots();
    initViewToggle();

    initStatusFilters();
    initCollapsibleList('filterList');

    renderPendingAppointments();
    initCollapsibleList('pendingList');
});