import { initTimelineLayout } from './timeLineLayout.js';
import { initAppointmentListView } from './listView.js';
import { APP_VIEW_PREFERENCE_KEY } from '../../config/storageKeys.js';

/**
 * Convert time
 * @param {string} isoStr
 * @returns {Date}
 */
function parseISO(isoStr) {
    if (!isoStr) return new Date();
    const [year, month, day] = isoStr.split('-').map(Number);
    return new Date(year, month - 1, day, 12, 0, 0);
}

/**
 * Main funciton to refresh middle pannel
 * @param {string|null} forcedDateStr - Optional iso from string
 */
export function refreshActiveView(forcedDateStr = null) {
    const dateEl = document.getElementById('selectedDateText');

    const currentIso = forcedDateStr || dateEl?.dataset.isoDate || new Date().toLocaleDateString('en-CA');

    const selectedDate = parseISO(currentIso);

    const allAppointments = window.BE_DATA?.appointments || [];
    const meta            = window.BE_DATA?.meta || {};
    const savedView = localStorage.getItem(APP_VIEW_PREFERENCE_KEY) || 'timeline';

    const filtered = allAppointments;

    if (savedView === 'timeline') {
        initTimelineLayout(filtered, selectedDate, 3);
        window.dispatchEvent(new Event('resize'));
    } else {
        initAppointmentListView(filtered, meta);
    }
}

export function initAppointmentWatchers() {
    window.addEventListener('dateChanged', (e) => {
        refreshActiveView(e.detail);
    });

    window.addEventListener('viewChanged', () => {
        refreshActiveView();
    });

    window.addEventListener('filtersChanged', () => {
        refreshActiveView();
    });

    refreshActiveView();
}
