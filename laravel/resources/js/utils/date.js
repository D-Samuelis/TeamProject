/**
 * Date utility functions for calendar and appointment management
 */

/**
 * Get the number of days in a specific month
 * @param {number} year
 * @param {number} month - 0-indexed (0 = January)
 * @returns {number}
 */
export function daysInMonth(year, month) {
    return new Date(year, month + 1, 0).getDate();
}

/**
 * Format a date to DD.MM.YYYY format
 * @param {Date} date
 * @returns {string}
 */
export function formatDate(date) {
    const d = String(date.getDate()).padStart(2, '0');
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const y = date.getFullYear();
    return `${d}.${m}.${y}`;
}

/**
 * Get the day of week (0-6) for the first day of a month
 * @param {number} year
 * @param {number} month - 0-indexed
 * @returns {number}
 */
export function getFirstDayOfMonth(year, month) {
    return new Date(year, month, 1).getDay();
}

/**
 * Check if a date matches the DD.MM.YYYY format
 * @param {string} dateStr
 * @returns {boolean}
 */
export function isValidDateFormat(dateStr) {
    return /^\d{2}\.\d{2}\.\d{4}$/.test(dateStr);
}

/**
 * Parse a DD.MM.YYYY string to a Date object
 * @param {string} dateStr - Format: DD.MM.YYYY
 * @returns {Date|null}
 */
export function parseDate(dateStr) {
    if (!isValidDateFormat(dateStr)) return null;
    const [day, month, year] = dateStr.split('.').map(Number);
    return new Date(year, month - 1, day);
}

/**
 * Format any date to: "weekday, month day, year"
 * @param {Date|string} date - Date object or DD.MM.YYYY string
 */
export function formatFullDate(date) {
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const months = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];

    // If date is a string -> parse it first
    let dateObj = date;
    if (typeof date === 'string') {
        dateObj = parseDate(date);
        if (!dateObj) return '';
    }

    const dayName = days[dateObj.getDay()];
    const monthName = months[dateObj.getMonth()];
    const dayOfMonth = dateObj.getDate();
    const year = dateObj.getFullYear();

    return `${dayName}, ${monthName} ${dayOfMonth}, ${year}`;
}
 