/**
 * Get time information for today, including formatted versions
 */

export function getTimeInfo() {
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes();
    const seconds = now.getSeconds();

    return {
        hours,
        minutes,
        seconds,
    }
}

export function displayTime(elementId) {
    const info = getTimeInfo();
    const el = document.getElementById(elementId);  
    if (el) {
        el.innerHTML = `${info.formatted.long}`;
    }
}