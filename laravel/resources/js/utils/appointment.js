/**
 * Appointment utilities for managing and filtering appointments
 */

/**
 * Sample appointment data
 * @type {Array<{date: string, title: string, duration: number}>}
 */
export const appointments = [
    { date: '20.03.2025', title: 'Haircut', duration: 60 },
    { date: '25.03.2025', title: 'Beard Trim', duration: 30 },
    { date: '10.02.2025', title: 'Full Styling', duration: 90 },
];

/**
 * Get all appointments for a specific date
 * @param {string} dateStr - Format: DD.MM.YYYY
 * @returns {Array}
 */
export function getAppointmentsForDate(dateStr) {
    return appointments.filter(a => a.date === dateStr);
}

/**
 * Get appointment count for a specific month
 * @param {number} year
 * @param {number} month - 0-indexed (0 = January)
 * @returns {number}
 */
export function getAppointmentCountForMonth(year, month) {
    return appointments.filter(a => {
        const [day, m, y] = a.date.split('.').map(Number);
        return y === year && m === month + 1;
    }).length;
}

/**
 * Get all unique months with appointments
 * @returns {Array<{year: number, month: number}>}
 */
export function getMonthsWithAppointments() {
    const months = new Set();
    appointments.forEach(a => {
        const [, month, year] = a.date.split('.').map(Number);
        months.add(`${year}-${month}`);
    });
    return Array.from(months).map(str => {
        const [year, month] = str.split('-').map(Number);
        return { year, month };
    });
}
