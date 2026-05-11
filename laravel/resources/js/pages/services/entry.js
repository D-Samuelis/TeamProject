import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initServicesListView } from './listView.js';
import { initServiceConnectionsModal } from './modals/connectionsModal.js';
import { initCreateServiceModal } from './modals/createServiceModal.js';
import { initArchiveServiceModal } from './modals/archiveServiceModal.js';
import { initServiceShowPage } from './show.js';
import { initToolbar } from './toolbar.js';
import { initUserSearch } from "../../components/displays/userSearch.js";
import { initBusinessSearch } from "../../components/displays/businessSearch.js";

document.addEventListener('DOMContentLoaded', () => {
    initCollapsibleList('managementList');

    initServicesListView(window.BE_DATA.services, window.BE_DATA.meta);

    initServiceConnectionsModal(window.BE_DATA.services);

    initCreateServiceModal();
    initArchiveServiceModal();
    initServiceShowPage();

    initToolbar();

    initUserSearch();
    initBusinessSearch();
});
