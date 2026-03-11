import { initModals } from './modals.js';
import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initBusinessListView } from './listView.js';

document.addEventListener('DOMContentLoaded', () => {
    initModals();
    initCollapsibleList('managementList');

    initBusinessListView(window.BE_DATA.businesses);
});