import { setRandomPlaceholder } from '../../components/helpers/textGenerator.js';
import { APPOINTMENT_SAMPLES } from '../../config/constants.js';

document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('aiBookingInput');
    if (input) {
        setRandomPlaceholder(input, APPOINTMENT_SAMPLES);
    }
});