import { initCollapsibleList } from "../../components/miniLists/miniList.js";
import { initBusinessListView } from "./listView.js";
import { initBusinessStatusFilters } from "./statusFilters.js"; /* TODO: CLEAR THIS SHIT INTO ONE FILE (+ assets entry) */
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

document.addEventListener("DOMContentLoaded", () => {
    const pendingToast = sessionStorage.getItem('pending_toast');
    if (pendingToast) {
        sessionStorage.removeItem("pending_toast");
        const { type, title, message } = JSON.parse(pendingToast);
        Toast[type]?.(title, message);
    }

    /* index */
    initCollapsibleList("managementList");
    initCollapsibleList("statusList");

    initBusinessListView(window.BE_DATA.businesses);

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
});
