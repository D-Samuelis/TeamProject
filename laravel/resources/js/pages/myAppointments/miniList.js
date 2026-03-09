import { GET_COLLAPSED_KEY } from '../../config/storageKeys.js';

/**
 * Init collapsable list (Discord style)
 * @param {string} containerId
 */
export function initCollapsibleList(containerId) {
    const list = document.getElementById(containerId);
    if (!list) return;

    const header = list.previousElementSibling;
    
    const storageKey = GET_COLLAPSED_KEY(containerId);
    
    const isCollapsed = localStorage.getItem(storageKey) === 'true';

    if (isCollapsed) {
        list.classList.add('is-hidden');
        list.style.display = 'none';
        const icon = header?.querySelector('.fa-chevron-down');
        if (icon) icon.style.transform = 'rotate(-90deg)';
    }

    if (header && header.classList.contains('appointments__subtitle')) {
        header.addEventListener('click', () => {
            const icon = header.querySelector('.fa-chevron-down');
            const hidden = list.classList.toggle('is-hidden');
            
            localStorage.setItem(storageKey, hidden);
            
            if (hidden) {
                if (icon) icon.style.transform = 'rotate(-90deg)';
                list.style.display = 'none';
            } else {
                if (icon) icon.style.transform = 'rotate(0deg)';
                list.style.display = 'flex';
            }
        });
    }
}