const SELECTORS = {
    container: '.rule-panel',
    card: '.rule-card',
    handle: '.rule-card__drag-handle',
    expandBtn: '.js-rule-expand'
};

export function initRuleDragDrop(options = {}) {
    console.log('initRuleDragDrop called', options);
    const container = document.querySelector(SELECTORS.container);
    console.log('container:', container);
    if (!container) return;

    const reorderUrl = options.reorderUrl;
    const csrfToken = options.csrf 
        || document.querySelector('meta[name="csrf-token"]')?.content;

    new Sortable(container, {
        animation: 150,
        handle: SELECTORS.card,
        ghostClass: 'rule-card--dragging',
        fallbackTolerance: 3,
        scroll: true,
        
        onStart: function() {
            container.classList.add('is-sorting');
        },
        
        onEnd: async function () {
            container.classList.remove('is-sorting');
            const cards = Array.from(container.querySelectorAll(SELECTORS.card));
            
            cards.forEach((card, index) => {
                const label = card.querySelector('.js-priority-label');
                if (label) {
                    const prefix = label.querySelector('span') ? '<span>#</span>' : '#';
                    label.innerHTML = `${prefix}${index + 1}`;
                }
            });

            if (!reorderUrl) return;

            const orderedIds = cards
                .map(card => card.dataset.ruleId)
                .filter(Boolean);

            console.log('reorderUrl:', reorderUrl);
            console.log('orderedIds:', orderedIds);
            console.log('csrfToken:', csrfToken);

            try {
                await fetch(reorderUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ order: orderedIds }),
                });
            } catch (err) {
                console.error('Failed to save order:', err);
            }
        }
    });

    const setupExpandToggles = () => {
        const expandButtons = container.querySelectorAll(SELECTORS.expandBtn);

        expandButtons.forEach(btn => {
            console.log("button");
            btn.onclick = (e) => {
                console.log("click");
                e.preventDefault();
                const card = btn.closest(SELECTORS.card);
                if (!card) return;

                card.classList.toggle('is-expanded');
                
                const labelSpan = btn.querySelector('span');
                if (labelSpan) {
                    labelSpan.textContent = card.classList.contains('is-expanded') 
                        ? 'Hide Schedule' 
                        : 'Show Schedule';
                }
            };
        });
    };

    setupExpandToggles();

    injectStyles();
}

function injectStyles() {
    if (document.head.dataset.ruleDragDropStyles) return;
    document.head.dataset.ruleDragDropStyles = '1';

    const style = document.createElement('style');
    style.textContent = `
        ${SELECTORS.card} {
            cursor: grab;
            user-select: none;
        }

        ${SELECTORS.card}:active {
            cursor: grabbing;
        }

        .rule-card--dragging {
            opacity: 0.35 !important;
            background: #fff !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: 2px solid #7b58fb !important;
            z-index: 9999;
        }

        /* Zabezpečíme, aby interaktívne prvky neprepúšťali drag, ak netreba */
        ${SELECTORS.card} button, 
        ${SELECTORS.card} a, 
        ${SELECTORS.card} input {
            cursor: pointer;
            position: relative;
            z-index: 10;
        }
    `;
    document.head.appendChild(style);
}