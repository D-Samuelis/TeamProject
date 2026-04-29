import { Modal } from '../../../components/displays/modal.js';

export function initServiceConnectionsModal(data = null) {
    document.addEventListener('click', (e) => {
        // Podpora pre starý trigger aj nový toolbar trigger
        const trigger = e.target.closest('.js-open-service-connections, [data-modal-target="service-connections-modal"]');
        if (!trigger) return;

        e.preventDefault();

        // Ak dáta nie sú v argumente, skúsime BE_DATA (pre detail služby)
        const serviceId = trigger.dataset.id || window.BE_DATA?.service?.id;
        const sourceData = data || window.BE_DATA?.services || (window.BE_DATA?.service ? [window.BE_DATA.service] : []);
        
        const service = sourceData.find(s => s.id == serviceId);
        
        if (!service) {
            console.error("Service data not found for modal");
            return;
        }

        Modal.showCustom({
            title: `Connections: ${service.name}`,
            confirmText: 'Close',
            action: 'info',
            body: `
                <div class="connections-modal-wrapper">
                    <div class="dropdown-mini-list dropdown-mini-list--modal" 
                         style="max-height: 450px; overflow-y: auto;">
                        ${renderServiceConnectionsHtml(service)}
                    </div>
                </div>`,
            onConfirm: (modal) => modal.remove()
        });
    });
}

function renderServiceConnectionsHtml(service) {
    const branches = service.branches ?? [];
    const assets = service.assets ?? [];

    if (!branches.length) {
        return `
            <div class="team-member-item service-settings__sidebar-link--muted">
                <span class="member-role">No branches assigned to this service.</span>
            </div>`;
    }

    return branches.map(branch => {
        const c = getColorById(branch.id);
        const branchAssets = assets.filter(a => (a.branch_id ?? a.branch?.id) === branch.id);

        const assetsHtml = branchAssets.length
            ? branchAssets.map(asset => `
                <a href="/manage/assets/${asset.id}" class="team-member-item" style="padding-left: 10px; border-left: 1px solid #eee; background:${c.bg};">
                    <div class="member-info">
                        <span class="member-name" style="font-size: 13px; font-weight: 400;">${asset.name}</span>
                    </div>
                    <i class="fa-regular fa-gem" style="color:${c.dot}; font-size: 12px;"></i>
                </a>`).join('')
            : `<div class="team-member-item--muted" style="padding-left: 45px; font-size: 11px; color: #bbb;">No assets for this branch</div>`;

        return `
            <div class="service-settings__sidebar-group" style="margin-bottom: 20px;">
                <a href="/manage/businesses/${service.business?.id}?branch=${branch.id}"
                   class="team-member-item"
                   style="background:${c.bg}; border-left: 4px solid ${c.border}; border-radius: 4px;">
                    <div class="member-info">
                        <span class="member-name" style="color:${c.text}; font-weight: 700;">${branch.name}</span>
                        <span class="member-role" style="color:${c.text}; opacity: 0.8;">Branch detail</span>
                    </div>
                    <i class="fa-solid fa-location-dot" style="color:${c.border}"></i>
                </a>
                <div class="branch-assets-list" style="margin-top: 5px;">
                    ${assetsHtml}
                </div>
            </div>`;
    }).join('');
}

function getColorById(id) {
    const hue = (id * 137.508) % 360;
    return {
        bg:     `hsl(${hue}, 70%, 97%)`,
        border: `hsl(${hue}, 55%, 45%)`,
        text:   `hsl(${hue}, 55%, 25%)`,
        dot:    `hsl(${hue}, 60%, 55%)`
    };
}