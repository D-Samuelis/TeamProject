import { Modal } from '../../../components/displays/modal.js';

export function initDeleteRuleModal() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.js-delete-rule-btn');
        if (!btn) return;

        e.preventDefault();

        const { ruleId, ruleTitle } = btn.dataset;

        Modal.showCustom({
            title: 'Delete Rule',
            confirmText: 'Delete',
            action: 'delete',
            body: `
                <div class="modal-confirm-content">
                    <p>Are you sure you want to delete <strong>${ruleTitle}</strong>?</p>
                    <p class="text-muted small">This action cannot be undone.</p>
                </div>
            `,
            onConfirm: async (modal) => {
                const url = window.BE_DATA.routes.deleteRule.replace(':id', ruleId);

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
                    alert('Error deleting rule.');
                }
            }
        });
    });
}