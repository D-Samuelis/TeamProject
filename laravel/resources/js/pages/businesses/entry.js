import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initBusinessListView } from './listView.js';
import { initBusinessStatusFilters } from './statusFilters.js';
import { initCreateBusinessModal } from './modals/createBusinessModal.js';
import { initEditBusinessMetaDataModal } from './modals/editBusinessMetaDataModal.js';
import { initCreateBranchModal } from './modals/createBranchModal.js';
import { initEditBranchModal } from './modals/editBranchModal.js';

document.addEventListener('DOMContentLoaded', () => {
    initCollapsibleList('managementList');
    initCollapsibleList('statusList');
    
    initBusinessListView(window.BE_DATA.businesses);
    initBusinessStatusFilters();

    initCreateBusinessModal();
    initEditBusinessMetaDataModal();

    initCreateBranchModal();
    initEditBranchModal();

    initCollapsibleList('businessInfo');
    initCollapsibleList('branchesList');
});