{{-- resources/views/pages/public/asset/book.blade.php --}}
@extends('layouts.app') {{-- adjust to your layout --}}

@section('head')
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink:          #1c1c1c;
            --ink-muted:    #6e6e6e;
            --ink-faint:    #c4c4c4;
            --paper:        #f8f6f2;
            --paper-2:      #efece5;
            --white:        #ffffff;
            --accent:       #c9622c;
            --accent-bg:    #f7ede5;
            --success:      #2d6a4f;
            --success-bg:   #e0f5ec;
            --border:       #e3dfd7;
            --r:            10px;
        }

        .book-wrap * { box-sizing: border-box; margin: 0; padding: 0; }

        .book-wrap {
            font-family: 'DM Sans', sans-serif;
            color: var(--ink);
            font-size: 15px;
            line-height: 1.6;
            max-width: 960px;
            margin: 2rem auto;
            padding: 0 1.25rem 4rem;
        }

        /* ── header ── */
        .book-header { margin-bottom: 2rem; }
        .book-header h1 {
            font-family: 'DM Serif Display', serif;
            font-size: 2rem;
            font-weight: 400;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        .book-header .meta {
            margin-top: 6px;
            font-size: 13px;
            color: var(--ink-muted);
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .book-header .meta span { display: flex; align-items: center; gap: 4px; }

        /* ── layout ── */
        .book-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 1.5rem;
            align-items: start;
        }
        @media (max-width: 700px) {
            .book-layout { grid-template-columns: 1fr; }
        }

        /* ── calendar card ── */
        .cal-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--r);
            overflow: hidden;
        }

        .cal-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
        }
        .cal-nav button {
            background: none;
            border: 1px solid var(--border);
            border-radius: 6px;
            width: 32px; height: 32px;
            cursor: pointer;
            font-size: 16px;
            display: flex; align-items: center; justify-content: center;
            color: var(--ink-muted);
            transition: border-color .15s, color .15s;
        }
        .cal-nav button:hover { border-color: var(--accent); color: var(--accent); }
        .cal-nav-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1.05rem;
            font-weight: 400;
        }

        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0;
        }
        .cal-dow {
            text-align: center;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--ink-faint);
            padding: 10px 0 6px;
        }
        .cal-day {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            cursor: pointer;
            border-radius: 6px;
            margin: 2px;
            transition: background .12s;
            position: relative;
            border: 1.5px solid transparent;
        }
        .cal-day:hover:not(.empty):not(.past):not(.closed) {
            background: var(--accent-bg);
            border-color: var(--accent);
            color: var(--accent);
        }
        .cal-day.empty   { cursor: default; }
        .cal-day.past    { color: var(--ink-faint); cursor: not-allowed; }
        .cal-day.closed  { color: var(--ink-faint); cursor: not-allowed; }
        .cal-day.today   { font-weight: 500; }
        .cal-day.has-slots::after {
            content: '';
            width: 4px; height: 4px;
            border-radius: 50%;
            background: var(--accent);
            position: absolute;
            bottom: 5px;
        }
        .cal-day.selected {
            background: var(--ink) !important;
            color: white !important;
            border-color: var(--ink) !important;
        }
        .cal-day.selected::after { background: var(--accent); }

        /* ── slots panel ── */
        .slots-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 1.25rem;
            position: sticky;
            top: 1.5rem;
        }
        .slots-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1.1rem;
            font-weight: 400;
            margin-bottom: .75rem;
        }
        .slots-loading {
            text-align: center;
            color: var(--ink-faint);
            padding: 2rem 0;
            font-size: 13px;
        }
        .slots-closed {
            color: var(--ink-muted);
            font-size: 13px;
            padding: 1.5rem 0;
            text-align: center;
        }
        .slots-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            margin-bottom: 1rem;
        }
        .slot-btn {
            padding: 9px 4px;
            border: 1px solid var(--border);
            border-radius: 7px;
            background: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
            color: var(--ink);
            transition: all .12s;
        }
        .slot-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-bg); }
        .slot-btn.active { background: var(--accent); color: white; border-color: var(--accent); }

        /* ── confirm strip ── */
        .confirm-strip {
            border-top: 1px solid var(--border);
            padding-top: 1rem;
            display: none;
        }
        .confirm-strip.show { display: block; }
        .confirm-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 4px 0;
        }
        .confirm-row .lbl { color: var(--ink-muted); }
        .confirm-row .val { font-weight: 500; }
        .book-btn {
            margin-top: 1rem;
            width: 100%;
            padding: 11px;
            background: var(--ink);
            color: white;
            border: none;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s, transform .1s;
        }
        .book-btn:hover   { background: var(--accent); }
        .book-btn:active  { transform: scale(.98); }
        .book-btn:disabled { opacity: .5; cursor: not-allowed; }

        /* ── success banner ── */
        .success-banner {
            display: none;
            background: var(--success-bg);
            color: var(--success);
            border-radius: var(--r);
            padding: 1.25rem;
            font-size: 14px;
            font-weight: 500;
            margin-top: 1.5rem;
            text-align: center;
        }
        .success-banner.show { display: block; }

        /* ── spinner ── */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner {
            display: inline-block;
            width: 16px; height: 16px;
            border: 2px solid var(--border);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin .6s linear infinite;
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div class="book-wrap">

        {{-- Header --}}
        <div class="book-header">
            <h1>Book {{ $asset->name }}</h1>
            <div class="meta">
                <span>📋 {{ $service->name }}</span>
                <span>⏱ {{ $service->duration_minutes }} minutes</span>
            </div>
        </div>

        <div class="book-layout">

            {{-- Calendar --}}
            <div class="cal-card">
                <div class="cal-nav">
                    <button id="prevMonth" aria-label="Previous month">&#8249;</button>
                    <div class="cal-nav-title" id="calTitle"></div>
                    <button id="nextMonth" aria-label="Next month">&#8250;</button>
                </div>
                <div style="padding: 8px 10px 10px;">
                    <div class="cal-grid" id="calDow"></div>
                    <div class="cal-grid" id="calDays"></div>
                </div>
            </div>

            {{-- Slots --}}
            <div class="slots-card">
                <div class="slots-title" id="slotsTitle">Pick a date</div>
                <div id="slotsBody">
                    <div class="slots-closed">← Select a day on the calendar</div>
                </div>
                <div class="confirm-strip" id="confirmStrip">
                    <div class="confirm-row"><span class="lbl">Service</span><span class="val">{{ $service->name }}</span></div>
                    <div class="confirm-row"><span class="lbl">Asset</span><span class="val">{{ $asset->name }}</span></div>
                    <div class="confirm-row"><span class="lbl">Date</span><span class="val" id="cfDate">–</span></div>
                    <div class="confirm-row"><span class="lbl">Time</span><span class="val" id="cfTime">–</span></div>
                    <div class="confirm-row"><span class="lbl">Duration</span><span class="val">{{ $service->duration_minutes }} min</span></div>
                    <button class="book-btn" id="bookBtn" onclick="submitBooking()">Confirm booking</button>
                </div>
            </div>
        </div>

        <div class="success-banner" id="successBanner">
            ✓ Your appointment has been booked! Redirecting…
        </div>

    </div>

    <script>
        (function () {
            // ── Config from Laravel ─────────────────────────────────────────────────
            const ASSET_ID   = {{ $asset->id }};
            const SERVICE_ID = {{ $service->id }};
            const DURATION   = {{ $service->duration_minutes }};
            const SLOTS_URL  = '{{ route('appointment.slots') }}';
            const STORE_URL  = '{{ route('appointment.store') }}';
            const CSRF       = '{{ csrf_token() }}';

            // ── State ───────────────────────────────────────────────────────────────
            const today       = new Date(); today.setHours(0,0,0,0);
            let   viewYear    = today.getFullYear();
            let   viewMonth   = today.getMonth();          // 0-based
            let   slotCache   = {};                        // 'YYYY-MM-DD' => string[]
            let   loadedMonths= new Set();
            let   selectedDate= null;
            let   selectedSlot= null;

            const DOW = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

            // ── Calendar render ─────────────────────────────────────────────────────
            function renderCalendar() {
                document.getElementById('calTitle').textContent =
                    new Date(viewYear, viewMonth, 1)
                        .toLocaleDateString('en-GB', { month: 'long', year: 'numeric' });

                // DOW headers (Mon-first)
                const dowEl = document.getElementById('calDow');
                dowEl.innerHTML = DOW.map(d => `<div class="cal-dow">${d}</div>`).join('');

                const firstDay = new Date(viewYear, viewMonth, 1);
                const totalDays = new Date(viewYear, viewMonth + 1, 0).getDate();
                // Monday-first: Mon=0 … Sun=6
                let startOffset = (firstDay.getDay() + 6) % 7;

                let html = '';
                for (let i = 0; i < startOffset; i++) html += '<div class="cal-day empty"></div>';

                for (let d = 1; d <= totalDays; d++) {
                    const dt  = new Date(viewYear, viewMonth, d);
                    const key = dateKey(dt);
                    const isPast     = dt < today;
                    const isToday    = dt.getTime() === today.getTime();
                    const isSelected = selectedDate === key;
                    const loaded     = key in slotCache;
                    const slots      = slotCache[key];
                    const hasSlots   = loaded && slots.length > 0;
                    const isClosed   = loaded && slots.length === 0;

                    let cls = 'cal-day';
                    if (isPast)              cls += ' past';
                    if (isToday)             cls += ' today';
                    if (isSelected)          cls += ' selected';
                    if (hasSlots && !isPast) cls += ' has-slots';
                    if (isClosed)            cls += ' closed';

                    // Past = not clickable. Closed days ARE clickable (show "Closed" message).
                    const clickable = !isPast;
                    html += `<div class="${cls}" ${clickable ? `onclick="selectDate('${key}')"` : ''}>${d}</div>`;
                }

                document.getElementById('calDays').innerHTML = html;
            }

            // ── Load slots for visible month ────────────────────────────────────────
            async function loadMonth(year, month) {
                const mk = `${year}-${month}`;
                if (loadedMonths.has(mk)) { renderCalendar(); return; }
                loadedMonths.add(mk);

                const from = `${year}-${String(month + 1).padStart(2,'0')}-01`;
                const lastDay = new Date(year, month + 1, 0).getDate();
                const to   = `${year}-${String(month + 1).padStart(2,'0')}-${lastDay}`;

                const url = `${SLOTS_URL}?asset_id=${ASSET_ID}&service_id=${SERVICE_ID}&from=${from}&to=${to}`;

                try {
                    const res  = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
                    const data = await res.json();
                    Object.assign(slotCache, data);
                } catch (e) {
                    console.error('Failed to load slots', e);
                }

                renderCalendar();
            }

            // ── Select a date ───────────────────────────────────────────────────────
            window.selectDate = function(key) {
                selectedDate = key;
                selectedSlot = null;
                renderCalendar();
                renderSlots();
            };

            function renderSlots() {
                if (!selectedDate) return;

                const slots = slotCache[selectedDate] ?? null;
                const d     = new Date(selectedDate + 'T00:00:00');
                const label = d.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long' });

                document.getElementById('slotsTitle').textContent = label;

                if (!slots) {
                    document.getElementById('slotsBody').innerHTML = `<div class="slots-loading"><span class="spinner"></span></div>`;
                    document.getElementById('confirmStrip').classList.remove('show');
                    return;
                }

                if (slots.length === 0) {
                    document.getElementById('slotsBody').innerHTML = `<div class="slots-closed">Closed — no available slots.</div>`;
                    document.getElementById('confirmStrip').classList.remove('show');
                    return;
                }

                const btns = slots.map(s => {
                    const active = s === selectedSlot ? ' active' : '';
                    return `<button class="slot-btn${active}" onclick="selectSlot('${s}')">${s}</button>`;
                }).join('');

                document.getElementById('slotsBody').innerHTML = `<div class="slots-grid">${btns}</div>`;
                document.getElementById('confirmStrip').classList.toggle('show', !!selectedSlot);

                if (selectedSlot) {
                    // compute end time
                    const [h, m] = selectedSlot.split(':').map(Number);
                    const endMin = h * 60 + m + DURATION;
                    const endStr = `${String(Math.floor(endMin/60)).padStart(2,'0')}:${String(endMin%60).padStart(2,'0')}`;

                    document.getElementById('cfDate').textContent = label;
                    document.getElementById('cfTime').textContent = `${selectedSlot} – ${endStr}`;
                }
            }

            window.selectSlot = function(slot) {
                selectedSlot = slot;
                renderSlots();
            };

            // ── Book ────────────────────────────────────────────────────────────────
            window.submitBooking = async function() {
                if (!selectedDate || !selectedSlot) return;

                const btn = document.getElementById('bookBtn');
                btn.disabled = true;
                btn.textContent = 'Booking…';

                try {
                    const res = await fetch(STORE_URL, {
                        method:  'POST',
                        headers: {
                            'Content-Type':  'application/json',
                            'Accept':        'application/json',
                            'X-CSRF-TOKEN':  CSRF,
                        },
                        body: JSON.stringify({
                            asset_id:   ASSET_ID,
                            service_id: SERVICE_ID,
                            date:       selectedDate,
                            start_at:   selectedSlot,
                        }),
                    });

                    const data = await res.json();

                    if (!res.ok) {
                        const msg = data.errors?.start_at?.[0] ?? data.message ?? 'Something went wrong.';
                        alert(msg);
                        btn.disabled = false;
                        btn.textContent = 'Confirm booking';
                        // Refresh this day's slots
                        loadedMonths.delete(`${viewYear}-${viewMonth}`);
                        await loadMonth(viewYear, viewMonth);
                        renderSlots();
                        return;
                    }

                    // Remove booked slot from cache
                    slotCache[selectedDate] = (slotCache[selectedDate] ?? []).filter(s => s !== selectedSlot);
                    selectedSlot = null;

                    document.getElementById('successBanner').classList.add('show');
                    document.getElementById('confirmStrip').classList.remove('show');
                    renderCalendar();

                    setTimeout(() => {
                        window.location.href = '{{ route('myAppointments') }}';
                    }, 2000);

                } catch (e) {
                    alert('Network error. Please try again.');
                    btn.disabled = false;
                    btn.textContent = 'Confirm booking';
                }
            };

            // ── Month navigation ────────────────────────────────────────────────────
            document.getElementById('prevMonth').addEventListener('click', () => {
                if (viewYear === today.getFullYear() && viewMonth === today.getMonth()) return;
                viewMonth--;
                if (viewMonth < 0) { viewMonth = 11; viewYear--; }
                loadMonth(viewYear, viewMonth);
            });

            document.getElementById('nextMonth').addEventListener('click', () => {
                viewMonth++;
                if (viewMonth > 11) { viewMonth = 0; viewYear++; }
                loadMonth(viewYear, viewMonth);
            });

            // ── Helpers ─────────────────────────────────────────────────────────────
            function dateKey(d) {
                return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
            }

            // ── Init ─────────────────────────────────────────────────────────────────
            renderCalendar();
            loadMonth(viewYear, viewMonth);

        })();
    </script>
@endsection
