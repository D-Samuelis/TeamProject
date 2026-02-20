import './bootstrap';
import './components/profile/profile.js';
import './components/theme/themeToggle.js';
import './components/notifications/notifications.js';

import { logWindowSizeOnResize } from './utils/helpers.js';
import initProfileMenu from './components/profile/profile.js';
import initNotificationsMenu from './components/notifications/notifications.js';
import initTheme from './components/theme/themeToggle.js';

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initProfileMenu();
    initNotificationsMenu();
    logWindowSizeOnResize();
});