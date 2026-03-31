import { Modal } from '../../../components/displays/modal.js';

export function initRemoveUserModal() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.js-remove-user-btn');
        if (!btn) return;

        e.preventDefault();

        const { userId, userName, displayName, businessId, targetType, targetId } = btn.dataset;

        Modal.showCustom({
            title: 'Remove Employee',
            confirmText: 'Remove',
            action: 'delete',
            body: `
                <div class="modal-confirm-content">
                    <p>Are you sure you want to unassign <strong>${userName}</strong> from <strong>${displayName}</strong>?</p>
                    <p class="text-muted small">The user will lose access to this section immediately, but can be re-assigned later if needed.</p>
                </div>
            `,
            onConfirm: async (modal) => {
                const url = window.BE_DATA.routes.deleteUser
                    .replace(':id', userId);

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
                        target_type: targetType,
                        target_id: targetId,
                    }),
                });

                if (res.ok) {
                    window.location.reload();
                } else {
                    alert('Error removing employee.');
                }
            }
        });
    });
}