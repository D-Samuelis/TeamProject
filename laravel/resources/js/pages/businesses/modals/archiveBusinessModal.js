import { Modal } from '../../../components/displays/modal.js';
import { getFutureDateData } from '../../../utils/date.js';

export function initArchiveBusinessModal() {
    document.addEventListener('click', (e) => {
        // Selektujeme podľa data-modal-target, aby to sedelo na Toolbar
        const btn = e.target.closest('[data-modal-target="archive-business-modal"]');
        if (!btn) return;

        const { id, name } = btn.dataset;

        Modal.showCustom({
            title: 'Archive Business',
            confirmText: 'Archive Business',
            action: 'warning',
            body: `
                <div class="modal-confirm-content">
                    <p>Are you sure you want to archive <strong>${name}</strong>?</p>
                    
                    <div class="archive-expiry-wrapper text-muted small" style="margin-top: 1.5rem; line-height: 1.6;">
                        <span>This business will be marked as archived and automatically deleted in</span>

                        <select id="archive-expiry-select-business" class="form-select-inline" style="margin: 0 4px;">
                            <option value="${getFutureDateData(1).timestamp}">1 Day [${getFutureDateData(1).display}]</option>
                            <option value="${getFutureDateData(7).timestamp}" selected>1 Week [${getFutureDateData(7).display}]</option>
                            <option value="${getFutureDateData(30).timestamp}">1 Month [${getFutureDateData(30).display}]</option>
                        </select> 

                        <span>if not restored in time.</span>
                    </div>
                </div>
            `,
            onConfirm: async (modal) => {
                const submitBtn = modal.querySelector('[data-modal-action="confirm"]');
                const expiryTimestamp = modal.querySelector('#archive-expiry-select-business').value;
                
                // Dynamická cesta z routes definovaných v BE_DATA
                const url = window.BE_DATA.routes.delete.replace(':id', id);

                if (submitBtn) submitBtn.disabled = true;

                try {
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
                            delete_at: expiryTimestamp // Pripravené pre tvoj backend
                        }),
                    });

                    if (res.ok) {
                        window.location.reload();
                    } else {
                        const data = await res.json();
                        alert(data.message || 'Error archiving business.');
                    }
                } catch (error) {
                    console.error('Archive error:', error);
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                }
            }
        });
    });
}