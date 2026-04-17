/**
 * calendar.js
 * Optimized for modularity and fixed edge-case selection bugs.
 */
import { daysInMonth, getFirstDayOfMonth } from '../../utils/date.js';

/** @type {string} ISO Date string (YYYY-MM-DD) */
let currentSelectedIso = new Date().toLocaleDateString('en-CA'); // Bezpečný lokálny ISO formát

/** @type {Object} Configuration for range highlighting */
const config = { viewRange: 3 };

export function initCalendar() {
    const calendarContainer = document.getElementById("calendarContainer");
    if (!calendarContainer) return;

    syncSelectedDateFromUI();

    const now = new Date();
    renderCalendar(now.getFullYear(), now.getMonth() + 1);

    setupSelectListeners();
}

/**
 * Clears and re-renders the calendar container
 * @param {number} year
 * @param {number} month
 */
export function renderCalendar(year, month) {
    const container = document.getElementById("calendarContainer");
    if (!container) return;
    
    container.innerHTML = '';
    const calendar = document.createElement('div');
    calendar.className = "calendar";
    
    generateCalendar(calendar, year, month);
    container.appendChild(calendar);
}

/**
 * Builds the calendar grid
 */
function generateCalendar(container, year, month) {
    appendWeekDayHeader(container);

    const grid = document.createElement('div');
    grid.className = 'calendar__grid';

    const range = getActiveRangeKeys();
    const todayIso = toIsoKey(new Date());
    
    appendPaddingDays(grid, year, month);
    appendMonthDays(grid, year, month, todayIso, range);
    appendRemainingDays(grid);

    container.appendChild(grid);
}

/**
 * Populates the grid with days of the current month
 */
function appendMonthDays(grid, year, month, todayIso, range) {
    const totalDays = daysInMonth(year, month);
    const appointments = window.BE_DATA?.appointments || [];

    for (let day = 1; day <= totalDays; day++) {
        const iterDate = new Date(year, month - 1, day, 12, 0, 0); 
        const iterIso = toIsoKey(iterDate);
        
        const classes = [];
        if (iterIso === todayIso) classes.push('calendar__cell--today');
        
        if (iterIso === currentSelectedIso) {
            classes.push('calendar__cell--active');
        } else if (range.others.includes(iterIso)) {
            classes.push('calendar__cell--in-range');
        }
        
        const hasApps = appointments.some(a => toIsoKey(new Date(a.date)) === iterIso);
        const cell = createCellElement(day, classes.join(' '), { day, month, year }, hasApps);
        grid.appendChild(cell);
    }
}

/**
 * Creates a single calendar cell DOM element
 */
function createCellElement(text, extraClass, data, hasIndicator) {
    const cell = document.createElement('div');
    const isPadding = extraClass.includes('--empty');
    cell.className = `calendar__cell ${extraClass}`.trim();
    cell.innerHTML = `<span>${text}</span>`;

    if (hasIndicator && !isPadding) {
        const dot = document.createElement('div');
        dot.className = 'calendar__cell-dot';
        cell.appendChild(dot);
    }

    if (data && !isPadding) {
        const isoStr = toIsoKey(new Date(data.year, data.month - 1, data.day, 12, 0, 0));
        cell.dataset.iso = isoStr;

        cell.onmouseenter = () => highlightHoverRange(cell, true);
        cell.onmouseleave = () => highlightHoverRange(cell, false);
        cell.onclick = () => handleDateSelection(isoStr, data);
    }

    return cell;
}

/**
 * Highlights a block of days on hover
 */
function highlightHoverRange(targetCell, active) {
    if (targetCell.classList.contains('calendar__cell--empty')) return;

    const grid = targetCell.parentElement;
    const realCells = Array.from(grid.querySelectorAll('.calendar__cell:not(.calendar__cell--empty)'));
    const idx = realCells.indexOf(targetCell);
    
    if (idx === -1) return;

    const offset = Math.floor((config.viewRange - 1) / 2);
    for (let i = idx - offset; i <= idx + offset; i++) {
        if (realCells[i]) {
            realCells[i].classList.toggle('calendar__cell--hover-range', active);
        }
    }
}

/**
 * Internal handler for selection
 */
function handleDateSelection(isoStr, data) {
    currentSelectedIso = isoStr;
    const dateObj = new Date(data.year, data.month - 1, data.day, 12, 0, 0);
    
    const el = document.getElementById('selectedDateText');
    if (el) {
        el.textContent = dateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        el.dataset.isoDate = isoStr;
    }

    window.dispatchEvent(new CustomEvent('dateChanged', { detail: isoStr }));
    renderCalendar(data.year, data.month);
}

/**
 * Helper: Manual ISO key (YYYY-MM-DD) to avoid timezone shifts
 */
function toIsoKey(date) {
    const d = new Date(date);
    return d.toLocaleDateString('en-CA');
}

function getActiveRangeKeys() {
    const [y, m, d] = currentSelectedIso.split('-').map(Number);
    const selectedDate = new Date(y, m - 1, d, 12, 0, 0);
    const range = { others: [] };
    const offset = Math.floor((config.viewRange - 1) / 2);
    
    for (let i = -offset; i <= offset; i++) {
        if (i === 0) continue;
        const tempDate = new Date(selectedDate);
        tempDate.setDate(selectedDate.getDate() + i);
        range.others.push(toIsoKey(tempDate));
    }
    return range;
}

function syncSelectedDateFromUI() {
    const el = document.getElementById('selectedDateText');
    if (el?.dataset.isoDate) {
        currentSelectedIso = el.dataset.isoDate;
    }
}

function setupSelectListeners() {
    const m = document.getElementById('calendarMonth');
    const y = document.getElementById('calendarYear');
    const update = () => renderCalendar(parseInt(y.value), parseInt(m.value) + 1);
    m?.addEventListener('change', update);
    y?.addEventListener('change', update);
}

function appendPaddingDays(grid, year, month) {
    let firstDayWeek = getFirstDayOfMonth(year, month);
    firstDayWeek = (firstDayWeek === 0) ? 6 : (firstDayWeek - 1);
    const prevMonthDays = new Date(year, month - 1, 0).getDate();

    for (let i = firstDayWeek - 1; i >= 0; i--) {
        grid.appendChild(createCellElement(prevMonthDays - i, 'calendar__cell--empty'));
    }
}

function appendRemainingDays(grid) {
    const currentCells = grid.children.length;
    for (let i = 1; i <= (42 - currentCells); i++) {
        grid.appendChild(createCellElement(i, 'calendar__cell--empty'));
    }
}

function appendWeekDayHeader(container) {
    const header = document.createElement('div');
    header.className = "calendar__header";
    ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'].forEach(day => {
        const d = document.createElement('div');
        d.className = "calendar__weekday";
        d.textContent = day;
        header.appendChild(d);
    });
    container.appendChild(header);
}