import { initModals } from './modals.js';
import { initListSearch } from '../../components/sort/searchBar.js';
import { initCollapsibleList } from '../../components/miniLists/miniList.js';

document.addEventListener('DOMContentLoaded', () => {
    console.log("business init")
    initModals();
    initListSearch('#businessSearchInput', '.business-card', '.js-search-data');
    initCollapsibleList('managementList');
});