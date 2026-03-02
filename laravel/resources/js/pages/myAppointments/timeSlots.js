export function initTimeSlots() {
    const container = document.getElementById('timelineContainer');
    if (!container) return;

    container.innerHTML = '';

    const nowIndicator = document.createElement('div');
    nowIndicator.className = 'timeline__now-indicator';
    nowIndicator.innerHTML = `
        <div class="timeline__now-time"></div>
        <div class="timeline__now-line"></div>
    `;
    container.appendChild(nowIndicator);
    
    for (let i = 0; i < 24; i++) {
        const hour = `${i.toString().padStart(2, '0')}:00`;
        const slot = document.createElement('div');
        slot.className = 'timeline__slot';
        
        slot.innerHTML = `
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
            <div class="timeline__content" data-hour="${i}"></div>
        `;
        
        container.appendChild(slot);
    }

    setTimeout(() => {
        const container = document.getElementById('timelineContainer');
        const indicator = document.querySelector('.timeline__now-indicator');
        
        if (container && indicator) {
            const scrollPos = indicator.offsetTop - (container.clientHeight / 3);
            container.scrollTo({
                top: scrollPos,
                behavior: 'smooth'
            });
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
    if (!indicator || !timeLabel) return;

    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes();

    timeLabel.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;

    const slotHeight = 80; 
    const topPosition = (hours * slotHeight) + (minutes * (slotHeight / 60) + 16);

    indicator.style.top = `${topPosition}px`;
}

function syncIndicatorToCurrentTime() {
    const now = new Date();
    const seconds = now.getSeconds();
    const ms = now.getMilliseconds();

    const msUntilNextMinute = ((60 - seconds) * 1000) - ms;

    return msUntilNextMinute;
}