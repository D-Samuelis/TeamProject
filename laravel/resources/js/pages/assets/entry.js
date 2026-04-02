import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initRuleDragDrop } from './dragDrop.js';
import { initEditAssetModal } from './modals/editAssetModal.js';
import { initCreateRuleModal } from './modals/createRuleModal.js';
import { initEditRuleModal } from './modals/editRuleModal.js';

document.addEventListener('DOMContentLoaded', () => {
    initCollapsibleList('assetInfo');
    initCollapsibleList('branchesList');
    const container = document.querySelector('.rule-panel');

    initRuleDragDrop({
        reorderUrl: container ? container.dataset.reorderUrl : null
    });

    initEditAssetModal();
    initCreateRuleModal();
    initEditRuleModal();
});