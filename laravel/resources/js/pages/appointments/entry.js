import { initCollapsibleList } from '../../components/miniLists/miniList.js';
import { initAppointmentListView } from './listView.js'; 

document.addEventListener('DOMContentLoaded', () => {
    console.log("entry loaded");
    const allAppointments = window.BE_DATA.appointments || [];
    const currentUser = window.BE_DATA.user;

    console.log("user:", currentUser);
    console.log("data:", allAppointments);

    let dataToRender = allAppointments;

    // Filter aplikujeme len ak máme usera aj appointments
    if (currentUser && currentUser.id) {
        dataToRender = allAppointments.filter(app => {
            // POZOR: Skontroluj, či sa v DB tvoj kľúč volá presne 'user_id'
            return app.user_id === currentUser.id;
        });
    } else {
        console.warn("User nie je prihlásený alebo chýba ID. Zobrazujem všetko.");
    }

    initAppointmentListView(dataToRender);

    const btnTimeline = document.getElementById('showTimeline');
    const btnList = document.getElementById('showList');
    const timelineView = document.getElementById('timelineView');
    const listView = document.getElementById('listView');

    if (btnTimeline && btnList) {
        btnTimeline.addEventListener('click', () => {
            timelineView.classList.remove('hidden');
            listView.classList.add('hidden');
            btnTimeline.classList.add('active');
            btnList.classList.remove('active');
        });

        btnList.addEventListener('click', () => {
            listView.classList.remove('hidden');
            timelineView.classList.add('hidden');
            btnList.classList.add('active');
            btnTimeline.classList.remove('active');
        });
    }
});