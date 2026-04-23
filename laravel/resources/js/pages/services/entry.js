import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initServicesListView } from './listView.js'; 
import { initServiceConnectionsModal } from './modals/connectionsModal.js';
import { initServiceStatusFilters } from './statusFilters.js';
import { initCreateServiceModal } from './modals/createServiceModal.js';
import { initArchiveServiceModal } from './modals/archiveServiceModal.js';

document.addEventListener('DOMContentLoaded', () => {
    initCollapsibleList('managementList');
    
    initServicesListView(window.BE_DATA.services);
    initServiceStatusFilters();

    initServiceConnectionsModal(window.BE_DATA.services);

    initCreateServiceModal();
    initArchiveServiceModal();
});