let currentView = 'timeline';

/**
 * Main entry point for the timeline layout
 * @param {Date} baseDate
 * @param {number} daysCount
 */
export function initTimelineLayout(data = [], baseDate = new Date(), daysCount = 3) {
    const container = document.getElementById('timelineContainer');
    if (!container) return;

    container.innerHTML = '';

    const headerWrapper = createWrapper('timeline__header-wrapper', daysCount, true);
    const bodyWrapper = createWrapper('timeline__body-wrapper', daysCount, false, 'timelineBody');

    container.appendChild(headerWrapper);
    container.appendChild(bodyWrapper);

    const dates = calculateDates(baseDate, daysCount);

    renderCorner(headerWrapper);
    renderDayHeaders(headerWrapper, dates);
    renderTimeAxis(bodyWrapper);
    renderDayColumns(bodyWrapper, daysCount);
    
    renderAppointments(bodyWrapper, data, dates);

    renderNowIndicator(bodyWrapper);
    setupAutoScroll(bodyWrapper);
    initIndicatorLoop();
}

/**
 * Creates grid wrappers for header and body
 * @param {string} className
 * @param {number} daysCount
 * @param {boolean} hasScrollbarSpace
 * @param {string} id
 */
function createWrapper(className, daysCount, hasScrollbarSpace, id = '') {
    const el = document.createElement('div');
    el.className = className;
    if (id) el.id = id;
    
    const axisWidth = "100px";
    const scrollbarSpace = hasScrollbarSpace ? "6px" : "";
    el.style.display = 'grid';
    el.style.gridTemplateColumns = `${axisWidth} repeat(${daysCount}, 1fr) ${scrollbarSpace}`;
    
    return el;
}

/**
 * Renders the top-left corner with the view switcher (Columns / List)
 * @param {HTMLElement} parent
 */
function renderCorner(parent) {
    parent.innerHTML = ''; // Vyčistíme starý obsah
    const corner = document.createElement('div');
    corner.className = 'timeline__header-corner';
    
    // Zistíme, ktorý pohľad je aktívny podľa ID elementov v DOM
    const isTimelineActive = !document.getElementById('timelineView').classList.contains('hidden');

    corner.innerHTML = `
        <div class="view-switcher">
            <button class="view-switcher__btn ${isTimelineActive ? 'active' : ''}" id="showTimeline">
                <i class="fa-solid fa-table-columns"></i> Columns
            </button>
            <button class="view-switcher__btn ${!isTimelineActive ? 'active' : ''}" id="showList">
                <i class="fa-solid fa-list"></i> List
            </button>
        </div>
    `;
    
    parent.appendChild(corner);
}

/**
 * Renders day headers with big background numbers
 * @param {HTMLElement} parent
 * @param {Date} baseDate
 * @param {number} daysCount
 */
function renderDayHeaders(parent, dates) {
    dates.forEach(dateObj => {
        const isToday = new Date().toDateString() === dateObj.toDateString();
        const header = document.createElement('div');
        header.className = `timeline__day-header ${isToday ? 'is-today' : ''}`;
        
        const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
        const monthName = dateObj.toLocaleDateString('en-US', { month: 'short' });
        const dayNumber = dateObj.getDate().toString().padStart(2, '0');

        // Tu môžeš pridať filter na dáta, aby si zistil reálny počet:
        const count = window.BE_DATA.appointments.filter(a => 
            new Date(a.date).toDateString() === dateObj.toDateString()
        ).length;

        header.innerHTML = `
            <div class="column-info">
                <span class="column-info__date">${dayName}, ${monthName}</span>
                <div class="column-info__stats">
                    <i class="fa-regular fa-calendar-check"></i>
                    <span class="column-info__count">${count} Appointments</span>
                </div>
            </div>
            <div class="column-date">${dayNumber}</div>
        `;
        parent.appendChild(header);
    });
}

/**
 * Renders the vertical time axis (00:00 - 23:00)
 * @param {HTMLElement} parent
 */
function renderTimeAxis(parent) {
    const timeAxis = document.createElement('div');
    timeAxis.className = 'timeline__axis';
    timeAxis.style.paddingTop = '1.5rem';

    for (let i = 0; i < 24; i++) {
        const hour = `${i.toString().padStart(2, '0')}:00`;
        const marker = document.createElement('div');
        marker.className = 'timeline__marker-wrapper';
        marker.innerHTML = `
            <div class="timeline__marker">
                <div class="timeline__point"><div class="timeline__point-stick"></div><div class="timeline__point-arrow"></div></div>
                <div class="timeline__time"><div class="timeline__dot"></div><div class="timeline__text">${hour}</div></div>
            </div>`;
        timeAxis.appendChild(marker);
    }
    parent.appendChild(timeAxis);
}

/**
 * Renders the grid columns for each day
 * @param {HTMLElement} parent
 * @param {number} daysCount
 */
function renderDayColumns(parent, daysCount) {
    for (let d = 0; d < daysCount; d++) {
        const dayCol = document.createElement('div');
        dayCol.className = 'timeline__day-column';
        
        dayCol.style.paddingTop = '1.5rem'; 

        for (let i = 0; i < 24; i++) {
            const slot = document.createElement('div');
            slot.className = 'timeline__slot-grid-line';
            slot.dataset.hour = i;
            dayCol.appendChild(slot);
        }
        parent.appendChild(dayCol);
    }
}

/**
 * Renders the "Now" time indicator
 * @param {HTMLElement} parent
 */
function renderNowIndicator(parent) {
    const indicator = document.createElement('div');
    indicator.className = 'timeline__now-indicator';
    indicator.innerHTML = `<div class="timeline__now-time"></div><div class="timeline__now-line"></div>`;
    parent.appendChild(indicator);
    updateNowIndicator();
}

/**
 * Updates the position and text of the now indicator
 */
function updateNowIndicator() {
    const indicator = document.querySelector('.timeline__now-indicator');
    const timeLabel = document.querySelector('.timeline__now-time');
    if (!indicator) return;

    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes();

    if (timeLabel) timeLabel.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;

    const slotHeight = 80;
    const offset = 24; // Align with axis padding
    const topPosition = (hours * slotHeight) + (minutes * (slotHeight / 60)) + offset;
    indicator.style.top = `${topPosition}px`;
}

/**
 * Handles initial smooth scroll to current time
 * @param {HTMLElement} bodyWrapper
 */
function setupAutoScroll(bodyWrapper) {
    setTimeout(() => {
        const indicator = bodyWrapper.querySelector('.timeline__now-indicator');
        if (indicator) {
            const scrollPos = indicator.offsetTop - (bodyWrapper.clientHeight / 3);
            bodyWrapper.scrollTo({ top: scrollPos, behavior: 'smooth' });
        }
    }, 200);
}

/**
 * Starts the interval loop for the now indicator
 */
function initIndicatorLoop() {
    const delay = syncIndicatorToCurrentTime();
    setTimeout(() => {
        updateNowIndicator();
        setInterval(updateNowIndicator, 60000);
    }, delay);
}

/**
 * Logic to sync indicator precisely with the next minute
 */
function syncIndicatorToCurrentTime() {
    const now = new Date();
    return ((60 - now.getSeconds()) * 1000) - now.getMilliseconds();
}

/**
 * Calculates array of dates based on baseDate and count
 * @param {Date} baseDate
 * @param {number} count
 * @returns {Date[]}
 */
function calculateDates(baseDate, count) {
    const startOffset = Math.floor((count - 1) / 2) * -1;
    return Array.from({ length: count }, (_, i) => {
        const d = new Date(baseDate);
        d.setDate(d.getDate() + startOffset + i);
        return d;
    });
}

/**
 * Renders appointment blocks into the day columns
 */
function renderAppointments(parent, data, visibleDates) {
    if (!data || !Array.isArray(data)) return;

    const columns = parent.querySelectorAll('.timeline__day-column');
    const SLOT_HEIGHT = 80;
    const OFFSET_TOP = 24;

    visibleDates.forEach((dateObj, colIndex) => {
        const columnEl = columns[colIndex];
        const dayApps = data.filter(app => {
            const d = new Date(app.date);
            return d.getDate() === dateObj.getDate() &&
                   d.getMonth() === dateObj.getMonth() &&
                   d.getFullYear() === dateObj.getFullYear();
        }).sort((a, b) => new Date(a.start_at) - new Date(b.start_at));

        const groups = [];
        dayApps.forEach(app => {
            const start = new Date(app.start_at).getTime();
            const end = start + (app.duration || 60) * 60000;
            
            let placed = false;
            for (let group of groups) {
                const isOverlapping = group.some(item => {
                    const iStart = new Date(item.start_at).getTime();
                    const iEnd = iStart + (item.duration || 60) * 60000;
                    return (start < iEnd && end > iStart);
                });

                if (isOverlapping) {
                    group.push(app);
                    placed = true;
                    break;
                }
            }
            if (!placed) groups.push([app]);
        });

        groups.forEach(group => {
            if (group.length > 2) {
                renderSummaryBlock(columnEl, group, SLOT_HEIGHT, OFFSET_TOP);
            } else {
                group.forEach((app, idx) => {
                    const width = 98 / group.length; // 98% alebo 46%
                    const left = idx * width;
                    renderSingleAppointment(columnEl, app, SLOT_HEIGHT, OFFSET_TOP, width, left);
                });
            }
        });
    });
}

function renderSingleAppointment(columnEl, app, slotHeight, offsetTop, width, left) {
    const startTime = new Date(app.start_at);
    const duration = app.duration || 60;
    const endTime = new Date(startTime.getTime() + duration * 60000);
    
    const top = (startTime.getHours() * slotHeight) + (startTime.getMinutes() * (slotHeight / 60)) + offsetTop;
    const height = (duration * (slotHeight / 60));

    const appEl = document.createElement('div');
    appEl.className = `timeline__appointment is-${app.status}`;
    
    appEl.style.top = `${top}px`;
    appEl.style.height = `${height}px`;
    appEl.style.left = `${left}%`;
    appEl.style.width = `${width}%`;

    const formatTime = (date) => `${date.getHours()}:${date.getMinutes().toString().padStart(2, '0')}`;
    const serviceName = app.service_name || (app.service ? app.service.name : 'Service');

    appEl.innerHTML = `
        <div class="appointment-time-sidebar">
            <span>${formatTime(startTime)}</span>
            <span>${formatTime(endTime)}</span>
        </div>
        <div class="appointment-content-inner">
            <span>${serviceName}</span>
        </div>
    `;
    
    columnEl.appendChild(appEl);
}

function renderSummaryBlock(columnEl, group, slotHeight, offsetTop) {
    const starts = group.map(a => new Date(a.start_at).getTime());
    const minStart = new Date(Math.min(...starts));
    const top = (minStart.getHours() * slotHeight) + (minStart.getMinutes() * (slotHeight / 60)) + offsetTop;
    
    const appEl = document.createElement('div');
    appEl.className = `timeline__appointment is-multiple`;
    appEl.style.top = `${top}px`;
    appEl.style.height = `60px`;

    appEl.innerHTML = `
        <i class="fa-solid fa-layer-group"></i>
        <span>${group.length} Appointments</span>
    `;
    
    appEl.onclick = () => console.log("Rozkliknúť zoznam:", group);
    columnEl.appendChild(appEl);
}