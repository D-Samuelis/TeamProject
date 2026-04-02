import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initRuleDragDrop } from './dragDrop.js';

document.addEventListener('DOMContentLoaded', () => {
    initCollapsibleList('assetInfo');
    initCollapsibleList('branchesList');
    const container = document.querySelector('.rule-panel');

    initRuleDragDrop({
        reorderUrl: container ? container.dataset.reorderUrl : null
    });
});