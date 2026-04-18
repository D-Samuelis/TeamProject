const SNACKBAR_DURATION_MS = 8000;
const TICK_MS = 50;

const TYPE_LABELS = {
    business: "Business",
    service:  "Service",
    branch:   "Branch",
};

let stackEl = null;

function getStack() {
    if (!stackEl) stackEl = document.getElementById("snackbar-stack");
    return stackEl;
}

/**
 * Show a batch of navigation suggestions as stacked snackbars.
 * Older snackbars from the previous response are cleared first.
 *
 * @param {Array<{type: string, id: string, name: string, url: string, label: string}>} navigations
 */
export function showNavigations(navigations) {
    if (!navigations || navigations.length === 0) return;

    clearAll();

    const capped = capByType(navigations, 2);
    capped.forEach(nav => showSnackbar(nav));
}

function capByType(navigations, max) {
    const counts = {};
    navigations.forEach(nav => {
        counts[nav.type] = (counts[nav.type] ?? 0) + 1;
    });
    return navigations.filter(nav => counts[nav.type] <= max);
}

function showSnackbar(nav) {
    const stack = getStack();
    if (!stack) return;

    const sb = document.createElement("div");
    sb.className = "snackbar";
    sb.dataset.id = `${nav.type}-${nav.id}`;

    sb.innerHTML = `
        <div class="snackbar-body">
            <span class="snackbar-type">${TYPE_LABELS[nav.type] ?? nav.type}</span>
            <p class="snackbar-label">Navigate to detail page of ${TYPE_LABELS[nav.type] ?? nav.type} <strong>${escapeHtml(nav.name)}</strong>?</p>
            <div class="snackbar-actions">
                <button class="snackbar-proceed">Proceed</button>
                <button class="snackbar-dismiss">Dismiss</button>
            </div>
        </div>
        <div class="snackbar-progress">
            <div class="snackbar-progress-bar"></div>
        </div>
    `;

    const progressBar = sb.querySelector(".snackbar-progress-bar");
    const proceedBtn  = sb.querySelector(".snackbar-proceed");
    const dismissBtn  = sb.querySelector(".snackbar-dismiss");

    stack.append(sb);

    requestAnimationFrame(() => sb.classList.add("snackbar-visible"));

    let elapsed = 0;
    let paused = false;

    const interval = setInterval(() => {
        if (paused) return;
        elapsed += TICK_MS;
        const pct = Math.max(0, 100 - (elapsed / SNACKBAR_DURATION_MS) * 100);
        progressBar.style.width = `${pct}%`;
        if (elapsed >= SNACKBAR_DURATION_MS) dismiss();
    }, TICK_MS);

    sb.addEventListener("mouseenter", () => { paused = true; });
    sb.addEventListener("mouseleave", () => { paused = false; });

    proceedBtn.addEventListener("click", () => {
        clearInterval(interval);
        window.location.href = nav.url;
        sessionStorage.setItem('bexi-open', 'false');
    });

    dismissBtn.addEventListener("click", () => {
        clearInterval(interval);
        dismiss();
    });

    function dismiss() {
        sb.classList.remove("snackbar-visible");
        sb.classList.add("snackbar-hidden");
        sb.addEventListener("transitionend", () => sb.remove(), { once: true });
    }
}

export function clearAll() {
    const stack = getStack();
    if (!stack) return;
    stack.querySelectorAll(".snackbar").forEach(el => el.remove());
}

function escapeHtml(str) {
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}
