import { APP_VIEW_PREFERENCE_KEY } from '../../config/storageKeys.js';

export function initViewToggle() {
    const timelineContainer = document.getElementById('timelineView');
    const listView = document.getElementById('listView');
    
    // 1. Načítame uložený stav
    const savedView = localStorage.getItem(APP_VIEW_PREFERENCE_KEY) || 'timeline';

    // 2. Funkcia na prepínanie
    function switchView(targetKey) {
        const isTimeline = targetKey === 'timeline';
        
        // Prepneme viditeľnosť kontajnerov
        timelineView?.classList.toggle('hidden', !isTimeline);
        listView?.classList.toggle('hidden', isTimeline);

        // Aktualizujeme triedu .active na tlačidlách (hľadáme ich v DOMe až teraz)
        document.querySelectorAll('#showTimeline').forEach(b => b.classList.toggle('active', isTimeline));
        document.querySelectorAll('#showList').forEach(b => b.classList.toggle('active', !isTimeline));

        localStorage.setItem(APP_VIEW_PREFERENCE_KEY, targetKey);

        if (isTimeline) {
            window.dispatchEvent(new Event('resize'));
        }
    }

    // 3. Počiatočné nastavenie
    switchView(savedView);

    // 4. EVENT DELEGATION: Počúvame na kliknutia na celom dokumente
    // Takto nám je jedno, kedy a kde sa tie tlačidlá v JS vygenerujú
    document.addEventListener('click', (e) => {
        const btnTimeline = e.target.closest('#showTimeline');
        const btnList = e.target.closest('#showList');

        if (btnTimeline) {
            e.preventDefault();
            switchView('timeline');
        }
        
        if (btnList) {
            e.preventDefault();
            switchView('list');
        }
    });
}