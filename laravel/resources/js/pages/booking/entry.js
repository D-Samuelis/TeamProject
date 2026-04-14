import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initPublicBusinessDetail } from './_business.js';
import { initPublicAssetBook } from './_asset.js';

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('branchList')) {
        initCollapsibleList('branchList');
    }

    if (document.querySelector('.public-business-detail')) {
        initPublicBusinessDetail();
    }

    if (document.querySelector('.public-asset-book')) {
        initPublicAssetBook();
    }
});