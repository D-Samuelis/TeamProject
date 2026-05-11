import { BEXI_SIDEBAR_KEY } from "../../config/storageKeys";
import { openSidebar, closeSidebar } from "../../chatbot/main.js";

export function initBexiButton() {
    const bexiBtn = document.getElementById('bexiToggleBtn');
    if (bexiBtn) {
        bexiBtn.onclick = (e) => {
            e.stopPropagation();
            localStorage.setItem(BEXI_SIDEBAR_KEY, 'true');
            openSidebar();
        };
    }   
}