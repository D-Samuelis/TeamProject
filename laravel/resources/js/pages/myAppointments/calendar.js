import { getTodayInfo } from '../../utils/today.js';
import { daysInMonth, formatDate, getFirstDayOfMonth } from '../../utils/date.js';

export function initCalendar() {
    const calendarContainer = document.getElementById("calendarContainer");
    if (!calendarContainer) return;

    const now = new Date();
    const currentYear = now.getFullYear();
    const currentMonth = now.getMonth() + 1;

    renderCalendar(currentYear, currentMonth);

    document.getElementById('calendarMonth')?.addEventListener('change', updateFromSelects);
    document.getElementById('calendarYear')?.addEventListener('change', updateFromSelects);
}

function updateFromSelects() {
    const m = document.getElementById('calendarMonth').value;
    const y = document.getElementById('calendarYear').value;
    renderCalendar(parseInt(y), parseInt(m));
}

function renderCalendar(year, month) {
    const container = document.getElementById("calendarContainer");
    container.innerHTML = '';
    
    const calendar = document.createElement('div');
    calendar.className = "calendar";
    
    generateCalendar(calendar, year, month);
    container.appendChild(calendar);
}

function generateCalendar(calendar, year, month) {
    appendWeekDayHeader(calendar);

    const grid = document.createElement('div');
    grid.className = 'calendar__grid';

    let firstDayWeek = getFirstDayOfMonth(year, month);
    firstDayWeek = (firstDayWeek === 0) ? 6 : (firstDayWeek - 1);

    const totalDays = daysInMonth(year, month);
    const today = new Date();
    
    const isCurrentMonth = today.getFullYear() === year && today.getMonth() === (month - 1);

    const prevMonthDays = new Date(year, month - 1, 0).getDate();
    for (let i = firstDayWeek - 1; i >= 0; i--) {
        grid.appendChild(addCellToCalendar(prevMonthDays - i, 'calendar__cell--empty'));
    }

    for (let day = 1; day <= totalDays; day++) {
        let extraClass = null;
        if (isCurrentMonth && day === today.getDate()) {
            extraClass = 'calendar__cell--today';
        }
        
        const cell = addCellToCalendar(day, extraClass, { day, month, year });
        grid.appendChild(cell);
    }

    const currentCells = grid.children.length;
    for (let i = 1; i <= (42 - currentCells); i++) {
        grid.appendChild(addCellToCalendar(i, 'calendar__cell--empty'));
    }

    calendar.appendChild(grid);
}

function addCellToCalendar(text, extraClass = null, data = null) {
    const cell = document.createElement('div');
    cell.className = "calendar__cell" + (extraClass ? ` ${extraClass}` : '');
    cell.textContent = text;

    if (data) Object.assign(cell.dataset, data);

    if (!extraClass?.includes('--empty')) {
        cell.addEventListener('click', function() {
            document.querySelectorAll('.calendar__cell--active').forEach(el => 
                el.classList.remove('calendar__cell--active'));
            this.classList.add('calendar__cell--active');

            const dateStr = `${data.year}-${String(data.month).padStart(2, '0')}-${String(data.day).padStart(2, '0')}`;
            
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            const formatted = new Date(data.year, data.month - 1, data.day).toLocaleDateString('en-US', options);
            document.getElementById('selectedDateText').textContent = formatted;

            window.dispatchEvent(new CustomEvent('dateChanged', { detail: dateStr }));
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