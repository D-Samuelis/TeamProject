document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('aiBookingInput');
    if (!input) return;

    setRandomPlaceholder(input, APPOINTMENT_SAMPLES);

    input.addEventListener('keydown', e => {
        if (e.key !== 'Enter') return;
        e.preventDefault();

        const text = input.value.trim();
        if (!text) return;

        input.value = '';

        window.bexiOpenAndSend?.(text);
    });
});
