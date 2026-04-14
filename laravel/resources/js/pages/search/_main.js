
import { initListSearch } from '../../components/table/searchBar.js';

export function initBookingSearch() {
    const searchInput = document.querySelector('#bookingSearch');
    const bookingGrid = document.querySelector('.booking-grid');

    if (!searchInput || !bookingGrid) return;

    initListSearch(
        '#bookingSearch',
        '.booking-grid .card-link',
        '.js-search-data'
    );
}

export function initRangeSliders() {
    const maxPriceInput = document.getElementById('max_price');
    const maxPriceValue = document.getElementById('max_price_value');

    if (maxPriceInput && maxPriceValue) {
        maxPriceInput.addEventListener('input', () => {
            maxPriceValue.textContent = `${maxPriceInput.value} €`;
        });
    }

    const maxDurationInput = document.getElementById('max_duration');
    const maxDurationValue = document.getElementById('max_duration_value');

    if (maxDurationInput && maxDurationValue) {
        maxDurationInput.addEventListener('input', () => {
            maxDurationValue.textContent = `${maxDurationInput.value} min`;
        });
    }
}