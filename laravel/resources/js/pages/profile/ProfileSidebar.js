export function initProfileSidebar() {
    const navItems = document.querySelectorAll("[data-section-target]");
    const panels = document.querySelectorAll("[data-section-panel]");

    if (!navItems.length || !panels.length) return;

    function activateSection(target) {
        navItems.forEach((nav) => nav.classList.remove("is-active"));
        panels.forEach((panel) => panel.classList.remove("is-active"));

        const activeNav = document.querySelector(`[data-section-target="${target}"]`);
        const activePanel = document.querySelector(`[data-section-panel="${target}"]`);

        activeNav?.classList.add("is-active");
        activePanel?.classList.add("is-active");
    }

    navItems.forEach((item) => {
        item.addEventListener("click", () => {
            const target = item.dataset.sectionTarget;
            activateSection(target);
            window.location.hash = target;
        });
    });

    const currentHash = window.location.hash.replace("#", "");

    if (currentHash) {
        activateSection(currentHash);
    }
}
