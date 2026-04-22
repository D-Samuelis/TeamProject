import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initServicesListView } from './listView.js'; 

document.addEventListener('DOMContentLoaded', () => {
    initCollapsibleList('managementList');
    initServicesListView(window.BE_DATA.services);
});