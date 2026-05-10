import initAuthToggle from './authToggle.js';
import initGenderSelection from './genderToggle.js';
import initAuthValidator from './authValidate.js';
import { initTitleComboboxes } from './titleCombobox.js';
import { initPasswordReveal } from './passwordToggle.js';
import { initDatePickerToggle } from './datePickerToggle.js';

document.addEventListener('DOMContentLoaded', () => {
    console.log("Entry auth registered");
    initAuthToggle();
    initGenderSelection();
    initAuthValidator();
    initTitleComboboxes();
    initPasswordReveal();
    initDatePickerToggle();
});