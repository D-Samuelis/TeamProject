export function initDaysOverlay(centerDate = new Date()) {
    const parentContainer = document.getElementById("timelineContainer");
    if (!parentContainer) return;

    // Odstránime starú hlavičku, ak existuje
    const existing = document.querySelector('.timeline-days-overlay');
    if (existing) existing.remove();

    // Vytvoríme novú hlavičku
    const overlay = document.createElement('div');
    overlay.className = 'timeline-days-overlay';
    
    // Logika pre "Yesterday, Today, Tomorrow"
    // Ak chceš 3 dni: [dátum-1, dátum, dátum+1]
    const days = [-1, 0, 1]; 
    
    days.forEach(offset => {
        const date = new Date(centerDate);
        date.setDate(date.getDate() + offset);
        
        const col = document.createElement('div');
        col.className = 'day-header';
        col.innerHTML = `
            <span class="day-name">${date.toLocaleDateString('sk-SK', { weekday: 'short' })}</span>
            <span class="day-num">${date.getDate()}</span>
        `;
        overlay.appendChild(col);
    });

    // Vložíme ju priamo nad timeline
    parentContainer.parentElement.insertBefore(overlay, parentContainer);
}