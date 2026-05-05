import { initBranchListView } from './listView.js';
import { initToolbar } from './toolbar.js';
import { initArchiveBranchModal } from './modals/archiveBranchModal.js';
import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initBranchConnectionsModal } from './modals/connectionsModal.js';
import { initCreateBranchModal } from './modals/createBranchModal.js';
import { initEditBranchModal } from './modals/editBranchModal.js';

document.addEventListener('DOMContentLoaded', () => {
    initToolbar();
    initCollapsibleList('branchInfo');

    if (window.BE_DATA.branches) {
        initBranchListView(window.BE_DATA.branches);
    }

    initArchiveBranchModal();
    initBranchConnectionsModal();
    initCreateBranchModal();
    initEditBranchModal();
});