
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

export function initCategorySelect() {
    const select = document.querySelector('[data-custom-select]');
    const input = document.getElementById('category_id');

    if (!select || !input) return;

    const button = select.querySelector('.custom-select__button');
    const label = select.querySelector('[data-custom-select-label]');
    const menu = select.querySelector('.custom-select__menu');
    const options = [...select.querySelectorAll('.custom-select__option')];

    if (!button || !label || !menu || options.length === 0) return;

    const closeMenu = () => {
        menu.hidden = true;
        button.setAttribute('aria-expanded', 'false');
    };

    const openMenu = () => {
        menu.hidden = false;
        button.setAttribute('aria-expanded', 'true');
    };

    const setSelectedOption = (option) => {
        input.value = option.dataset.value || '';
        label.textContent = option.textContent.trim();

        options.forEach((item) => {
            const isSelected = item === option;
            item.classList.toggle('is-selected', isSelected);
            item.setAttribute('aria-selected', isSelected ? 'true' : 'false');
        });

        closeMenu();
        button.focus();
    };

    button.addEventListener('click', () => {
        if (menu.hidden) {
            openMenu();
        } else {
            closeMenu();
        }
    });

    options.forEach((option) => {
        option.addEventListener('click', () => setSelectedOption(option));
    });

    document.addEventListener('click', (event) => {
        if (!select.contains(event.target)) {
            closeMenu();
        }
    });

    select.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
            button.focus();
        }
    });
}
