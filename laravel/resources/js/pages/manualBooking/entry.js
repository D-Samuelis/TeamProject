import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initPublicBusinessDetail ,initBookingSearch} from './_main.js';

document.addEventListener('DOMContentLoaded', async () => {
    initCollapsibleList('targetList');
    initCollapsibleList('filterList');
    initCollapsibleList('branchList');

    initPublicBusinessDetail();
    initBookingSearch();
});