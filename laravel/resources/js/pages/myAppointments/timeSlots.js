export function initTimeSlots() {
    const container = document.getElementById('timelineContainer');
    if (!container) return;

    container.innerHTML = '';
    
    for (let i = 0; i < 24; i++) {
        const hour = `${i.toString().padStart(2, '0')}:00`;
        
        const slot = document.createElement('div');
        slot.className = 'timeline__slot';
        
        slot.innerHTML = `
            <div class="timeline__time">${hour}</div>
            <div class="timeline__content" data-hour="${i}"></div>
        `;
        
        container.appendChild(slot);
    }
}