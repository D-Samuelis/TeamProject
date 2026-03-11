export function initModals() {
    const modalTriggers = document.querySelectorAll('[data-modal-target]');

    modalTriggers.forEach(trigger => {
        const modalId = trigger.getAttribute('data-modal-target');
        const modal = document.getElementById(modalId);
        
        if (!modal) return;

        const closeTriggers = modal.querySelectorAll('.modal-close-trigger, .modal-overlay');

        const showModal = () => modal.classList.remove('hidden');
        const hideModal = () => modal.classList.add('hidden');

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            showModal();
        });

        closeTriggers.forEach(close => {
            close.addEventListener('click', (e) => {
                e.preventDefault();
                hideModal();
            });
        });
    });
}