const SNACKBAR_DURATION_MS = 8000;
const TICK_MS = 50;

let stackEl = null;

function getStack() { return stackEl ??= document.getElementById("snackbar-stack"); }

export function showNavigations(navigations) {
    if (!navigations?.length) return;
    clearAll();
    capByType(navigations, 2).forEach(nav => showSnackbar({
        label: nav.label,
        type: nav.type,
        proceedIcon: "arrow_forward",
        onProceed: () => {
            if (window.innerWidth <= 850 && window.closeSidebar) window.closeSidebar();
            window.location.href = nav.url;
        }
    }));
}

export function showSuggestion(text) {
    if (!text) return;
    showSnackbar({
        label: text,
        type: "Reply",
        proceedIcon: "send",
        onProceed: () => { window.bexiOpenAndSend(text); }
    });
}

function capByType(navigations, max) {
    const counts = {};
    navigations.forEach(nav => { counts[nav.type] = (counts[nav.type] ?? 0) + 1; });
    return navigations.filter(nav => counts[nav.type] <= max);
}
function scrollMessages() {
    const messages = document.getElementById("messages");
    if (messages) messages.scrollTop = messages.scrollHeight;
}

function showSnackbar({ label, type, proceedIcon, onProceed }) {
    const stack = getStack();
    if (!stack) return;

    const sb = document.createElement("div");
    sb.className = "snackbar";

    sb.innerHTML = `
        <div class="snackbar-body">
            <span class="snackbar-type">${type ?? ""}</span>
            <p class="snackbar-label">${label}</p>
            <div class="snackbar-actions">
                <button class="snackbar-proceed" title="Proceed">
                    <span class="material-icons">${proceedIcon}</span>
                </button>
            </div>
        </div>
    `;

    stack.appendChild(sb);
    requestAnimationFrame(() => {
        sb.classList.add("snackbar-visible");
        scrollMessages();
    });

    let elapsed = 0;
    let paused = false;

    const interval = setInterval(() => {
        if (paused) return;
        elapsed += TICK_MS;
        if (elapsed >= SNACKBAR_DURATION_MS) dismiss();
    }, TICK_MS);

    sb.addEventListener("mouseenter", () => { paused = true; });
    sb.addEventListener("mouseleave", () => { paused = false; });

    sb.querySelector(".snackbar-proceed").addEventListener("click", () => {
        clearInterval(interval);
        dismiss();
        onProceed();
    });

    function dismiss() {
        sb.classList.remove("snackbar-visible");
        sb.classList.add("snackbar-hidden");
        sb.addEventListener("transitionend", () => {
            sb.remove();
            scrollMessages();
        }, { once: true });
    }
}
export function clearAll() {
    getStack()?.querySelectorAll(".snackbar").forEach(el => el.remove());
}
