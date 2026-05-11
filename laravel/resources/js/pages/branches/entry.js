<<<<<<< HEAD
import { initBranchListView } from './listView.js';
import { initToolbar } from './toolbar.js';
import { initArchiveBranchModal } from './modals/archiveBranchModal.js';
import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initBranchConnectionsModal } from './modals/connectionsModal.js';
import { initCreateBranchModal } from './modals/createBranchModal.js';
import { initEditBranchModal } from './modals/editBranchModal.js';
import { initServiceAssigner } from './serviceAssigner.js';
import { initBusinessSearch } from "../../components/displays/businessSearch.js";
import { Toast } from '../../components/displays/toast.js';

document.addEventListener("DOMContentLoaded", () => {
    const pendingToast = sessionStorage.getItem("pending_toast");
    if (pendingToast) {
        sessionStorage.removeItem("pending_toast");
        const { type, title, message } = JSON.parse(pendingToast);
        Toast[type]?.(title, message);
    }
=======
import { initBranchListView } from "./listView.js";
import { initToolbar } from "./toolbar.js";
import { initArchiveBranchModal } from "./modals/archiveBranchModal.js";
import { initCollapsibleList } from "../../components/miniLists/miniList.js";
import { initBranchConnectionsModal } from "./modals/connectionsModal.js";
import { initCreateBranchModal } from "./modals/createBranchModal.js";
import { initEditBranchModal } from "./modals/editBranchModal.js";
import { initServiceAssigner } from "./serviceAssigner.js";
import { Toast } from '../../components/displays/toast.js';

document.addEventListener("DOMContentLoaded", () => {
    const pendingToast = sessionStorage.getItem("pending_toast");
    if (pendingToast) {
        sessionStorage.removeItem("pending_toast");
        const { type, title, message } = JSON.parse(pendingToast);
        Toast[type]?.(title, message);
    }

>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf
    initToolbar();
    initCollapsibleList("branchInfo");

    if (window.BE_DATA.branches) {
        initBranchListView(window.BE_DATA.branches, window.BE_DATA.meta);
    }

    initServiceAssigner();

    initArchiveBranchModal();
    initBranchConnectionsModal();
    initCreateBranchModal();
    initEditBranchModal();
<<<<<<< HEAD
    initBusinessSearch();
=======
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf
});
