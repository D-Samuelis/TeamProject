import { initCalendar } from './calendar.js';
import { initCalendarFilters } from './calendarFilter.js';
import { initTimeSlots } from './timeSlots.js';

document.addEventListener('DOMContentLoaded', async () => {
    initCalendar();
    initCalendarFilters();
    initTimeSlots();
});