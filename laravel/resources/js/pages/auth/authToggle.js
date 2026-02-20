export default function initAuthToggle() {
    const authCard = document.getElementById('authCard');
    const loginBtn = document.getElementById('switchToLogin');
    const registerBtn = document.getElementById('switchToRegister');
    const loginSection = document.getElementById('loginSection');
    const registerSection = document.getElementById('registerSection');

    if (!authCard || !loginBtn || !registerBtn) return;

    const toggleAuth = (mode) => {
        if (mode === 'register') {
            loginSection.classList.add('hidden');
            registerSection.classList.remove('hidden');
            
            registerBtn.classList.add('auth-card__tab--active');
            loginBtn.classList.remove('auth-card__tab--active');
            
            window.history.replaceState(null, '', '#register');
        } else {
            registerSection.classList.add('hidden');
            loginSection.classList.remove('hidden');
            
            loginBtn.classList.add('auth-card__tab--active');
            registerBtn.classList.remove('auth-card__tab--active');
            
            window.history.replaceState(null, '', '#login');
        }
    };

    loginBtn.addEventListener('click', () => toggleAuth('login'));
    registerBtn.addEventListener('click', () => toggleAuth('register'));

    const hasRegisterErrors = registerSection.querySelector('.auth-form__error') !== null;
    const isRegisterHash = window.location.hash === '#register';

    if (hasRegisterErrors || isRegisterHash) {
        toggleAuth('register');
    }
}