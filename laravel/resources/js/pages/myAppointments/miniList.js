/**
 * Init collapsable list (Discord style)
 * @param {string} containerId
 */
export function initCollapsibleList(containerId) {
    const list = document.getElementById(containerId);
    if (!list) return;

    const header = list.previousElementSibling;
    
    if (header && header.classList.contains('appointments__subtitle')) {
        header.style.cursor = 'pointer';
        header.style.userSelect = 'none';

        header.addEventListener('click', () => {
            const icon = header.querySelector('.fa-chevron-down');
            
            list.classList.toggle('is-hidden');
            
            if (list.classList.contains('is-hidden')) {
                icon.style.transform = 'rotate(-90deg)';
                list.style.display = 'none';
            } else {
                icon.style.transform = 'rotate(0deg)';
                list.style.display = 'block';
            }
        });
    }
}