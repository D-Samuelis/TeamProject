import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initBookingSearch, initCategorySelect, initRangeSliders } from './_main.js';

document.addEventListener('DOMContentLoaded', () => {
    initCollapsibleList('targetList');
    initCollapsibleList('filterList');
    initBookingSearch();
    initCategorySelect();
    initRangeSliders();
});
