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
                <div class="timeline__point"></div>
                <div class="timeline__time">${hour}</div>
            </div>
            <div class="timeline__content" data-hour="${i}"></div>
        `;
        
        container.appendChild(slot);
    }

    updateNowIndicator();

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

    setInterval(updateNowIndicator, 60000);
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
    const topPosition = (hours * slotHeight) + (minutes * (slotHeight / 60));

    indicator.style.top = `${topPosition}px`;
}