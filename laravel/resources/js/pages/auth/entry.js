import initAuthToggle from './authToggle.js';
import initGenderSelection from './genderToggle.js';
import initAuthValidator from './authValidate.js';
import { initTitleComboboxes } from './titleCombobox.js';

document.addEventListener('DOMContentLoaded', () => {
    console.log("Entry auth registered");
    initAuthToggle();
    initGenderSelection();
    initAuthValidator();
    initTitleComboboxes();
});