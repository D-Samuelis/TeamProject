/**
 * @param {string} inputSelector - Selector for search input
 * @param {string} cardSelector  - Selector for whole item/card/row (what we hide)
 * @param {string|null} targetSelector - (Optional) Only if you wish to look in certain places (example: '.search-data')
 */
export function initListSearch(inputSelector, cardSelector, targetSelector = null) {
    const searchInput = document.querySelector(inputSelector);
    if (!searchInput) return;

    searchInput.addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase().trim();
        const cards = document.querySelectorAll(cardSelector);

        cards.forEach(card => {
            let searchableText = "";

            if (targetSelector) {
                const targets = card.querySelectorAll(targetSelector);
                targets.forEach(el => searchableText += " " + el.textContent.toLowerCase());
            } else {
                searchableText = card.textContent.toLowerCase();
            }

            card.style.display = searchableText.includes(term) ? "" : "none";
        });
    });
}