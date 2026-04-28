import { Modal } from '../../../components/displays/modal.js';
import { getFutureDateData } from '../../../utils/date.js';

export function initArchiveAssetModal() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-modal-target="archive-asset-modal"]');
        if (!btn) return;

        e.preventDefault();

        const assetId = btn.dataset.id || window.BE_DATA?.asset?.id;
        const assetName = btn.dataset.name || window.BE_DATA?.asset?.name || 'this asset';

        if (!assetId) {
            console.error("No asset ID found for archiving");
            return;
        }

        openArchiveAssetModal(assetId, assetName);
    });
}

function openArchiveAssetModal(assetId, assetName) {
    Modal.showCustom({
        title: 'Archive Asset',
        confirmText: 'Archive',
        action: 'warning',
        body: `
            <div class="modal-confirm-content">
                <p>Are you sure you want to archive <strong>${assetName}</strong>?</p>
                
                <div class="archive-expiry-wrapper text-muted small" style="margin-top: 1rem;">
                    <span>This asset will be marked as archived and automatically deleted in</span>

                    <select id="archive-expiry-select" class="form-select-inline" style="margin: 0 0.5rem; padding: 2px 5px; border-radius: 4px; border: 1px solid #ccc;">
                        <option value="${getFutureDateData(1).timestamp}">1 Day [${getFutureDateData(1).display}]</option>
                        <option value="${getFutureDateData(7).timestamp}" selected>1 Week [${getFutureDateData(7).display}]</option>
                        <option value="${getFutureDateData(30).timestamp}">1 Month [${getFutureDateData(30).display}]</option>
                    </select> 

                    <span>if not restored in time.</span>
                </div>
            </div>
        `,
        onConfirm: async (modal) => {
            const url = window.BE_DATA.routes.deleteAsset.replace(':id', assetId);
            const expiryTimestamp = modal.querySelector('#archive-expiry-select').value;

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
                        /*delete_at: expiryTimestamp*/
                    }),
                });

                if (res.ok) {
                    window.location.reload();
                } else {
                    const errorData = await res.json();
                    console.error("Server error:", errorData);
                    Modal.showFieldErrors(modal, errorData.errors);
                }
            } catch (err) {
                console.error("Network error:", err);
            }
        }
    });
}