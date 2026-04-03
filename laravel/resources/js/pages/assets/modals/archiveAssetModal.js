import { Modal } from '../../../components/displays/modal.js';

export function initArchiveAssetModal() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.js-archive-asset-btn');
        if (!btn) return;

        const assetId = btn.dataset.id;
        const assetName = btn.dataset.name;

        console.log("Archive Asset Modal - ID:", assetId, "Name:", assetName);

        Modal.showCustom({
            title: 'Archive Asset',
            confirmText: 'Archive',
            action: 'warning',
            body: `
                <div class="modal-confirm-content">
                    <p>Are you sure you want to archive <strong>${assetName}</strong>?</p>
                    <p class="text-muted small">This asset will be marked as archived and automatically deleted if not restored in time.</p>
                </div>
            `,
            onConfirm: async (modal) => {
                const url = window.BE_DATA.routes.deleteAsset.replace(':id', assetId);
                console.log("Odosielam na URL:", url);

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
                        }),
                    });

                    if (res.ok) {
                        window.location.reload();
                    } else {
                        const errorData = await res.json();
                        console.error("Server error:", errorData);
                    }
                } catch (err) {
                    console.error("Network error:", err);
                }
            }
        });
    });
}