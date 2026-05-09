import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initServicesListView } from './listView.js';
import { initServiceConnectionsModal } from './modals/connectionsModal.js';
import { initCreateServiceModal } from './modals/createServiceModal.js';
import { initArchiveServiceModal } from './modals/archiveServiceModal.js';
import { initServiceShowPage } from './show.js';
import { initToolbar } from './toolbar.js';
import { Toast } from "../../components/displays/toast.js";
import { initUserSearch } from "../../components/displays/userSearch.js";
import { initBusinessSearch } from "../../components/displays/businessSearch.js";

document.addEventListener('DOMContentLoaded', () => {
    const pendingToast = sessionStorage.getItem("pending_toast");
    if (pendingToast) {
        sessionStorage.removeItem("pending_toast");
        const { type, title, message } = JSON.parse(pendingToast);
        Toast[type]?.(title, message);
    }
    
    initCollapsibleList('managementList');

    initServicesListView(window.BE_DATA.services, window.BE_DATA.meta);

    initCollapsibleList("managementList");
    initServicesListView(window.BE_DATA.services);

    initServiceConnectionsModal(window.BE_DATA.services);

    initCreateServiceModal();
    initArchiveServiceModal();
    initServiceShowPage();

    initToolbar();

    initUserSearch();
    initBusinessSearch();
});
