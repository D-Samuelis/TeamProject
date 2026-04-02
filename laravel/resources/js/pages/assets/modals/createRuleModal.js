import { Modal } from '../../../components/displays/modal.js';
import { RuleScheduleHelper } from './helpers/ruleScheduleHelper.js';

export function initCreateRuleModal() {
    const btn = document.querySelector('[data-modal-target="create-rule-modal"]');
    if (!btn) return;

    btn.onclick = () => {
        Modal.showCustom({
            title: 'New Rule',
            confirmText: 'Create Rule',
            action: 'create',
            body: `
                <form id="createRuleForm">
                    <div class="modal-form__group">
                        <label class="modal-form__label">Title</label>
                        <input type="text" name="title" class="modal-form__input" required>
                    </div>
                    <div class="modal-form__group">
                        <label class="modal-form__label">Description (Optional)</label>
                        <textarea name="description" class="modal-form__input"></textarea>
                    </div>
                    <div class="modal-form__group">
                        <label class="modal-form__label">Valid From</label>
                        <input type="date" name="valid_from" class="modal-form__input">
                    </div>
                    <div class="modal-form__group">
                        <label class="modal-form__label">Valid To</label>
                        <input type="date" name="valid_to" class="modal-form__input">
                    </div>
                    <div class="modal-form__group">
                        <div class="schedule-builder-section">
                            <label class="modal-form__label">Schedule Configuration</label>
                            ${RuleScheduleHelper.getTemplate('create')}
                        </div>
                    </div>    
                </form>
            `,
            onConfirm: async (modal) => {
                const form = modal.querySelector('#createRuleForm');
                const formData = new FormData(form);
                formData.append('asset_id', window.BE_DATA.asset.id);
                formData.append('rule_set', RuleScheduleHelper.serialize(modal));
                formData.append('_token', window.BE_DATA.csrf);

                const res = await fetch('/manage/rules', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: formData
                });

                if (res.ok) window.location.reload();
                else if (res.status === 422) Modal.showFieldErrors(modal, (await res.json()).errors);
            }
        });

        RuleScheduleHelper.initEvents(document.getElementById('dynamic-modal'));
    };
}