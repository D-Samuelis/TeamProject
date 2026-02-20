/**
 * Calendar Details view handler for myAppointments page
 */

import { getTimeInfo } from '../../utils/time.js';
import { TIME_SLOT_HEIGHT } from '../../config/constants.js';

let timeIndicatorInterval = null;

/**
 * Generate and render the details for selected day
 * @param {string} date
 * @param {number} startHour
 * @param {number} endHour
 */
export function generateDetails(date, startHour, endHour) {
    const container = document.getElementById('calendarDetails');
    if (!container) {
        console.warn('[calendarDetails] #calendarDetails not found');
        return;
    }

    if (timeIndicatorInterval) {
        clearInterval(timeIndicatorInterval);
    }

    container.innerHTML = '';

    addDateToContainer(container, date);
    addNumOfAppotinmentsForDay(container, 0); // TODO: fetch this shit later

    const containerTimeContent = document.createElement('div');
    containerTimeContent.className = 'calendar-details-content';
    addTimeSlotsForDay(containerTimeContent, startHour, endHour);

    const timeIndicator = initTimeIndicator(containerTimeContent);

    container.appendChild(containerTimeContent);


    const runner = () => updateTimeIndicator(timeIndicator, startHour, endHour);
    runner();
    
    timeIndicatorInterval = setInterval(runner, 60000);
}

/**
 * Initialize time indicator
 * @param {element} container - master div for appending
 * 
 * @returns {element} timeIndicator - div of which we will be updating position 
 */
function initTimeIndicator(container) {
    const timeIndicator = document.createElement('div');
    timeIndicator.className = 'calendar-details-time-indicator';
    container.appendChild(timeIndicator);
    return timeIndicator;
}

/**
 * Updates position of time indicator
 * @param {element} indicator - div of which we will be updating position 
 * @param {number} startHour
 * @param {number} endHour
 */
function updateTimeIndicator(indicator, startHour, endHour) {
    if (!indicator) return;

    const { hours, minutes } = getTimeInfo();
    
    let position = (TIME_SLOT_HEIGHT * (hours - startHour)) + 
                   (TIME_SLOT_HEIGHT * minutes / 60);

    if (hours < startHour || hours >= endHour) {
        indicator.style.display = 'none';
    } else {
        indicator.style.display = 'block';
        indicator.style.top = `${position}rem`;
    }
}

/**
 * Renders date in container
 * @param {element} container - master div for appending
 * @param {string} date
 */
function addDateToContainer(container, date) {
    const div = document.createElement('div');
    div.className = 'calendar-details-date';
    div.textContent = date;
    container.appendChild(div);
}

/**
 * Renders date in container
 * @param {element} container - master div for appending
 * @param {string} count - number of appointments in day
 */
function addNumOfAppotinmentsForDay(container, count) {
    const div = document.createElement('div');
    div.className = 'calendar-details-summary';
    div.textContent = `[ ${count} ] Appointments for this day`;
    container.appendChild(div);
}

/**
 * Renders date in container
 * @param {element} container - master div for appending
 * @param {number} startHour
 * @param {number} endHour
 */
function addTimeSlotsForDay(container, startHour, endHour) {
    for (let hour = startHour; hour <= endHour; hour++) {
        const div = document.createElement('div');
        div.className = 'calendar-details-time-slot';

        const time = document.createElement('div');
        time.className = 'calendar-details-time-slot-time';
        time.textContent = `${String(hour).padStart(2, '0')}:00`;
        
        const content = document.createElement('div');
        content.className = 'calendar-details-time-slot-content';

        div.append(time, content);
        container.appendChild(div);
    }
}