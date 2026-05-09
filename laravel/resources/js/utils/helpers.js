/* General purpose utilities */

/**
 * Log window width and height on resize
 */
export function logWindowSizeOnResize() {
    function logSize() {
        console.log(`Window size: ${window.innerWidth} x ${window.innerHeight} px`);
    }
    window.addEventListener('resize', logSize);

    logSize();
}

/**
 * Escapes HTML characters in a string to prevent XSS when inserting into HTML
 * @param {*} str 
 * @returns 
 */
export function _esc(str) {
    if (!str) return "";
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
}