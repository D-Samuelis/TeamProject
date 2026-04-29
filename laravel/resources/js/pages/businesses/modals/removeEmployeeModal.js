import { Modal } from '../../../components/displays/modal.js';

export function initRemoveUserModal() {
    document.addEventListener('click', (e) => {
        // Selektor pre tlačidlo "Remove" v zozname zamestnancov
        const btn = e.target.closest('.js-remove-user-btn');
        if (!btn) return;

        e.preventDefault();

        const { userId, userName, displayName, targetType, targetId } = btn.dataset;

        Modal.showCustom({
            title: 'Remove Employee',
            confirmText: 'Remove Access',
            action: 'delete',
            body: `
                <div class="modal-confirm-content">
                    <p>Are you sure you want to unassign <strong>${userName}</strong> from <strong>${displayName}</strong>?</p>
                    <p class="text-muted small" style="margin-top: 1rem; line-height: 1.5;">
                        The user will lose access to this section immediately, but can be re-assigned later if needed.
                    </p>
                </div>
            `,
            onConfirm: async (modal) => {
                const submitBtn = modal.querySelector('[data-modal-action="confirm"]');
                
                // Dynamická URL z BE_DATA.routes.deleteUser (napr. /users/:id/unassign)
                const url = window.BE_DATA.routes.deleteUser.replace(':id', userId);

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
                            target_type: targetType, // business / branch / service
                            target_id: targetId,
                        }),
                    });

                    if (res.ok) {
                        window.location.reload();
                    } else {
                        const data = await res.json();
                        alert(data.message || 'Error removing employee.');
                        if (submitBtn) submitBtn.disabled = false;
                    }
                } catch (error) {
                    console.error('Remove user error:', error);
                    alert('A network error occurred. Please try again.');
                    if (submitBtn) submitBtn.disabled = false;
                }
            }
        });
    });
}