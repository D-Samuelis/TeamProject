import { initCollapsibleList } from "../../components/miniLists/miniList.js";

export function initServiceShowPage() {
    const page = document.querySelector(".service-settings-page");
    if (!page) return;

    initCollapsibleList("serviceInfo");
    initCollapsibleList("serviceConnections");

    initServiceViewToggle();
}

function initServiceViewToggle() {
    const coreBtn = document.getElementById("showServiceCore");
    const branchesBtn = document.getElementById("showServiceBranches");
    const coreView = document.getElementById("serviceCoreView");
    const branchesView = document.getElementById("serviceBranchesView");

    if (!coreBtn || !branchesBtn || !coreView || !branchesView) return;

    const activate = (view) => {
        const showCore = view === "core";

        coreBtn.classList.toggle("active", showCore);
        branchesBtn.classList.toggle("active", !showCore);
        coreView.classList.toggle("hidden", !showCore);
        branchesView.classList.toggle("hidden", showCore);
    };

    coreBtn.addEventListener("click", () => activate("core"));
    branchesBtn.addEventListener("click", () => activate("branches"));
}