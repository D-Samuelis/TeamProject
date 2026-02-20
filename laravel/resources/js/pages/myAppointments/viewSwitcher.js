export function initViewSwitcher() {
    const controls = {
        calendar: {
            btn: document.getElementById('showCalendar'),
            view: document.getElementById('calendarView')
        },
        list: {
            btn: document.getElementById('showList'),
            view: document.getElementById('listView')
        }
    };

    // Ak chýbajú kľúčové prvky, nepokračuj
    if (!controls.calendar.btn || !controls.list.btn) return;

    function switchView(targetKey) {
        Object.keys(controls).forEach(key => {
            const isTarget = key === targetKey;
            
            controls[key].view?.classList.toggle('hidden', !isTarget);
            controls[key].btn?.classList.toggle('active', isTarget);
        });
    }

    Object.keys(controls).forEach(key => {
        controls[key].btn.addEventListener('click', () => switchView(key));
    });
}