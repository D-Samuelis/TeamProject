import { initCollapsibleList } from "../../components/miniLists/miniList.js";
import { initServicesListView } from "./listView.js";
import { initServiceConnectionsModal } from "./modals/connectionsModal.js";
import { initCreateServiceModal } from "./modals/createServiceModal.js";
import { initArchiveServiceModal } from "./modals/archiveServiceModal.js";
import { initEditServiceModal } from "./modals/editServiceModal.js";
import { initServiceShowPage } from "./show.js";
import { initToolbar } from "./toolbar.js";
import { Toast } from "../../components/displays/toast.js";

document.addEventListener("DOMContentLoaded", () => {
    const pendingToast = sessionStorage.getItem("pending_toast");
    if (pendingToast) {
        sessionStorage.removeItem("pending_toast");
        const { type, title, message } = JSON.parse(pendingToast);
        Toast[type]?.(title, message);
    }

    initToolbar();
    initCollapsibleList("managementList");

    if (window.BE_DATA.services) {
        initServicesListView(window.BE_DATA.services);
    }

    initServiceConnectionsModal();
    initCreateServiceModal();
    initArchiveServiceModal();
    initEditServiceModal();
    initServiceShowPage();
});
