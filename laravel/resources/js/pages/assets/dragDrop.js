/**
 * Rule Drag & Drop — reordering via HTML5 Drag API
 *
 * Usage:
 *   import { initRuleDragDrop } from './ruleDragDrop.js';
 *   document.addEventListener('DOMContentLoaded', () => initRuleDragDrop());
 *
 * Expects:
 *   - Rule cards wrapped in a container matching SELECTORS.container
 *   - Each card matching SELECTORS.card with data-rule-id attribute
 *   - A CSRF token in <meta name="csrf-token"> or window.CSRF_TOKEN
 */

// ── Config ────────────────────────────────────────────────────────────────────

const SELECTORS = {
    container: '.business__panel',
    card:      '[data-rule-id]',
};

const CSS = {
    dragging:   'rule-card--dragging',
    dragOver:   'rule-card--drag-over',
    dragHandle: 'rule-card__drag-handle',
};

// ── State ─────────────────────────────────────────────────────────────────────

let draggedCard  = null;
let draggedIndex = null;
let csrfToken    = null;
let reorderUrl   = null;

// ── Public API ────────────────────────────────────────────────────────────────

/**
 * Initialise drag & drop on all rule cards.
 *
 * @param {Object} options
 * @param {string} options.reorderUrl  - POST endpoint for persisting new order
 *                                       e.g. '/manage/rules/reorder'
 * @param {string} [options.csrf]      - CSRF token (falls back to meta tag)
 */
export function initRuleDragDrop(options = {}) {
    reorderUrl = options.reorderUrl ?? null;
    csrfToken  = options.csrf
        ?? document.querySelector('meta[name="csrf-token"]')?.content
        ?? window.CSRF_TOKEN
        ?? null;

    attachHandlers();
    injectStyles();
}

// ── Setup ─────────────────────────────────────────────────────────────────────

/**
 * Find all cards and attach drag event listeners.
 * Safe to call multiple times — removes old listeners first.
 */
function attachHandlers() {
    const cards = getCards();

    cards.forEach((card, index) => {
        card.setAttribute('draggable', 'true');
        card.dataset.dragIndex = index;

        ensureDragHandle(card);

        // Remove old listeners before re-attaching
        card.removeEventListener('dragstart',  onDragStart);
        card.removeEventListener('dragend',    onDragEnd);
        card.removeEventListener('dragover',   onDragOver);
        card.removeEventListener('dragenter',  onDragEnter);
        card.removeEventListener('dragleave',  onDragLeave);
        card.removeEventListener('drop',       onDrop);

        card.addEventListener('dragstart',  onDragStart);
        card.addEventListener('dragend',    onDragEnd);
        card.addEventListener('dragover',   onDragOver);
        card.addEventListener('dragenter',  onDragEnter);
        card.addEventListener('dragleave',  onDragLeave);
        card.addEventListener('drop',       onDrop);
    });
}

/**
 * Inject a drag handle icon into a card if it doesn't already have one.
 * @param {HTMLElement} card
 */
function ensureDragHandle(card) {
    if (card.querySelector(`.${CSS.dragHandle}`)) return;

    const handle = document.createElement('div');
    handle.className = CSS.dragHandle;
    handle.title     = 'Drag to reorder';
    handle.innerHTML = '<i class="fa-solid fa-grip-vertical"></i>';

    card.prepend(handle);
}

// ── Drag Event Handlers ───────────────────────────────────────────────────────

/**
 * @param {DragEvent} e
 */
function onDragStart(e) {
    draggedCard  = this;
    draggedIndex = parseInt(this.dataset.dragIndex);

    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', this.dataset.ruleId);

    // Slight delay so the ghost image is captured before the class changes opacity
    requestAnimationFrame(() => this.classList.add(CSS.dragging));
}

/**
 * @param {DragEvent} e
 */
function onDragEnd(e) {
    this.classList.remove(CSS.dragging);
    clearAllDragOver();
    draggedCard  = null;
    draggedIndex = null;
}

/**
 * @param {DragEvent} e
 */
function onDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
}

/**
 * @param {DragEvent} e
 */
function onDragEnter(e) {
    e.preventDefault();
    if (this === draggedCard) return;
    this.classList.add(CSS.dragOver);
}

/**
 * @param {DragEvent} e
 */
function onDragLeave(e) {
    // Only clear if we're leaving the card itself, not a child element
    if (this.contains(e.relatedTarget)) return;
    this.classList.remove(CSS.dragOver);
}

/**
 * @param {DragEvent} e
 */
function onDrop(e) {
    e.preventDefault();

    if (!draggedCard || this === draggedCard) return;

    this.classList.remove(CSS.dragOver);

    const container  = getContainer();
    const cards      = getCards();
    const dropIndex  = parseInt(this.dataset.dragIndex);

    // Reorder in the DOM
    reorderInDom(container, draggedCard, this, draggedIndex, dropIndex);

    // Re-index all cards after DOM change
    refreshIndexes();

    // Persist to the server
    persistOrder();
}

// ── DOM Manipulation ──────────────────────────────────────────────────────────

/**
 * Move draggedCard before or after the target depending on direction.
 * @param {HTMLElement} container
 * @param {HTMLElement} dragged
 * @param {HTMLElement} target
 * @param {number} fromIndex
 * @param {number} toIndex
 */
function reorderInDom(container, dragged, target, fromIndex, toIndex) {
    if (fromIndex < toIndex) {
        container.insertBefore(dragged, target.nextSibling);
    } else {
        container.insertBefore(dragged, target);
    }
}

/**
 * Update data-drag-index on all cards after a reorder.
 */
function refreshIndexes() {
    console.log("ref");
    getCards().forEach((card, index) => {
        const newIndex = index + 1;
        card.dataset.dragIndex = index;
        
        const label = card.querySelector('.js-priority-label');
        if (label) {
            label.textContent = `#${newIndex}`;
        }
    });
}

/**
 * Remove drag-over styling from every card.
 */
function clearAllDragOver() {
    getCards().forEach(card => card.classList.remove(CSS.dragOver));
}

// ── Server Persistence ────────────────────────────────────────────────────────

/**
 * Send the new ordered list of rule IDs to the server.
 * Silently fails if no reorderUrl was provided.
 */
async function persistOrder() {
    if (!reorderUrl) return;

    const orderedIds = getCards().map(card => card.dataset.ruleId);

    try {
        const response = await fetch(reorderUrl, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken ?? '',
                'Accept':       'application/json',
            },
            body: JSON.stringify({ order: orderedIds }),
        });

        if (!response.ok) {
            console.error('[RuleDragDrop] Server responded with', response.status);
        }
    } catch (err) {
        console.error('[RuleDragDrop] Failed to persist order:', err);
    }
}

// ── Helpers ───────────────────────────────────────────────────────────────────

/**
 * @returns {HTMLElement}
 */
function getContainer() {
    return document.querySelector(SELECTORS.container);
}

/**
 * @returns {HTMLElement[]}
 */
function getCards() {
    return Array.from(document.querySelectorAll(SELECTORS.card));
}

// ── Styles ────────────────────────────────────────────────────────────────────

/**
 * Inject minimal CSS needed for drag states.
 * Only injected once — guarded by a data attribute on <head>.
 */
function injectStyles() {
    if (document.head.dataset.ruleDragDropStyles) return;
    document.head.dataset.ruleDragDropStyles = '1';

    const style = document.createElement('style');
    style.textContent = `
        .${CSS.dragHandle} {
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: grab;
            color: #ccc;
            padding: 0 6px 0 0;
            flex-shrink: 0;
            font-size: 13px;
            transition: color 0.15s;
        }
        .${CSS.dragHandle}:hover { color: #888; }
        .${CSS.dragHandle}:active { cursor: grabbing; }

        [data-rule-id] {
            cursor: default;
            transition: opacity 0.15s, box-shadow 0.15s;
        }

        .${CSS.dragging} {
            opacity: 0.4;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }

        .${CSS.dragOver} {
            box-shadow: 0 0 0 2px #2563eb;
        }
    `;
    document.head.appendChild(style);
}