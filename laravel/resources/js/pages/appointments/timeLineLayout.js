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

    console.log("Visible Dates on Timeline:", visibleDates.map(d => d.toDateString()));
    console.log("Appointments to process:", data.length);

    data.forEach(app => {
        const appDate = new Date(app.date);
        
        // Hľadáme zhodu (POZOR: Musia byť rovnaké roky, mesiace aj dni)
        const colIndex = visibleDates.findIndex(d => 
            d.getDate() === appDate.getDate() && 
            d.getMonth() === appDate.getMonth() &&
            d.getFullYear() === appDate.getFullYear()
        );

        if (colIndex > -1) {
            console.log(`Rendering app: ${app.service_name} at col ${colIndex}`);
            
            const startTime = new Date(app.start_at);
            const hours = startTime.getHours();
            const minutes = startTime.getMinutes();
            const duration = app.duration || 60;

            const top = (hours * SLOT_HEIGHT) + (minutes * (SLOT_HEIGHT / 60)) + OFFSET_TOP;
            const height = (duration * (SLOT_HEIGHT / 60));

            const appEl = document.createElement('div');
            // Pridaj si 'timeline__appointment' do CSS (viď nižšie)
            appEl.className = `timeline__appointment is-${app.status}`;
            appEl.style.top = `${top}px`;
            appEl.style.height = `${height}px`;
            
            // Inline štýly pre istotu, ak by CSS nenačítalo
            appEl.style.position = 'absolute';
            appEl.style.left = '5px';
            appEl.style.right = '5px';
            appEl.style.zIndex = '100';

            appEl.innerHTML = `
                <div class="appointment-content">
                    <strong>${hours}:${minutes.toString().padStart(2, '0')}</strong><br>
                    <span>${app.service?.name || 'Service'}</span>
                </div>
            `;

            columns[colIndex].appendChild(appEl);
        } else {
            // Toto nám povie, ak appointment nespadá do zobrazených 3 dní
            console.log(`App date ${appDate.toDateString()} is NOT in visible range.`);
        }
    });
}