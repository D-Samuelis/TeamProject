export const DAY_NAMES = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

export const RuleScheduleHelper = {
    getTemplate: (prefix) => {
        return DAY_NAMES.map((name, i) => `
            <div class="schedule-day js-day-row" data-day="${i}">
                <div class="schedule-day-header">
                    <input type="checkbox" class="js-day-toggle" id="${prefix}_day_${i}" data-day="${i}">
                    <label for="${prefix}_day_${i}">${name}</label>
                    <button type="button" class="btn-add-range js-add-range" style="display:none">+ add range</button>
                </div>
                <div class="ranges-list js-ranges-container"></div>
            </div>
        `).join('');
    },

    initEvents: (modalElement) => {
        modalElement.querySelectorAll('.js-day-row').forEach(row => {
            const toggle = row.querySelector('.js-day-toggle');
            const addBtn = row.querySelector('.js-add-range');
            const container = row.querySelector('.js-ranges-container');

            toggle.addEventListener('change', () => {
                if (toggle.checked) {
                    addBtn.style.display = 'block';
                    if (container.children.length === 0) RuleScheduleHelper.addRangeRow(container);
                } else {
                    addBtn.style.display = 'none';
                    container.innerHTML = '';
                }
            });

            addBtn.addEventListener('click', () => RuleScheduleHelper.addRangeRow(container));
        });
    },

    addRangeRow: (container, from = '08:00', to = '17:00') => {
        const row = document.createElement('div');
        row.className = 'range-row';
        row.innerHTML = `
            <input type="time" class="js-time-from" value="${from}">
            <span>–</span>
            <input type="time" class="js-time-to" value="${to}">
            <button type="button" class="btn-del-range">✕</button>
        `;
        row.querySelector('.btn-del-range').onclick = () => row.remove();
        container.appendChild(row);
    },

    serialize: (modalElement) => {
        const result = { days: {} };
        modalElement.querySelectorAll('.js-day-row').forEach(row => {
            const dayIdx = row.dataset.day;
            const isChecked = row.querySelector('.js-day-toggle').checked;
            
            if (isChecked) {
                const ranges = Array.from(row.querySelectorAll('.range-row')).map(r => ({
                    from_time: r.querySelector('.js-time-from').value,
                    to_time: r.querySelector('.js-time-to').value
                })).filter(r => r.from_time && r.to_time);
                result.days[dayIdx] = ranges;
            } else {
                result.days[dayIdx] = [];
            }
        });
        return JSON.stringify(result);
    },

    hydrate: (modalElement, ruleSet) => {
        if (!ruleSet) return;
        
        // Ošetrenie ak je ruleSet už objekt alebo ešte string
        let data;
        try {
            data = typeof ruleSet === 'string' ? JSON.parse(ruleSet) : ruleSet;
        } catch (e) {
            console.error("Failed to parse rule_set JSON", e);
            return;
        }

        const days = data.days || data;

        Object.keys(days).forEach(dayIdx => {
            // Skontrolujeme, či pre tento deň máme nejaké dáta
            if (days[dayIdx] && days[dayIdx].length > 0) {
                // Hľadáme riadok dňa v rámci modalElementu
                const row = modalElement.querySelector(`.js-day-row[data-day="${dayIdx}"]`);
                
                if (!row) {
                    console.warn(`Could not find row for day index: ${dayIdx}`);
                    return; // Preskočíme tento deň ak v HTML chýba
                }

                const toggle = row.querySelector('.js-day-toggle');
                const container = row.querySelector('.js-ranges-container');
                const addBtn = row.querySelector('.js-add-range');
                
                if (toggle && container) {
                    toggle.checked = true;
                    if (addBtn) addBtn.style.display = 'block';
                    
                    container.innerHTML = ''; // Vyčistíme defaultný prázdny riadok ak tam je
                    
                    days[dayIdx].forEach(range => {
                        RuleScheduleHelper.addRangeRow(container, range.from_time, range.to_time);
                    });
                }
            }
        });
    }
};