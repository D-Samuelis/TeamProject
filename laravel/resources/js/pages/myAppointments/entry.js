import { initCalendarView } from './calendar.js';
import { initViewSwitcher } from './viewSwitcher.js';
import { displayToday } from '../../utils/today.js';
import initNotificationsMenu from '../../components/notifications/notifications.js';

import { appointmentsData } from '../../config/data/appointments.js';

import { generateDetails } from './details.js';

document.addEventListener('DOMContentLoaded', async () => {
  initNotificationsMenu();
  displayToday('todayDisplay');
  initCalendarView(appointmentsData);
  initViewSwitcher();

  // pack this logic in its own file... my bitch ass is lazy for that now
  const calendarContainer = document.getElementById('calendarContainer');
  
  setTimeout(() => { // select today defaultly...
    const todayCell = calendarContainer.querySelector('.calendar-cell.today');
    if (todayCell) {
      todayCell.classList.add('active-cell');
      const num = todayCell.querySelector('.calendar-day-number');
      if (num) num.classList.add('active-day-number');
      generateDetails(todayCell.dataset.date, 8, 23);
    }
  }, 0);

  calendarContainer.addEventListener('click', (e) => {
    const cell = e.target.closest('.calendar-cell');

    if (!cell || cell.classList.contains('calendar-cell--empty')) return;

    // toggle selected day: remove previous .active-cell and its active-day-number
    const prev = calendarContainer.querySelector('.active-cell');
    if (prev && prev !== cell) {
      prev.classList.remove('active-cell');
      const prevNum = prev.querySelector('.calendar-day-number');
      if (prevNum) prevNum.classList.remove('active-day-number');
    }

    // Add active classes to clicked cell
    if (!cell.classList.contains('active-cell')) {
      cell.classList.add('active-cell');
      const num = cell.querySelector('.calendar-day-number');
      if (num) num.classList.add('active-day-number');
    }

    generateDetails(cell.dataset.date, 8, 23); //fetch from and to later...
  });
});
