import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initRuleDragDrop } from './dragDrop.js';

document.addEventListener('DOMContentLoaded', () => {
    initCollapsibleList('assetInfo');
    initCollapsibleList('branchesList');
    initCollapsibleList('actionsList');
    const container = document.querySelector('.rule-panel');

    initRuleDragDrop({
        reorderUrl: container ? container.dataset.reorderUrl : null
    });
    /* 
    initBusinessListView(window.BE_DATA.businesses);
    initBusinessStatusFilters();

    initCreateBusinessModal();
    initEditBusinessMetaDataModal();

    initCreateBranchModal();
    initEditBranchModal();

    initCollapsibleList('businessInfo');
    initCollapsibleList('branchesList');
    initCollapsibleList('manageEmployeesList');

    initBusinessViewSwitcher();

    initAssignEmployeeModal();

    initListSearch('#appointmentSearchInput', '.filterable-member', '.js-search-data');
    initListSearch('#appointmentSearchInput', '.filterable-service', 'strong');

    initArchiveBranchModal();
    initRemoveUserModal();
    initArchiveBusinessModal(); */
});