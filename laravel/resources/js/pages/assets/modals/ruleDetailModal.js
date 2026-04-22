import { Modal } from '../../../components/displays/modal.js';

const DAY_NAMES = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

export function initRuleDetailModal() {
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('.js-open-rule-detail');
        if (!trigger) return;

        const asset = window.BE_DATA.assets.find(a => a.id == trigger.dataset.id);
        if (!asset) return;

        const activeRule = getActiveRule(asset.rules);
        if (!activeRule) return;

        Modal.showCustom({
            title: `Rules: ${activeRule.title}`,
            action: 'info',
            confirmText: 'Got it',
            body: `
                <div class="asset-rules" style="padding: 10px;">
                    <div class="rule-card__schedule-grid" style="display: flex; flex-direction: column; width: 100%;">
                        ${renderScheduleHtml(activeRule)}
                    </div>
                </div>
            `,
            onConfirm: (modal) => modal.remove()
        });
    });
}

function getActiveRule(rules) {
    if (!rules || rules.length === 0) return null;

    const now = new Date();
    const today = [
        now.getFullYear(), 
        String(now.getMonth() + 1).padStart(2, '0'), 
        String(now.getDate()).padStart(2, '0')
    ].join('-');
    
    return [...rules]
        .sort((a, b) => a.priority - b.priority)
        .find(r => {
            const from = r.valid_from ? r.valid_from.substring(0, 10) : null;
            const to = r.valid_to ? r.valid_to.substring(0, 10) : null;
            return (!from || today >= from) && (!to || today <= to);
        });
}

function renderScheduleHtml(rule) {
    let ruleSet = rule.rule_set;
    if (typeof ruleSet === 'string') ruleSet = JSON.parse(ruleSet);
    
    const days = ruleSet.days || ruleSet;

    return DAY_NAMES.map((name, i) => {
        const dayData = days[i] || [];
        const isClosed = dayData.length === 0;
        
        const timeRange = !isClosed 
            ? dayData.map(t => `${t.from_time}–${t.to_time}`).join(', ')
            : '<span class="rule-card__day-hours--closed">closed</span>';

        return `
            <div class="rule-card__schedule-item ${isClosed ? 'is-closed' : ''}" style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <span class="day-label" style="min-width: 35px; font-weight: bold; font-size: 0.85rem;">${name}</span>
                <div class="day-line" style="flex-grow: 1; border-bottom: 1px dashed var(--color-border-light); opacity: 0.5;"></div>
                <span class="day-time" style="font-size: 0.9rem;">${timeRange}</span>
            </div>
        `;
    }).join('');
}