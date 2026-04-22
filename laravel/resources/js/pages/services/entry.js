import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initServicesListView } from './listView.js'; 
import { initServiceConnectionsModal } from './modals/connectionsModal.js';
import { initServiceStatusFilters } from './statusFilters.js';
import { initCreateServiceModal } from './modals/createServiceModal.js';

document.addEventListener('DOMContentLoaded', () => {
    initCollapsibleList('managementList');
    
    initServicesListView(window.BE_DATA.services);
    initServiceStatusFilters();

    initServiceConnectionsModal(window.BE_DATA.services);

    initCreateServiceModal();
});