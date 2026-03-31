import { Modal } from '../../../components/displays/modal.js';

export function initArchiveBusinessModal() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.js-archive-business-btn');
        if (!btn) return;

        const { id, name } = btn.dataset;

        Modal.showCustom({
            title: 'Archive Business',
            confirmText: 'Archive',
            action: 'warning',
            body: `
                <div class="modal-confirm-content">
                    <p>Are you sure you want to archive <strong>${name}</strong>?</p>
                    <p class="text-muted small">This business will be marked as archived and automatically deleted in "FETCH TIME" if not restored in time.</p>
                </div>
            `,
            onConfirm: async (modal) => {
                const url = window.BE_DATA.routes.delete.replace(':id', id);

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
                    alert('Error archiving business.');
                }
            }
        });
    });
}