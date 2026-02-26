// authToggle.js
export default function initAuthToggle() {
    const loginBtn = document.getElementById('switchToLogin');
    const registerBtn = document.getElementById('switchToRegister');
    const loginSection = document.getElementById('loginSection');
    const registerSection = document.getElementById('registerSection');

    // DEBUG: Skontroluj v konzole, či JS vidí tieto prvky
    console.log({ loginBtn, registerBtn, loginSection, registerSection });

    if (!loginSection || !registerSection) return;

    const showSection = (mode) => {
        if (mode === 'register') {
            loginSection.classList.add('hidden');
            registerSection.classList.remove('hidden');
            window.history.replaceState(null, '', '#register');
        } else {
            registerSection.classList.add('hidden');
            loginSection.classList.remove('hidden');
            window.history.replaceState(null, '', '#login');
        }
    };

    // Používame voliteľné reťazenie (?.) aby JS nezlyhal, ak tlačidlo nenájde
    registerBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        showSection('register');
    });

    loginBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        showSection('login');
    });

    // Inicializácia pri načítaní (podľa hashu v URL)
    if (window.location.hash === '#register') {
        showSection('register');
    }
}