export function initPublicAssetBook() {
    const data = window.PUBLIC_ASSET_BOOK_DATA;
    if (!data) return;

    const {
        assetId: ASSET_ID,
        serviceId: SERVICE_ID,
        duration: DURATION,
        slotsUrl: SLOTS_URL,
        storeUrl: STORE_URL,
        csrf: CSRF,
        rescheduleId: RESCHEDULE_ID,
        updateUrlTemplate,
        appointmentDetailUrlTemplate,
        myAppointmentsUrl,
    } = data;

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    let viewYear = today.getFullYear();
    let viewMonth = today.getMonth();
    let slotCache = {};
    let loadedMonths = new Set();
    let selectedDate = null;
    let selectedSlot = null;

    const DOW = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const UPDATE_URL = RESCHEDULE_ID
        ? updateUrlTemplate.replace('__ID__', RESCHEDULE_ID)
        : null;

    function showCenteredBookingMessage(message, type = 'error', onConfirm = null, buttonText = 'OK') {
        let overlay = document.getElementById('assetBookingMessageOverlay');

        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'assetBookingMessageOverlay';
            overlay.className = 'public-asset-book__message-overlay';
            overlay.innerHTML = `
                <div class="public-asset-book__message-box">
                    <div class="public-asset-book__message-text"></div>
                    <button type="button" class="public-asset-book__message-btn">OK</button>
                </div>
            `;

            document.body.appendChild(overlay);
        }

        const text = overlay.querySelector('.public-asset-book__message-text');
        const box = overlay.querySelector('.public-asset-book__message-box');
        const button = overlay.querySelector('.public-asset-book__message-btn');

        text.textContent = message;
        button.textContent = buttonText;

        box.classList.remove(
            'public-asset-book__message-box--error',
            'public-asset-book__message-box--warning',
            'public-asset-book__message-box--success'
        );

        box.classList.add(`public-asset-book__message-box--${type}`);
        overlay.classList.add('show');

        const closeOverlay = () => {
            overlay.classList.remove('show');
        };

        button.onclick = () => {
            closeOverlay();
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        };

        overlay.onclick = (e) => {
            if (e.target === overlay) {
                closeOverlay();
            }
        };
    }

    function dateKey(d) {
        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    }

    function renderCalendar() {
        const calTitle = document.getElementById('calTitle');
        const calDow = document.getElementById('calDow');
        const calDays = document.getElementById('calDays');

        if (!calTitle || !calDow || !calDays) return;

        calTitle.textContent = new Date(viewYear, viewMonth, 1).toLocaleDateString('en-GB', {
            month: 'long',
            year: 'numeric',
        });

        calDow.innerHTML = DOW.map(d => `<div class="public-asset-book__dow">${d}</div>`).join('');

        const firstDay = new Date(viewYear, viewMonth, 1);
        const totalDays = new Date(viewYear, viewMonth + 1, 0).getDate();
        const startOffset = (firstDay.getDay() + 6) % 7;

        let html = '';

        for (let i = 0; i < startOffset; i++) {
            html += '<div class="public-asset-book__day empty"></div>';
        }

        for (let d = 1; d <= totalDays; d++) {
            const dt = new Date(viewYear, viewMonth, d);
            const key = dateKey(dt);
            const isPast = dt < today;
            const isToday = dt.getTime() === today.getTime();
            const isSelected = selectedDate === key;
            const loaded = key in slotCache;
            const slots = slotCache[key];
            const hasSlots = loaded && slots.length > 0;
            const isClosed = loaded && slots.length === 0;

            let cls = 'public-asset-book__day';
            if (isPast) cls += ' past';
            if (isToday) cls += ' today';
            if (isSelected) cls += ' selected';
            if (hasSlots && !isPast) cls += ' has-slots';
            if (isClosed) cls += ' closed';

            const clickable = !isPast;

            html += `<div class="${cls}" ${clickable ? `data-date="${key}"` : ''}>${d}</div>`;
        }

        calDays.innerHTML = html;

        calDays.querySelectorAll('[data-date]').forEach(day => {
            day.addEventListener('click', () => {
                selectDate(day.dataset.date);
            });
        });

        renderCalendarNote();
    }

    function renderCalendarNote() {
        const note = document.getElementById('calendarAvailabilityNote');
        if (!note) return;

        const prefix = `${viewYear}-${String(viewMonth + 1).padStart(2, '0')}-`;
        const monthKeys = Object.keys(slotCache).filter(key => key.startsWith(prefix));
        const availableDays = monthKeys.filter(key => (slotCache[key] || []).length > 0).length;

        if (!monthKeys.length) {
            note.classList.add('public-asset-book__calendar-note--hidden');
            return;
        }

        if (availableDays === 0) {
            note.classList.remove('public-asset-book__calendar-note--hidden');
        } else {
            note.classList.add('public-asset-book__calendar-note--hidden');
        }
    }

    async function loadMonth(year, month) {
        const mk = `${year}-${month}`;
        if (loadedMonths.has(mk)) {
            renderCalendar();
            return;
        }

        loadedMonths.add(mk);

        const from = `${year}-${String(month + 1).padStart(2, '0')}-01`;
        const lastDay = new Date(year, month + 1, 0).getDate();
        const to = `${year}-${String(month + 1).padStart(2, '0')}-${lastDay}`;

        const url = `${SLOTS_URL}?asset_id=${ASSET_ID}&service_id=${SERVICE_ID}&from=${from}&to=${to}`;

        try {
            const res = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
            });

            const responseData = await res.json();
            Object.assign(slotCache, responseData);
        } catch (e) {
            console.error('Failed to load slots', e);
        }

        renderCalendar();
    }

    function selectDate(key) {
        selectedDate = key;
        selectedSlot = null;
        renderCalendar();
        renderSlots();
    }

    function renderSlots() {
        if (!selectedDate) return;

        const slotsBody = document.getElementById('slotsBody');
        const slotsTitle = document.getElementById('slotsTitle');
        const confirmStrip = document.getElementById('confirmStrip');
        const cfDate = document.getElementById('cfDate');
        const cfTime = document.getElementById('cfTime');

        if (!slotsBody || !slotsTitle || !confirmStrip || !cfDate || !cfTime) return;

        const slots = slotCache[selectedDate] ?? null;
        const d = new Date(`${selectedDate}T00:00:00`);
        const label = d.toLocaleDateString('en-GB', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
        });

        slotsTitle.textContent = label;

        if (!slots) {
            slotsBody.innerHTML =
                `<div class="public-asset-book__slots-loading"><span class="public-asset-book__spinner"></span></div>`;
            confirmStrip.classList.remove('show');
            return;
        }

        if (slots.length === 0) {
            slotsBody.innerHTML =
                `<div class="public-asset-book__slots-message">No free slots for this day.</div>`;
            confirmStrip.classList.remove('show');
            return;
        }

        const btns = slots.map(slot => {
            const active = slot === selectedSlot ? ' active' : '';
            return `<button class="public-asset-book__slot-btn${active}" data-slot="${slot}">${slot}</button>`;
        }).join('');

        slotsBody.innerHTML = `<div class="public-asset-book__slots-grid">${btns}</div>`;

        slotsBody.querySelectorAll('[data-slot]').forEach(button => {
            button.addEventListener('click', () => {
                selectSlot(button.dataset.slot);
            });
        });

        confirmStrip.classList.toggle('show', !!selectedSlot);

        if (selectedSlot) {
            const [h, m] = selectedSlot.split(':').map(Number);
            const endMin = h * 60 + m + DURATION;
            const endStr = `${String(Math.floor(endMin / 60)).padStart(2, '0')}:${String(endMin % 60).padStart(2, '0')}`;

            cfDate.textContent = label;
            cfTime.textContent = `${selectedSlot} – ${endStr}`;
        }
    }

    function selectSlot(slot) {
        selectedSlot = slot;
        renderSlots();
    }

    async function submitBooking() {
        if (!selectedDate || !selectedSlot) return;

        const btn = document.getElementById('bookBtn');
        const confirmStrip = document.getElementById('confirmStrip');

        if (!btn || !confirmStrip) return;

        btn.disabled = true;
        btn.textContent = RESCHEDULE_ID ? 'Rescheduling…' : 'Booking…';

        const isReschedule = !!RESCHEDULE_ID;
        const url = isReschedule ? UPDATE_URL : STORE_URL;
        const method = isReschedule ? 'PATCH' : 'POST';
        const body = isReschedule
            ? { date: selectedDate, start_at: selectedSlot }
            : { asset_id: ASSET_ID, service_id: SERVICE_ID, date: selectedDate, start_at: selectedSlot };

        try {
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
                body: JSON.stringify(body),
            });

            const responseData = await res.json();

            if (!res.ok) {
                const msg = responseData.errors?.start_at?.[0] ?? responseData.message ?? 'Something went wrong.';

                if (msg === 'Unauthenticated.') {
                    showCenteredBookingMessage('Please sign in to complete your booking.', 'warning');
                } else {
                    showCenteredBookingMessage(msg, 'error');
                }

                btn.disabled = false;
                btn.textContent = isReschedule ? 'Confirm Reschedule' : 'Confirm booking';
                loadedMonths.delete(`${viewYear}-${viewMonth}`);
                await loadMonth(viewYear, viewMonth);
                renderSlots();
                return;
            }

            slotCache[selectedDate] = (slotCache[selectedDate] ?? []).filter(s => s !== selectedSlot);
            selectedSlot = null;

            confirmStrip.classList.remove('show');
            renderCalendar();

            btn.disabled = false;
            btn.textContent = isReschedule ? 'Confirm Reschedule' : 'Confirm booking';

            if (isReschedule) {
                showCenteredBookingMessage(
                    'Appointment rescheduled successfully.',
                    'success',
                    () => {
                        window.location.href = appointmentDetailUrlTemplate.replace('__ID__', RESCHEDULE_ID);
                    },
                    'Go to appointment'
                );
            } else {
                showCenteredBookingMessage(
                    'Appointment booked successfully.',
                    'success',
                    () => {
                        window.location.href = myAppointmentsUrl;
                    },
                    'Go to my appointments'
                );
            }
        } catch (e) {
            showCenteredBookingMessage('Network error. Please try again.', 'error');
            btn.disabled = false;
            btn.textContent = RESCHEDULE_ID ? 'Confirm Reschedule' : 'Confirm booking';
        }
    }

    function bindControls() {
        const prevMonth = document.getElementById('prevMonth');
        const nextMonth = document.getElementById('nextMonth');
        const bookBtn = document.getElementById('bookBtn');

        if (prevMonth) {
            prevMonth.addEventListener('click', () => {
                if (viewYear === today.getFullYear() && viewMonth === today.getMonth()) return;
                viewMonth--;
                if (viewMonth < 0) {
                    viewMonth = 11;
                    viewYear--;
                }
                loadMonth(viewYear, viewMonth);
            });
        }

        if (nextMonth) {
            nextMonth.addEventListener('click', () => {
                viewMonth++;
                if (viewMonth > 11) {
                    viewMonth = 0;
                    viewYear++;
                }
                loadMonth(viewYear, viewMonth);
            });
        }

        if (bookBtn) {
            bookBtn.addEventListener('click', submitBooking);
        }
    }

    renderCalendar();
    bindControls();
    loadMonth(viewYear, viewMonth);

    if (RESCHEDULE_ID) {
        const bookBtn = document.getElementById('bookBtn');
        if (bookBtn) {
            bookBtn.textContent = 'Confirm Reschedule';
        }
    }
}