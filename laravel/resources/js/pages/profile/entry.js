import "../../../css/pages/profile/index.css";
import { initProfileModals } from "./ProfileModals";
import { initProfileEditValidator } from "./profileEditValidator";
import { initTitleComboboxes } from "../auth/titleCombobox";
import { initDatePickerToggle } from "../auth/datePickerToggle";
import { initNotify } from "./NotificationSettings";
import { initProfileSidebar } from "./ProfileSidebar";

document.addEventListener("DOMContentLoaded", () => {
    initProfileModals();
    initProfileEditValidator();
    initTitleComboboxes();
    initDatePickerToggle();
    initNotify();
    initProfileSidebar();
});
