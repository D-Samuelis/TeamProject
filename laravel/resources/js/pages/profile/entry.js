import "../../../css/pages/profile/index.css";
import { initProfilePasswordValidator } from "./PasswordValidator";
import { initProfileEditValidator } from "./profileEditValidator";
import { initTitleComboboxes } from "../auth/titleCombobox";
import { initNotify } from "./NotificationSettings";
import { initProfileSidebar } from "./ProfileSidebar";

document.addEventListener("DOMContentLoaded", () => {

    initProfilePasswordValidator();
    initProfileEditValidator();
    initTitleComboboxes();
    initNotify();
    initProfileSidebar();




});