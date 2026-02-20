import { DARK_MODE_STORAGE_KEY } from '../../config/storageKeys.js';

export default function initTheme() {
    const body = document.body;
    // POZOR: Musíš použiť tvoju novú BEM triedu!
    const buttons = document.querySelectorAll('.theme-toggle__btn');
    const slider = document.getElementById('themeSlider');

    // Poistka: Ak na stránke nie je slider, nepokračuj (zabráni chybám v konzole)
    if (!slider) return;

    function applyTheme(theme) {
        if (theme === 'light') {
            body.classList.remove('theme-dark');
            localStorage.setItem(DARK_MODE_STORAGE_KEY, 'disabled');
        } else if (theme === 'dark') {
            body.classList.add('theme-dark');
            localStorage.setItem(DARK_MODE_STORAGE_KEY, 'enabled');
        } else if (theme === 'system') {
            localStorage.removeItem(DARK_MODE_STORAGE_KEY);
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            body.classList.toggle('theme-dark', prefersDark);
        }
    }

    function moveSliderTo(theme) {
        const index = { light: 0, dark: 1, system: 2 }[theme] ?? 2;
        // Ak máš padding .1rem na slideri, možno budeš musieť doladiť percentá
        slider.style.transform = `translateX(${index * 100}%)`;
    }

    function loadInitialTheme() {
        const stored = localStorage.getItem(DARK_MODE_STORAGE_KEY);
        let theme = 'system';

        if (stored === 'enabled') theme = 'dark';
        else if (stored === 'disabled') theme = 'light';

        applyTheme(theme);
        moveSliderTo(theme);
    }

    // Inicializácia
    loadInitialTheme();

    // Event Listenery
    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const theme = button.dataset.theme;
            applyTheme(theme);
            moveSliderTo(theme);
        });
    });
}