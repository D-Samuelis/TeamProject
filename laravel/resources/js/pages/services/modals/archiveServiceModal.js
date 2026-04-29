import { Modal } from '../../../components/displays/modal.js';
import { getFutureDateData } from '../../../utils/date.js';

export function initArchiveServiceModal() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-modal-target="archive-service-modal"]');
        if (!btn) return;

        e.preventDefault();

        const id = btn.dataset.id || window.BE_DATA.service?.id;
        const name = btn.dataset.name || window.BE_DATA.service?.name || 'this service';

        if (!id) {
            console.error("No service ID found for archiving");
            return;
        }

        Modal.showCustom({
            title: 'Archive Service',
            confirmText: 'Archive Service',
            action: 'warning',
            body: `
                <div class="modal-confirm-content">
                    <p>Are you sure you want to archive service <strong>${name}</strong>?</p>
                    
                    <div class="archive-expiry-wrapper text-muted small" style="margin-top: 1.5rem;">
                        <span>This service will be marked as archived and automatically deleted in</span>

                        <select id="archive-expiry-select-service" class="form-select-inline" style="margin: 0 4px; padding: 2px 5px; border-radius: 4px; border: 1px solid #ccc;">
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
                const expiryTimestamp = modal.querySelector('#archive-expiry-select-service').value;
                
                const url = window.BE_DATA.routes.delete.replace(':id', id);

                if (submitBtn) submitBtn.disabled = true;

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.BE_DATA.csrf
                        },
                        body: JSON.stringify({
                            _token: window.BE_DATA.csrf,
                            _method: 'DELETE',
                            delete_at_timestamp: expiryTimestamp 
                        }),
                    });

                    if (res.ok) {
                        window.location.reload();
                    } else {
                        const errorData = await res.json();
                        console.error("Server error:", errorData);
                        alert(errorData.message || 'Error archiving service.');
                        if (submitBtn) submitBtn.disabled = false;
                    }
                } catch (err) {
                    console.error("Network error:", err);
                    alert('An unexpected network error occurred.');
                    if (submitBtn) submitBtn.disabled = false;
                }
            }
        });
    });
}