const ICONS = {
    success: '<polyline points="2,5 5,8 10,2"/>',
    error:   '<line x1="2" y1="2" x2="10" y2="10"/><line x1="10" y1="2" x2="2" y2="10"/>',
    warning: '<line x1="5" y1="2" x2="5" y2="6"/><circle cx="5" cy="9" r="0.6" fill="currentColor" stroke="none"/>',
    info:    '<line x1="5" y1="4" x2="5" y2="9"/><circle cx="5" cy="2" r="0.6" fill="currentColor" stroke="none"/>',
};

const CSS = `
#toast-container {
    position: fixed;
    bottom: 24px;
    right: 24px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    z-index: 9999;
    pointer-events: none;
}
.toast {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 10px;
    border: 0.5px solid;
    font-size: 14px;
    min-width: 260px;
    max-width: 340px;
    pointer-events: all;
    animation: toast-in 0.2s ease;
    position: relative;
    background: var(--color-background-primary);
}
.toast--success { border-color: var(--color-success); }
.toast--error   { border-color: var(--color-danger); }
.toast--warning { border-color: var(--color-warning); }
.toast--info    { border-color: var(--status-blue); }

.toast__icon {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 1px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.toast--success .toast__icon { background: var(--color-success); }
.toast--error   .toast__icon { background: var(--color-danger); }
.toast--warning .toast__icon { background: var(--color-warning); }
.toast--info    .toast__icon { background: var(--status-blue); }

.toast__icon svg {
    width: 9px;
    height: 9px;
    stroke: #111;
    fill: none;
    stroke-width: 2.5;
    stroke-linecap: round;
    stroke-linejoin: round;
}
.toast__body    { flex: 1; }
.toast__title   { font-weight: 500; font-size: 13px; color: var(--color-text); line-height: 1.3; }
.toast__msg     { font-size: 12px; color: var(--color-text-unimportant); margin-top: 2px; line-height: 1.4; }
.toast__close {
    font-size: 16px;
    line-height: 1;
    cursor: pointer;
    color: var(--color-text-unimportant);
    background: none;
    border: none;
    padding: 0;
    margin-left: 4px;
    opacity: 0.6;
}
.toast__close:hover { opacity: 1; }
.toast__progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 2px;
    border-radius: 0 0 10px 10px;
    animation: toast-progress 3.5s linear forwards;
}
.toast--success .toast__progress { background: var(--color-success); }
.toast--error   .toast__progress { background: var(--color-danger); }
.toast--warning .toast__progress { background: var(--color-warning); }
.toast--info    .toast__progress { background: var(--status-blue); }

.toast.removing { animation: toast-out 0.18s ease forwards; }

@keyframes toast-in  { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
@keyframes toast-out { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(20px); } }
@keyframes toast-progress { from { width: 100%; } to { width: 0%; } }
`;

function inject() {
    if (document.getElementById('toast-container')) return;

    const style = document.createElement('style');
    style.textContent = CSS;
    document.head.appendChild(style);

    const container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
}

function remove(el) {
    if (!el || el.classList.contains('removing')) return;
    el.classList.add('removing');
    setTimeout(() => el.remove(), 180);
}

function show(type, title, msg = '', duration = 3500) {
    inject();

    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast toast--${type}`;
    toast.innerHTML = `
        <div class="toast__icon">
            <svg viewBox="0 0 12 12">${ICONS[type]}</svg>
        </div>
        <div class="toast__body">
            <div class="toast__title">${title}</div>
            ${msg ? `<div class="toast__msg">${msg}</div>` : ''}
        </div>
        <button class="toast__close" type="button">×</button>
        <div class="toast__progress"></div>
    `;

    toast.querySelector('.toast__close').addEventListener('click', () => remove(toast));
    container.appendChild(toast);
    setTimeout(() => remove(toast), duration);
}

export const Toast = {
    success: (title, msg) => show('success', title, msg),
    error:   (title, msg) => show('error',   title, msg),
    warning: (title, msg) => show('warning', title, msg),
    info:    (title, msg) => show('info',    title, msg),
};