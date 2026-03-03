import { initCalendar } from './calendar.js';
import { initCalendarFilters } from './calendarFilter.js';
import { initTimeSlots } from './timeSlots.js';
import { initViewSwitcher } from './viewSwitcher.js';
import { initCollapsibleList } from './miniList.js';

document.addEventListener('DOMContentLoaded', async () => {
    initCalendar();
    initCalendarFilters();
    initTimeSlots();
    initViewSwitcher();

    initCollapsibleList('filterList');
    initCollapsibleList('pendingList');
});