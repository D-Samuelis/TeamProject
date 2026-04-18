const SNACKBAR_DURATION_MS = 8000;
const TICK_MS = 50;

const TYPE_LABELS = {
    business: "Business",
    service:  "Service",
    branch:   "Branch",
};

let stackEl   = null;
let templateEl = null;

function getStack()    { return stackEl    ??= document.getElementById("snackbar-stack"); }
function getTemplate() { return templateEl ??= document.getElementById("snackbar-template"); }

export function showNavigations(navigations) {
    if (!navigations?.length) return;
    clearAll();
    capByType(navigations, 2).forEach(nav => showSnackbar(nav));
}

function capByType(navigations, max) {
    const counts = {};
    navigations.forEach(nav => { counts[nav.type] = (counts[nav.type] ?? 0) + 1; });
    return navigations.filter(nav => counts[nav.type] <= max);
}

function showSnackbar(nav) {
    const stack = getStack();
    if (!stack) return;

    const label = TYPE_LABELS[nav.type] ?? nav.type;

    const sb = document.createElement("div");
    sb.className  = "snackbar";
    sb.dataset.id = `${nav.type}-${nav.id}`;
    sb.appendChild(getTemplate().content.cloneNode(true));

    sb.querySelector(".snackbar-type").textContent         = label;
    sb.querySelector(".snackbar-type-inline").textContent  = label;
    sb.querySelector(".snackbar-name").textContent         = nav.name;

    const progressBar = sb.querySelector(".snackbar-progress-bar");
    const proceedBtn  = sb.querySelector(".snackbar-proceed");
    const dismissBtn  = sb.querySelector(".snackbar-dismiss");

    stack.append(sb);
    requestAnimationFrame(() => sb.classList.add("snackbar-visible"));

    let elapsed = 0;
    let paused  = false;

    const interval = setInterval(() => {
        if (paused) return;
        elapsed += TICK_MS;
        progressBar.style.width = `${Math.max(0, 100 - (elapsed / SNACKBAR_DURATION_MS) * 100)}%`;
        if (elapsed >= SNACKBAR_DURATION_MS) dismiss();
    }, TICK_MS);

    sb.addEventListener("mouseenter", () => { paused = true; });
    sb.addEventListener("mouseleave", () => { paused = false; });

    proceedBtn.addEventListener("click", () => {
        clearInterval(interval);
        window.location.href = nav.url;
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
    getStack()?.querySelectorAll(".snackbar").forEach(el => el.remove());
}
