import { Modal } from '../../../components/displays/modal.js';

export function initServiceConnectionsModal(data) {
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('.js-open-service-connections');
        if (!trigger) return;

        const serviceId = trigger.dataset.id;
        const service   = data.find(s => s.id == serviceId);
        if (!service) return;

        Modal.showCustom({
            title: `Connections: ${service.name}`,
            confirmText: 'Got it',
            action: 'info',
            body: `
                <div class="connections-modal-wrapper">
                    <div class="dropdown__mini-list"
                         style="display:block; max-height:400px; overflow-y:auto; padding:10px;">
                        ${renderServiceConnectionsHtml(service)}
                    </div>
                </div>`,
            onConfirm: (modal) => modal.remove()
        });
    });
}

function renderServiceConnectionsHtml(service) {
    const branches = service.branches ?? [];
    const assets   = service.assets   ?? [];

    if (!branches.length) {
        return `<p style="font-size:12px; color:#bbb; padding:10px 15px; margin:0;">No branches assigned.</p>`;
    }

    return branches.map(branch => {
        const c = getColorById(branch.id);

        const branchAssets = assets.filter(a => (a.branch_id ?? a.branch?.id) === branch.id);

        const assetsHtml = branchAssets.length
            ? branchAssets.map(asset => `
                <a href="/manage/assets/${asset.id}"
                class="branch-item"
                style="display:flex; align-items:center; gap:10px; padding:8px 15px; text-decoration:none;">
                    <i class="fa-solid fa-box" style="color:${c.dot}; font-size:12px;"></i>
                    <span style="font-size:13px; color:#555;">${asset.name}</span>
                </a>`).join('')
            : `<p style="font-size:12px; color:#bbb; padding-left:30px; margin:6px 0;">No assets for this branch.</p>`;

        return `
            <div style="margin-bottom:16px;">
                <a href="/manage/businesses/${service.business?.id}?branch=${branch.id}"
                style="background:${c.bg}; border-left:4px solid ${c.border};
                        border-radius:0 6px 6px 0; padding:12px; margin-bottom:4px;
                        display:block; text-decoration:none;">
                    <span style="color:${c.text}; font-weight:700; font-size:14px;">
                        <i class="fa-solid fa-location-dot" style="margin-right:8px; opacity:0.7;"></i>
                        ${branch.name}
                    </span>
                </a>
                ${assetsHtml}
            </div>`;
    }).join('');
}

function getColorById(id) {
    const hue = (id * 137.508) % 360;
    return {
        bg:     `hsl(${hue}, 70%, 96%)`,
        border: `hsl(${hue}, 55%, 45%)`,
        text:   `hsl(${hue}, 55%, 25%)`,
        dot:    `hsl(${hue}, 60%, 55%)`
    };
}