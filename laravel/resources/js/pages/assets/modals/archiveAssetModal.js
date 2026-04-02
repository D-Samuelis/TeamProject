import { Modal } from '../../../components/displays/modal.js';

export function initArchiveAssetModal() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.js-archive-asset-btn');
        if (!btn) return;

        const { id, name } = btn.dataset;

        Modal.showCustom({
            title: 'Archive Asset',
            confirmText: 'Archive',
            action: 'warning',
            body: `
                <div class="modal-confirm-content">
                    <p>Are you sure you want to archive <strong>${name}</strong>?</p>
                    <p class="text-muted small">This asset will be marked as archived and automatically deleted if not restored in time.</p>
                </div>
            `,
            onConfirm: async (modal) => {
                const url = window.BE_DATA.routes.deleteAsset.replace(':id', id);

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
                    }),
                });

                if (res.ok) {
                    window.location.reload();
                } else {
                    alert('Error archiving asset.');
                }
            }
        });
    });
}