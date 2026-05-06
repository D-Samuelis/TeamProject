import { Modal } from '../../../components/displays/modal.js';

export function initBranchConnectionsModal(data = null) {
    document.addEventListener('click', (e) => {
        // Používame selektor, ktorý sme dali do listView.js
        const trigger = e.target.closest('.js-open-branch-connections');
        if (!trigger) return;

        e.preventDefault();

        const branchId = trigger.dataset.id;
        // Dáta berieme buď z argumentu alebo z globálneho BE_DATA.branches
        const sourceData = data || window.BE_DATA?.branches || [];
        const branch = sourceData.find(b => b.id == branchId);
        
        if (!branch) {
            console.error("Branch data not found for modal");
            return;
        }

        Modal.showCustom({
            title: `Connections: ${branch.name}`,
            confirmText: 'Close',
            action: 'info',
            body: `
                <div class="connections-modal-wrapper">
                    <div class="dropdown-mini-list dropdown-mini-list--modal" 
                         style="max-height: 450px; overflow-y: auto; display: block;">
                        ${renderBranchConnectionsHtml(branch)}
                    </div>
                </div>`,
            onConfirm: (modal) => modal.remove()
        });
    });
}

function renderBranchConnectionsHtml(branch) {
    const services = branch.services ?? [];
    const assets = branch.assets ?? [];

    if (!services.length && !assets.length) {
        return `
            <div class="team-member-item service-settings__sidebar-link--muted">
                <span class="member-role">No resources connected to this branch.</span>
            </div>`;
    }

    const c = getColorById(branch.id);
    
    const servicesHtml = services.length
        ? services.map(service => `
            <a href="/manage/services/${service.id}" class="team-member-item" style="background:${c.bg}; margin-bottom: 4px; border-radius: 4px;">
                <div class="member-info">
                    <span class="member-name" style="font-size: 13px; font-weight: 500; color:${c.text};">${service.name}</span>
                </div>
                <i class="fa-solid fa-bell-concierge" style="color:${c.border}; font-size: 12px;"></i>
            </a>`).join('')
        : `<div class="team-member-item--muted" style="padding: 10px; font-size: 11px; color: #bbb;">No services linked</div>`;

    return `
        <div class="service-settings__sidebar-group" style="margin-bottom: 15px;">
            <div class="team-member-item" style="background:${c.bg}; border-left: 4px solid ${c.border}; border-radius: 4px; pointer-events: none;">
                <div class="member-info">
                    <span class="member-name" style="color:${c.text}; font-weight: 700;">Active Resources</span>
                    <span class="member-role" style="color:${c.text}; opacity: 0.8;">Linked to ${branch.name}</span>
                </div>
                <i class="fa-solid fa-location-dot" style="color:${c.border}"></i>
            </div>
            
            <h4 style="font-size: 11px; text-transform: uppercase; color: #999; margin: 15px 0 8px 5px; letter-spacing: 0.5px;">Services</h4>
            <div class="branch-services-list">${servicesHtml}</div>
        </div>`;
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