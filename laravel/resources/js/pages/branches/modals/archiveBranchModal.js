import { Modal } from '../../../components/displays/modal.js';
import { getFutureDateData } from '../../../utils/date.js';

export function initArchiveBranchModal() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.js-archive-branch-btn') || e.target.closest('[data-modal-target="archive-branch-modal"]');
        if (!btn) return;

        const branchId = btn.dataset.id || window.BE_DATA?.branch?.id;
        const branchName = btn.dataset.name || window.BE_DATA?.branch?.name || 'this branch';

        Modal.showCustom({
            title: 'Archive Branch',
            confirmText: 'Archive',
            action: 'warning',
            body: `
                <div class="modal-confirm-content">
                    <p>Are you sure you want to archive <strong>${branchName}</strong>?</p>
                    <div style="margin-top: 1rem;" class="text-muted small">
                        Pobočka bude označená ako archivovaná. Vyberte dobu, po ktorej sa definitívne zmaže:
                        <select id="archive-expiry-select" class="form-select-inline" style="margin-left: 5px;">
                            <option value="${getFutureDateData(7).timestamp}">1 týždeň</option>
                            <option value="${getFutureDateData(30).timestamp}">1 mesiac</option>
                        </select>
                    </div>
                </div>
            `,
            onConfirm: async (modal) => {
                const url = window.BE_DATA.routes.delete.replace(':id', branchId);
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.BE_DATA.csrf,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                });

                if (res.ok) window.location.reload();
            }
        });
    });
}