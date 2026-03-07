const mockAppointments = [
    { id: 1, time: '09:00', title: 'Strihanie Brady', client: 'Jozef Mrkva', type: 'pending' },
    { id: 2, time: '10:30', title: 'Kompletka', client: 'Marek Hrach', type: 'pending' },
    { id: 3, time: '14:00', title: 'Úprava vlasov', client: 'Anna Jablková', type: 'confirmed' }
];

export function renderPendingAppointments() {
    const container = document.getElementById('pendingList');
    if (!container) return;

    container.innerHTML = '';

    const pending = mockAppointments.filter(app => app.type === 'pending');

    if (pending.length === 0) {
        container.innerHTML = '<p class="u-none">No pending appointments</p>';
        return;
    }

    pending.forEach(app => {
        const item = document.createElement('div');
        item.className = 'appointment-mini-card';
        item.innerHTML = `
            <div class="appointment-mini-card__time">${app.time}</div>
            <div class="appointment-mini-card__details">
                <span class="appointment-mini-card__title">${app.title}</span>
                <span class="appointment-mini-card__client">${app.client}</span>
            </div>
            <div class="appointment-mini-card__actions">
                <i class="fa-solid fa-check btn-approve"></i>
                <i class="fa-solid fa-xmark btn-reject"></i>
            </div>
        `;
        container.appendChild(item);
    });
}