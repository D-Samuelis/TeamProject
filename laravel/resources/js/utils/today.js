/**
 * Get today's date and day information
 */

export function getTodayInfo() {
    const today = new Date();
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const months = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];

    return {
        date: today,
        day: days[today.getDay()],
        dayNumber: today.getDate(),
        month: months[today.getMonth()],
        monthNumber: today.getMonth() + 1,
        year: today.getFullYear(),
        formatted: {
            short: `${today.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`,
            long: `${days[today.getDay()]}, ${months[today.getMonth()]} ${today.getDate()}, ${today.getFullYear()}`,
            iso: today.toISOString().split('T')[0]
        }
    };
}

export function displayToday(elementId) {
    const info = getTodayInfo();
    const el = document.getElementById(elementId);
    if (el) {
        el.innerHTML = `${info.formatted.long}`;
    }
    return info;
}
