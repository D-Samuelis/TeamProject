import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initBookingSearch, initRangeSliders } from './_main.js';

document.addEventListener('DOMContentLoaded', () => {
    initCollapsibleList('targetList');
    initCollapsibleList('filterList');
    initBookingSearch();
    initRangeSliders();
});