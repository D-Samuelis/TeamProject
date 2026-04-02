import { Modal } from '../../../components/displays/modal.js';
import { RuleScheduleHelper } from './helpers/ruleScheduleHelper.js';

export function initEditRuleModal() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.js-edit-rule-btn');
        if (!btn) return;

        const rule = JSON.parse(btn.dataset.rule);

        Modal.showCustom({
            title: 'Edit Rule',
            confirmText: 'Save Changes',
            action: 'edit',
            body: `
                <form id="editRuleForm">
                    <div class="modal-form__group">
                        <label class="modal-form__label">Title</label>
                        <input type="text" name="title" class="modal-form__input" value="${rule.title}" required>
                    </div>
                    <div class="modal-form__group">
                        <label class="modal-form__label">Description</label>
                        <textarea name="description" class="modal-form__input">${rule.description || ''}</textarea>
                    </div>
                    <div class="modal-form__group">
                        <label class="modal-form__label">Valid From</label>
                        <input type="date" name="valid_from" class="modal-form__input" 
                            value="${rule.valid_from ? rule.valid_from.split('T')[0] : ''}">
                    </div>
                    <div class="modal-form__group">
                        <label class="modal-form__label">Valid To</label>
                        <input type="date" name="valid_to" class="modal-form__input" 
                            value="${rule.valid_to ? rule.valid_to.split('T')[0] : ''}">
                    </div>
                    <div class="modal-form__group">
                        <div class="schedule-builder-section">
                            ${RuleScheduleHelper.getTemplate('edit')}
                        </div>
                    </div>
                </form>
            `,
            onConfirm: async (modal) => {
                const formData = new FormData(modal.querySelector('#editRuleForm'));
                formData.append('rule_set', RuleScheduleHelper.serialize(modal));
                formData.append('_token', window.BE_DATA.csrf);
                formData.append('_method', 'PUT');

                const res = await fetch(`/manage/rules/${rule.id}`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: formData
                });

                if (res.ok) window.location.reload();
                else if (res.status === 422) Modal.showFieldErrors(modal, (await res.json()).errors);
            }
        });

        const modal = document.getElementById('dynamic-modal');
        RuleScheduleHelper.initEvents(modal);
        RuleScheduleHelper.hydrate(modal, rule.rule_set);
    });
}