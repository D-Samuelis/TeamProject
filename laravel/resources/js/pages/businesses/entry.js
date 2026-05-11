import { initCollapsibleList } from "../../components/miniLists/miniList.js";
import { initBusinessListView } from "./listView.js";
<<<<<<< HEAD
=======
import { initBusinessStatusFilters } from "./statusFilters.js"; /* TODO: CLEAR THIS SHIT INTO ONE FILE (+ assets entry) */
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf
import { initCreateBusinessModal } from "./modals/createBusinessModal.js";
import { initEditBusinessMetaDataModal } from "./modals/editBusinessMetaDataModal.js";
import { initCreateBranchModal } from "./modals/createBranchModal.js";
import { initEditBranchModal } from "./modals/editBranchModal.js";
import { initBusinessViewSwitcher } from "./viewToggle.js";
import { initAssignEmployeeModal } from "./modals/assignEmployeeModal.js";
import { initListSearch } from "../../components/table/searchBar.js";
import { initArchiveBranchModal } from "../branches/modals/archiveBranchModal.js";
import { initRemoveUserModal } from "./modals/removeEmployeeModal.js";
import { initArchiveBusinessModal } from "./modals/archiveBusinessModal.js";
import { initToolbar } from "./toolbar.js";
import { Toast } from '../../components/displays/toast.js';
<<<<<<< HEAD
import { initUserSearch } from "../../components/displays/userSearch.js";
=======
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf

document.addEventListener("DOMContentLoaded", () => {
    const pendingToast = sessionStorage.getItem('pending_toast');
    if (pendingToast) {
        sessionStorage.removeItem("pending_toast");
        const { type, title, message } = JSON.parse(pendingToast);
        Toast[type]?.(title, message);
    }

    /* index */
<<<<<<< HEAD
    initCollapsibleList('managementList');
    initCollapsibleList('statusList');

    initBusinessListView(window.BE_DATA.businesses, window.BE_DATA.meta);
=======
    initCollapsibleList("managementList");
    initCollapsibleList("statusList");

    initBusinessListView(window.BE_DATA.businesses);
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf

    initCreateBusinessModal();

    /* show */
    initEditBusinessMetaDataModal();

    initCreateBranchModal();
    initEditBranchModal();

    initCollapsibleList("businessInfo");
    initCollapsibleList("branchesList");
    initCollapsibleList("manageEmployeesList");

    initBusinessViewSwitcher();

    initAssignEmployeeModal();

    initListSearch(
        "#appointmentSearchInput",
        ".filterable-member",
        ".js-search-data",
    );
    initListSearch("#appointmentSearchInput", ".filterable-service", "strong");

    initArchiveBranchModal();
    initRemoveUserModal();
    initArchiveBusinessModal();

    initToolbar();
<<<<<<< HEAD

    initUserSearch()
=======
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf
});
