export function initTimelineLayout(baseDate = new Date(), daysCount = 3) {
    const container = document.getElementById('timelineContainer');
    if (!container) return;

    container.innerHTML = '';
    
    // Konštanty pre šírku
    const axisWidth = "100px";
    const scrollbarWidth = "6px"; // Musí sedieť s CSS pre ::-webkit-scrollbar

    // HLAVIČKA: Má o jeden stĺpec viac (os + dni + scrollbar_gap)
    const headerGrid = `${axisWidth} repeat(${daysCount}, 1fr) ${scrollbarWidth}`;
    
    // TELO: Má len os + dni (scrollbar sa vykreslí "cez" container)
    const bodyGrid = `${axisWidth} repeat(${daysCount}, 1fr)`;

    const headerWrapper = document.createElement('div');
    headerWrapper.className = 'timeline__header-wrapper';
    headerWrapper.style.display = 'grid';
    headerWrapper.style.gridTemplateColumns = headerGrid;
    
    const bodyWrapper = document.createElement('div');
    bodyWrapper.className = 'timeline__body-wrapper';
    bodyWrapper.id = 'timelineBody';
    bodyWrapper.style.display = 'grid';
    bodyWrapper.style.gridTemplateColumns = bodyGrid;

    container.appendChild(headerWrapper);
    container.appendChild(bodyWrapper);

    // 2. GENERÁVANIE HLAVIČKY (Do headerWrapper)
    const corner = document.createElement('div');
    corner.className = 'timeline__header-corner';
    corner.innerHTML = `
        <div class="appointments__control-group">
            <button class="button button__toggle-left active" id="showTimeline"><i class="fa-solid fa-table-columns"></i></button>
            <button class="button button__toggle-right" id="showList"><i class="fa-solid fa-list"></i></button>
        </div>
    `;
    headerWrapper.appendChild(corner);

    const dates = calculateDates(baseDate, daysCount);
    dates.forEach(date => {
        const isToday = new Date().toDateString() === date.toDateString();
        const header = document.createElement('div');
        header.className = `timeline__day-header ${isToday ? 'is-today' : ''}`;
        
        const dayName = date.toLocaleDateString('en-US', { weekday: 'long' });
        const dayDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });

        header.innerHTML = `
            <div class="column-info">
                <span class="column-info__date">${dayName}, ${dayDate}</span>
                <div class="column-info__stats">
                    <i class="fa-regular fa-calendar-check"></i>
                    <span class="column-info__count">4</span>
                </div>
            </div>
        `;
        headerWrapper.appendChild(header);
    });

    // 3. GENERÁVANIE TELA (Do bodyWrapper)
    const timeAxis = document.createElement('div');
    timeAxis.className = 'timeline__axis';
    for (let i = 0; i < 24; i++) {
        const hour = `${i.toString().padStart(2, '0')}:00`;
        const markerWrapper = document.createElement('div');
        markerWrapper.className = 'timeline__marker-wrapper';
        markerWrapper.innerHTML = `
            <div class="timeline__marker">
                <div class="timeline__point">
                    <div class="timeline__point-stick"></div>
                    <div class="timeline__point-arrow"></div>
                </div>
                <div class="timeline__time">
                    <div class="timeline__dot"></div>
                    <div class="timeline__text">${hour}</div>
                </div>
            </div>
        `;
        timeAxis.appendChild(markerWrapper);
    }
    bodyWrapper.appendChild(timeAxis);

    for (let d = 0; d < daysCount; d++) {
        const dayCol = document.createElement('div');
        dayCol.className = 'timeline__day-column';
        
        for (let i = 0; i < 24; i++) {
            const slotBg = document.createElement('div');
            slotBg.className = 'timeline__slot-grid-line';
            slotBg.dataset.hour = i;
            dayCol.appendChild(slotBg);
        }
        bodyWrapper.appendChild(dayCol);
    }

    // 4. PRIDANIE INDIKÁTORA (Teraz do bodyWrapper!)
    const nowIndicator = document.createElement('div');
    nowIndicator.className = 'timeline__now-indicator';
    nowIndicator.innerHTML = `
        <div class="timeline__now-time"></div>
        <div class="timeline__now-line"></div>
    `;
    bodyWrapper.appendChild(nowIndicator);

    // 5. SCROLL LOGIKA (Teraz na bodyWrapper)
    setTimeout(() => {
        const indicator = bodyWrapper.querySelector('.timeline__now-indicator');
        if (bodyWrapper && indicator) {
            const scrollPos = indicator.offsetTop - (bodyWrapper.clientHeight / 3);
            bodyWrapper.scrollTo({ top: scrollPos, behavior: 'smooth' });
        }
    }, 200);

    updateNowIndicator();
    const delay = syncIndicatorToCurrentTime();
    setTimeout(() => {
        updateNowIndicator();
        setInterval(updateNowIndicator, 60000);
    }, delay);
}

function updateNowIndicator() {
    const indicator = document.querySelector('.timeline__now-indicator');
    const timeLabel = document.querySelector('.timeline__now-time');
    if (!indicator) return;

    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes();

    if (timeLabel) {
        timeLabel.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
    }

    const slotHeight = 80;
    const topPosition = (hours * slotHeight) + (minutes * (slotHeight / 60));

    indicator.style.top = `${topPosition}px`;
}

function syncIndicatorToCurrentTime() {
    const now = new Date();
    return ((60 - now.getSeconds()) * 1000) - now.getMilliseconds();
}

/**
 * Vypočíta pole dátumov pre stĺpce timeline
 * @param {Date} baseDate - Stredový dátum (vybraný v kalendári)
 * @param {number} count - Počet stĺpcov (1, 2 alebo 3)
 * @returns {Date[]} Pole objektov Date
 */
function calculateDates(baseDate, count) {
    const dates = [];
    
    // Výpočet offsetu, aby baseDate bol v strede
    // Ak count = 3, offset je -1 (vľavo), 0 (stred), +1 (vpravo)
    // Ak count = 1, offset je len 0
    const startOffset = Math.floor((count - 1) / 2) * -1;

    for (let i = 0; i < count; i++) {
        const d = new Date(baseDate);
        d.setDate(d.getDate() + startOffset + i);
        dates.push(d);
    }

    return dates;
}