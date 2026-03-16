import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initBusinessListView } from './listView.js';
import { initBusinessStatusFilters } from './statusFilters.js';
import { initCreateBusinessModal } from './modals/createBusinessModal.js';

document.addEventListener('DOMContentLoaded', () => {
    initCollapsibleList('managementList');
    initCollapsibleList('statusList');
    
    initBusinessListView(window.BE_DATA.businesses);
    initBusinessStatusFilters();

    initCreateBusinessModal();
});