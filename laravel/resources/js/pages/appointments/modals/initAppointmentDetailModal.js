import { Modal } from '../../../components/displays/modal.js';

export function initAppointmentDetailModal() {
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('.js-open-appointment-detail');
        if (!trigger) return;

        const rawData = trigger.dataset.appointments;
        if (!rawData) return;

        try {
            const appointments = JSON.parse(rawData);
            
            Modal.showCustom({
                title: appointments.length > 1 ? `Group Detail (${appointments.length})` : `Appointment Detail`,
                action: 'info',
                confirmText: 'Cancel',
                body: `
                    <div class="apt-modal">
                        ${appointments.map(app => renderAppCard(app)).join('')}
                    </div>
                `,
                onConfirm: (modal) => modal.remove()
            });
        } catch (err) {
            console.error("Parse error", err);
        }
    });
}

function renderAppCard(app) {
    const start = new Date(app.start_at);
    const end = new Date(start.getTime() + (app.duration || 60) * 60000);
    const timeStr = `${start.getHours()}:${String(start.getMinutes()).padStart(2, '0')} - ${end.getHours()}:${String(end.getMinutes()).padStart(2, '0')}`;
    
    // Extrakcia dát z tvojho objektu
    const serviceName = app.service?.name || app.service_name || 'General Service';
    const clientName = app.user?.name || 'Walk-in Client';
    const clientEmail = app.user?.email || 'No email provided';
    const assetName = app.asset?.name || 'Standard Station';

    return `
        <div class="apt-card is-${app.status}">
            <div class="apt-card__header">
                <div class="apt-card__time">
                    <i class="fa-regular fa-clock"></i> ${timeStr}
                </div>
                <span class="apt-badge apt-badge--${app.status}">${app.status}</span>
            </div>
            
            <div class="apt-card__body">
                <div class="apt-card__section">
                    <label>Service & Resource</label>
                    <div class="apt-card__title">${serviceName}</div>
                    <div class="apt-card__subtitle"><i class="fa-solid fa-location-dot"></i> ${assetName}</div>
                </div>

                <div class="apt-card__grid">
                    <div class="apt-card__section">
                        <label>Customer</label>
                        <div class="apt-card__info"><strong>${clientName}</strong></div>
                        <div class="apt-card__subinfo">${clientEmail}</div>
                    </div>
                    <div class="apt-card__section">
                        <label>Duration</label>
                        <div class="apt-card__info">${app.duration} min</div>
                    </div>
                </div>

                ${app.note ? `
                <div class="apt-card__note">
                    <i class="fa-solid fa-quote-left"></i>
                    <p>${app.note}</p>
                </div>
                ` : ''}
            </div>
        </div>
    `;
}