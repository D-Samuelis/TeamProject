export function initDatePickerToggle() {
    document.querySelectorAll('.date-picker-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const input = button.parentElement?.querySelector('input[type="date"]');
            if (!input) return;

            input.focus();
            if (typeof input.showPicker === 'function') {
                input.showPicker();
            }
        });
    });
}