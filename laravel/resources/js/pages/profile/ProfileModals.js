import { Modal } from "../../components/displays/modal.js";
import { initTitleComboboxes } from "../auth/titleCombobox";
import { initDatePickerToggle } from "../auth/datePickerToggle";
import { initProfileEditValidator } from "./profileEditValidator";

const genderOptions = [
    { value: "", label: "Select..." },
    { value: "male", label: "Male" },
    { value: "female", label: "Female" },
    { value: "other", label: "Other" },
];

function escapeHtml(value) {
    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}

function checked(value) {
    return value ? "checked" : "";
}

function profileData() {
    return window.PROFILE_DATA || { routes: {}, csrf: "", user: {} };
}

function profileFormBody() {
    const { routes, csrf, user } = profileData();
    const selectedGender = user.gender || "";

    return `
        <form class="modal-form form-profile-info" method="POST" action="${escapeHtml(routes.update)}">
            <input type="hidden" name="_token" value="${escapeHtml(csrf)}">
            <input type="hidden" name="_method" value="PATCH">

            <div class="profile-modal-grid">
                <div class="modal-form__group">
                    <label class="modal-form__label">Title before name</label>
                    <input class="modal-form__input" type="text" name="title_prefix" list="titles_before_list" value="${escapeHtml(user.title_prefix)}" placeholder=" ">
                    <div class="modal-form__error invalid-input-field"></div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Title after name</label>
                    <input class="modal-form__input" type="text" name="title_suffix" list="titles_after_list" value="${escapeHtml(user.title_suffix)}" placeholder=" ">
                    <div class="modal-form__error invalid-input-field"></div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Full name</label>
                    <input class="modal-form__input" type="text" name="name" value="${escapeHtml(user.name)}" placeholder=" " required>
                    <div class="modal-form__error invalid-input-field"></div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Email</label>
                    <input class="modal-form__input" type="email" name="email" value="${escapeHtml(user.email)}" placeholder=" " required>
                    <div class="modal-form__error invalid-input-field"></div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Phone</label>
                    <input class="modal-form__input" type="text" name="phone_number" value="${escapeHtml(user.phone_number)}" placeholder=" ">
                    <div class="modal-form__error invalid-input-field"></div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Birth date</label>
                    <div class="modal-form__input-wrapper modal-form__input-wrapper--date">
                        <input class="modal-form__input" type="date" name="birth_date" value="${escapeHtml(user.birth_date)}" placeholder=" ">
                        <button type="button" class="date-picker-toggle" tabindex="-1" aria-label="Open date picker">
                            <i class="fa-regular fa-calendar"></i>
                        </button>
                    </div>
                    <div class="modal-form__error invalid-input-field"></div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">City</label>
                    <input class="modal-form__input" type="text" name="city" value="${escapeHtml(user.city)}" placeholder=" ">
                    <div class="modal-form__error invalid-input-field"></div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Country</label>
                    <input class="modal-form__input" type="text" name="country" value="${escapeHtml(user.country)}" placeholder=" ">
                    <div class="modal-form__error invalid-input-field"></div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Gender</label>
                    <select class="modal-form__input" name="gender">
                        ${genderOptions.map((option) => `
                            <option value="${option.value}" ${selectedGender === option.value ? "selected" : ""}>${option.label}</option>
                        `).join("")}
                    </select>
                    <div class="modal-form__error invalid-input-field"></div>
                </div>
            </div>

            <div class="profile-modal-divider"></div>

            <div class="profile-modal-grid">
                <div class="modal-form__group">
                    <label class="modal-form__label">New password</label>
                    <input class="modal-form__input" type="password" name="password" autocomplete="new-password" placeholder=" ">
                    <div class="modal-form__error invalid-input-field"></div>
                </div>

                <div class="modal-form__group">
                    <label class="modal-form__label">Confirm new password</label>
                    <input class="modal-form__input" type="password" name="password_confirmation" autocomplete="new-password" placeholder=" ">
                    <div class="modal-form__error invalid-input-field"></div>
                </div>
            </div>

            <div class="modal-form__group profile-modal-current-password">
                <label class="modal-form__label">Current password</label>
                <input class="modal-form__input" type="password" name="current_password" autocomplete="current-password" placeholder=" " required>
                <p class="profile-modal-current-password__note">
                    Enter your current password to confirm any profile changes.
                </p>
                <div class="modal-form__error invalid-input-field"></div>
            </div>

            <datalist id="titles_before_list"></datalist>
            <datalist id="titles_after_list"></datalist>
        </form>
    `;
}

function settingsFormBody() {
    const { routes, csrf, user } = profileData();

    return `
        <form class="modal-form form-profile-settings" method="POST" action="${escapeHtml(routes.settings)}">
            <input type="hidden" name="_token" value="${escapeHtml(csrf)}">
            <input type="hidden" name="_method" value="PATCH">
            <input type="hidden" name="notify_email" value="0">
            <input type="hidden" name="notify_sms" value="0">
            <input type="hidden" name="is_visible" value="0">

            <div class="settings profile-modal-settings">
                <div class="setting-row">
                    <div>
                        <div class="setting-row__title">Email notifications</div>
                        <div class="setting-row__desc">Receive account and booking updates by email.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="notif-email" name="notify_email" value="1" ${checked(user.notify_email)}>
                        <span class="switch__slider"></span>
                    </label>
                </div>

                <div class="setting-row">
                    <div>
                        <div class="setting-row__title">SMS notifications</div>
                        <div class="setting-row__desc">Receive account and booking updates by SMS.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="notif-sms" name="notify_sms" value="1" ${checked(user.notify_sms)}>
                        <span class="switch__slider"></span>
                    </label>
                </div>

                <div class="setting-row">
                    <div>
                        <div class="setting-row__title">Visible account</div>
                        <div class="setting-row__desc">Allow your profile to be visible in the system.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="is_visible" value="1" ${checked(user.is_visible)}>
                        <span class="switch__slider"></span>
                    </label>
                </div>
            </div>
        </form>
    `;
}

function submitModalForm(modal) {
    const form = modal.querySelector("form");
    if (!form) return;

    if (typeof form.reportValidity === "function" && !form.reportValidity()) {
        return;
    }

    form.submit();
}

function openProfileModal() {
    Modal.showCustom({
        title: "Profile",
        action: "edit",
        type: "Edit profile",
        body: profileFormBody(),
        confirmText: "Save changes",
        onConfirm: submitModalForm,
            rules: {
            name: { required: { value: true, message: "Full name is required" } },
            email: { required: { value: true, message: "Email address is required" } },
            current_password: { required: { value: true, message: "Current password is required" } },
        },
    });

    initTitleComboboxes();
    initProfileEditValidator();
    initDatePickerToggle();
}

function openSettingsModal() {
    Modal.showCustom({
        title: "Settings",
        action: "edit",
        type: "Edit settings",
        body: settingsFormBody(),
        confirmText: "Save settings",
        onConfirm: submitModalForm,
    });
}

export function initProfileModals() {
    document.querySelector('[data-profile-modal="personal"]')?.addEventListener("click", openProfileModal);
    document.querySelector('[data-profile-modal="settings"]')?.addEventListener("click", openSettingsModal);
}
