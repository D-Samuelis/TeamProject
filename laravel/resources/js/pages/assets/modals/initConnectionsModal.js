import { Modal } from '../../../components/displays/modal.js';

export function initConnectionsModal() {
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('.js-open-connections');
        if (!trigger) return;

        const assetId = trigger.dataset.id;
        const asset = window.BE_DATA.assets.find(a => a.id == assetId);
        
        if (!asset) return;

        const modalBody = renderConnectionsHtml(asset);

        Modal.showCustom({
            title: `Connections: ${asset.name}`,
            confirmText: 'Got it',
            action: 'info',
            body: `
                <div class="connections-modal-wrapper">
                    <div class="dropdown__mini-list" style="display: block; max-height: 400px; overflow-y: auto; padding: 10px;">
                        ${modalBody}
                    </div>
                </div>
            `,
            onConfirm: (modal) => {
                modal.remove();
            }
        });
    });
}

function renderConnectionsHtml(asset) {
    if (!asset.branch) {
        return '<p style="color:#888; padding: 15px; text-align:center;">No branch connected.</p>';
    }

    const branch = asset.branch;
    const c = getBranchColor(branch.id);
    
    const branchServices = (asset.services || []).filter(s => 
        s.branches && s.branches.some(b => b.id === branch.id)
    );

    let servicesHtml = '';
    if (branchServices.length > 0) {
        servicesHtml = branchServices.map(service => `
            <div class="branch-item" style="display:flex; align-items:center; gap:10px; padding: 8px 15px;">
                <i class="fa-solid fa-circle" style="color:${c.dot}; font-size:6px;"></i>
                <span style="font-size: 13px; color: #555;">${service.name}</span>
            </div>
        `).join('');
    } else {
        servicesHtml = `<p style="font-size:12px; color:#bbb; padding-left:30px; margin: 10px 0;">No services assigned for this branch.</p>`;
    }

    return `
        <div class="service-item" style="background:${c.bg}; border-left:4px solid ${c.border}; border-radius:0 6px 6px 0; padding: 12px; margin-bottom: 5px;">
            <span style="color:${c.text}; font-weight:700; font-size: 14px;">
                <i class="fa-solid fa-location-dot" style="margin-right: 8px; opacity: 0.7;"></i>${branch.name}
            </span>
        </div>
        <div class="services-group" style="margin-bottom: 20px;">
            ${servicesHtml}
        </div>
    `;
}

function getBranchColor(id) {
    const hue = (id * 137.508) % 360;
    return {
        bg: `hsl(${hue}, 70%, 96%)`,
        border: `hsl(${hue}, 55%, 45%)`,
        text: `hsl(${hue}, 55%, 25%)`,
        dot: `hsl(${hue}, 60%, 55%)`
    };
}