/**
 * Helper functions for generating random text and populating input placeholders
 */
export const getRandomArrayItem = (array) => {
    if (!array || array.length === 0) return null;
    return array[Math.floor(Math.random() * array.length)];
};

/**
 * Set a random placeholder from a list of samples to an input element
 */
export const setRandomPlaceholder = (element, samples) => {
    if (!element || !samples) return;
    element.placeholder = getRandomArrayItem(samples);
};