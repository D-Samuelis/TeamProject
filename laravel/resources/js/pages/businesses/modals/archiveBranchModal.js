import { Modal } from '../../../components/displays/modal.js';
import { getFutureDateData } from '../../../utils/date.js';

export function initArchiveBranchModal() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.js-archive-branch-btn');
        if (!btn) return;

        const { id, name } = btn.dataset;

        Modal.showCustom({
            title: 'Archive Branch',
            confirmText: 'Archive',
            action: 'warning',
            body: `
                <div class="modal-confirm-content">
                    <p>Are you sure you want to archive <strong>${name}</strong>?</p>
                    
                    <div class="archive-expiry-wrapper text-muted small">
                        <span>This branch will be marked as archived and automatically deleted in</span>

                        <select id="archive-expiry-select-branch" class="form-select-inline">
                            <option value="${getFutureDateData(1).timestamp}">1 Day [${getFutureDateData(1).display}]</option>
                            <option value="${getFutureDateData(7).timestamp}" selected>1 Week [${getFutureDateData(7).display}]</option>
                            <option value="${getFutureDateData(30).timestamp}">1 Month [${getFutureDateData(30).display}]</option>
                        </select> 

                        <span>if not restored in time.</span>
                    </div>
                </div>
            `,
            onConfirm: async (modal) => {
                const url = window.BE_DATA.routes.branchDelete.replace(':id', id);

                const expiryTimestamp = modal.querySelector('#archive-expiry-select-branch').value;

                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        _token: window.BE_DATA.csrf,
                        _method: 'DELETE',
                        /*delete_at_timestamp: expiryTimestamp*/
                    }),
                });

                if (res.ok) {
                    window.location.reload();
                } else {
                    alert('Error archiving branch.');
                }
            }
        });
    });
}