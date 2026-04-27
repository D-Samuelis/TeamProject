import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initRuleDragDrop } from './dragDrop.js';
import { initEditAssetModal } from './modals/editAssetModal.js';
import { initCreateRuleModal } from './modals/createRuleModal.js';
import { initEditRuleModal } from './modals/editRuleModal.js';
import { initArchiveAssetModal } from './modals/archiveAssetModal.js';
import { initDeleteRuleModal } from '../assets/modals/deleteRuleModal.js';
import { initListSearch } from '../../components/table/searchBar.js';
import { initAssetListView } from './listView.js';
import { initAssetStatusFilters } from './statusFilters.js'; /* TODO: CLEAR THIS SHIT INTO ONE FILE (+ businesses entry) */
import { initConnectionsModal } from './modals/connectionsModal.js';
import { initRuleDetailModal } from './modals/ruleDetailModal.js';
import { initCreateAssetModal } from './modals/createAssetModal.js';
import { initToolbar } from './toolbar.js';

document.addEventListener('DOMContentLoaded', () => {
    /* index */
    initCollapsibleList('managementList');
    initCollapsibleList('statusList');

    initAssetListView(window.BE_DATA.assets);

    initListSearch('#assetSearchInput', '.asset-table__row', 'strong');

    const createBtn = document.querySelector('[data-modal-target="create-asset-modal"]');
    if (createBtn) {
        createBtn.addEventListener('click', () => {
            const modal = document.getElementById('createAssetModal');
            if (modal) modal.style.display = 'flex';
        });
    }
    initConnectionsModal();
    initRuleDetailModal();
    initCreateAssetModal();

    /* show */
    initCollapsibleList('assetInfo');
    initCollapsibleList('branchesList');
    const container = document.querySelector('.rule-panel');

    initRuleDragDrop({
        reorderUrl: container ? container.dataset.reorderUrl : null,
        canUpdate: window.BE_DATA?.canUpdate ?? false,
    });

    initEditAssetModal();
    initCreateRuleModal();
    initEditRuleModal();
    initArchiveAssetModal();
    initDeleteRuleModal();
    initListSearch('#ruleSearchInput', '.filterable-rule', '.js-search-data');

    initToolbar();
});
