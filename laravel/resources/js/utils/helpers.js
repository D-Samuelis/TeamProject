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