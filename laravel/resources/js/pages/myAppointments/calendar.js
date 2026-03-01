import { getTodayInfo } from '../../utils/today.js';
import { daysInMonth, formatDate, getFirstDayOfMonth } from '../../utils/date.js';

export function initCalendar() {
    const calendarContainer = document.getElementById("calendarContainer");
    const calendar = createDiv("calendar");
    generateCalendar_v2(calendar, 2026, 1);
    calendarContainer.appendChild(calendar);

    console.log(getTodayInfo());
    console.log(calendar);
}

function generateCalendar_v2(calendar, year, month) {
    calendar.innerHTML = '';
    const grid = document.createElement('div');
    grid.className = 'calendar__grid';

    appendWeekDayHeader(calendar);

    let firstDayWeek = getFirstDayOfMonth(year, month);
    firstDayWeek = (firstDayWeek === 0) ? 6 : (firstDayWeek - 1); // Convert Sunday based week to Monday based
    const totalDays = daysInMonth(year, month);
    
    const today = new Date();
    const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;

    const prevMonthDays = new Date(year, month, 0).getDate();
    for (let i = firstDayWeek - 1; i >= 0; i--) {
        const dayNum = prevMonthDays - i;
        grid.appendChild(addCellToCalendar_v2(dayNum, 'calendar__cell--empty'));
    }

    for (let day = 1; day <= totalDays; day++) {
        let extraClass = null;
        if (isCurrentMonth && day === today.getDate()) {
            extraClass = 'calendar__cell--today';
        }
        
        const cell = addCellToCalendar_v2(day, extraClass, { day: day });
        grid.appendChild(cell);
    }

    const currentCells = grid.children.length;
    const remainingCells = 42 - currentCells; 
    
    for (let i = 1; i <= remainingCells; i++) {
        grid.appendChild(addCellToCalendar_v2(i, 'calendar__cell--empty'));
    }

    calendar.appendChild(grid);
}

function addCellToCalendar_v2(text, extraClass = null, data = null) {
    let className = "calendar__cell";
    if (extraClass) className += ` ${extraClass}`;

    const cell = createDiv(className, null, data, text);

    if (!extraClass?.includes('--empty')) {
        cell.addEventListener('click', function() {
            document.querySelectorAll('.calendar__cell--active').forEach(el => {
                el.classList.remove('calendar__cell--active');
            });

            this.classList.add('calendar__cell--active');
        });
    }

    return cell;
}

function appendWeekDayHeader(container) {
    const parrent = createDiv("calendar__header");

    ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'].forEach(day => {
        const div = createDiv("calendar__weekday", null, null, day);

        /*if (currentDay.startsWith(div.textContent)) div.classList.add('active-date');*/
        parrent.appendChild(div);
    });

    container.appendChild(parrent);
}

function createDiv(className, id, data, textContent) {
    const div = document.createElement('div');
    
    if (className) div.className = className;
    if (id) div.id = id;
    if (textContent) div.textContent = textContent;

    if (data && typeof data === 'object') {
        Object.assign(div.dataset, data);
    }

    return div;
}

/**
 * Calendar view handler for myAppointments page
 */

import { getAppointmentsForDate } from '../../utils/appointment.js';

const info = getTodayInfo();
let currentYear = info.year;
let currentMonth = info.monthNumber - 1;
let currentDay = info.day;
let currentDayNumber = info.dayNumber;
let selectedMonth = currentMonth;

/**
 * Generate and render the calendar for a specific month
 * @param {number} year
 * @param {number} month - 0-indexed
 */
export function generateCalendar(year, month) {
    const container = document.getElementById('calendarContainer');
    if (!container) {
        console.warn('[calendarView] #calendarContainer not found');
        return;
    }

    container.innerHTML = '';

    let firstDayWeek = getFirstDayOfMonth(year, month);
    // Convert Sunday (0) to 6 for Monday-based week
    firstDayWeek = (firstDayWeek === 0) ? 6 : (firstDayWeek - 1);
    
    const totalDays = daysInMonth(year, month);

    const calendar = document.createElement('div');
    calendar.className = 'calendar-grid';

    addWeekdayHeader(calendar);

    const totalDaysPrevMonth = getEmptyCellsBeforeFirstDay(year, month);
    addEmptyCellsBeforeMonth(calendar, firstDayWeek, totalDaysPrevMonth);

    addCellsCurrentMonth(calendar, totalDays, year, month, currentDayNumber);

    const totalCells = firstDayWeek + totalDays;
    const remainingCells = 7 * 6 - totalCells;
    addEmptyCellsAfterMonth(calendar, remainingCells);

    container.appendChild(calendar);
}

function getCellTypeName(type) {
    switch(type) {
        case "empty":
            return "calendar-cell calendar-cell--empty"
        default:
            return "calendar-cell"
    }
} 

/**
 * Adds empty cells to calendar (bleached cells to make calendar consistentn width and height)
 * @param {HTMLElement} container - master container for appending
 * @param {string} date
 * @param {string} type - cell type [ empty/default ]
 */
function addCellToCalendar(container, date, type="empty") {
    const div = document.createElement('div');
    div.className = getCellTypeName(type)
    const number = document.createElement('div');
    number.className = 'calendar-day-number';
    number.textContent = date;
    div.appendChild(number);
    container.appendChild(div);
    return div;
}

function addEmptyCellsAfterMonth(container, remainingCells) {
    for (let i = 1; i <= remainingCells; i++) {
        addCellToCalendar(container, i, "empty");
    }
}

/**
 * Adds appointments to cell in calendar
 * @param {HTMLElement} div - cell for adding appointments to
 * @param {string} dateStr - date
 */
function addAppointmentsToCell(div, dateStr) {
    const appointments = getAppointmentsForDate(dateStr);
    if (appointments.length > 0) {
        div.classList.add('calendar-cell--has-appointments');
        div.title = appointments
            .map(a => `${a.title} (${a.duration} min)`)
            .join('\n');
    }
}

/**
 * Adds empty cells to calendar (bleached cells to make calendar consistentn width and height)
 * @param {HTMLElement} container - master container for appending
 * @param {number} totalDays - number of days in current month
 * @param {number} year
 * @param {number} month
 * @param {number} currentDayNumber
 */
function addCellsCurrentMonth(container, totalDays, year, month, currentDayNumber) {
    for (let day = 1; day <= totalDays; day++) {
        const div = addCellToCalendar(container, day, "non-empty");

        const dateStr = formatDate(new Date(year, month, day));

        div.dataset.date = dateStr;
        if (div.textContent == currentDayNumber && month == info.monthNumber - 1 && year == info.year) {
            div.classList.add('today');
        }

        addAppointmentsToCell(div, dateStr);
    }
}

/**
 * Adds empty cells to calendar (bleached cells to make calendar consistentn width and height)
 * @param {HTMLElement} container - master container for appending
 * @param {number} firstDayWeek - when is monday...
 * @param {number} totalDaysPrevMonth - number of days from previous month
 */
function addEmptyCellsBeforeMonth(container, firstDayWeek, totalDaysPrevMonth) {
    for (let i = 1; i <= firstDayWeek; i++) {
        addCellToCalendar(container, totalDaysPrevMonth - firstDayWeek + 1 + i, "empty");
    }
}

function getEmptyCellsBeforeFirstDay(year, month) {
    return month == 0 ? daysInMonth(year - 1, 11) : daysInMonth(year, month - 1);
}

/**
 * Adds weekday header to calendar
 * @param {HTMLElement} container - master container for appending
 */
function addWeekdayHeader(container) {
    ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'].forEach(d => {
        const header = document.createElement('div');
        header.textContent = d;
        header.className = 'calendar-weekday';

        if (currentDay.startsWith(header.textContent)) header.classList.add('active-date');
        
        container.appendChild(header);
    });
}

/**
 * Update year display
 */
export function updateYearDisplay() {
    const yearSpan = document.getElementById('currentYear');
    if (yearSpan) {
        yearSpan.textContent = currentYear;
    }
}

/**
 * Initialize calendar view with event listeners
 */
export function initCalendarView(appointments) {
    console.log(appointments);
    // Year navigation
    const prevYearBtn = document.getElementById('prevYear');
    const nextYearBtn = document.getElementById('nextYear');

    if (prevYearBtn) {
        prevYearBtn.addEventListener('click', () => {
            currentYear--;
            updateYearDisplay();
            generateCalendar(currentYear, currentMonth);
        });
    }

    if (nextYearBtn) {
        nextYearBtn.addEventListener('click', () => {
            currentYear++;
            updateYearDisplay();
            generateCalendar(currentYear, currentMonth);
        });
    }

    // Month buttons
    const monthButtons = document.querySelectorAll('.month-button');
    monthButtons.forEach((btn, index) => {
        btn.addEventListener('click', (e) => {
            currentMonth = index;

            if (selectedMonth === currentMonth) return;

            monthButtons.forEach(b => b.classList.remove('active-month'));
            btn.classList.add('active-month');
            generateCalendar(currentYear, currentMonth);

            console.log(selectedMonth);
            console.log(currentMonth); //switched to
            console.log(getCurrentMonthAnimationDirection(selectedMonth, currentMonth));
            animateMonthTransition(getCurrentMonthAnimationDirection(selectedMonth, currentMonth));
            selectedMonth = currentMonth; //update selected month to the current month after switching
            console.log("switching month");

        });
    });

    // Initial render
    /*
    generateCalendar(currentYear, currentMonth);

    highlightCurrentMonth(currentMonth);

    updateYearDisplay();*/
}

function animateMonthTransition() {
    const calendar = document.querySelector('.calendar-grid');
    if (!calendar) return;

    calendar.style.transition = "transform 0.3s ease, opacity 0.3s ease";
    calendar.style.transform = "translateY(0)";
    calendar.style.opacity = "1";

    requestAnimationFrame(() => {
        calendar.style.transform = "translateY(-50rem)";
        calendar.style.opacity = "0";
    });

    calendar.addEventListener("transitionend", () => {
        calendar.style.transform = "translateY(0)";
        calendar.style.opacity = "1";
    }, { once: true });
}


function getCurrentMonthAnimationDirection(selectedMonth, currentMonth) {
    if (selectedMonth === currentMonth) return null;
    return (selectedMonth < currentMonth) ? 'up' : 'down';
}

function highlightCurrentMonth(currentMonth) {
    const monthsContainer = document.getElementById("monthsContainer");
    const buttons = monthsContainer.children;
    if (buttons[currentMonth]) {
        buttons[currentMonth].classList.add("active-month");
    }
}
