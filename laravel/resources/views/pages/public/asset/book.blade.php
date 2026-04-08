{{-- resources/views/pages/public/asset/book.blade.php --}}
@extends('layouts.app')

@section('title', 'Bexora | Choose Time')

@section('content')
@php
    $selectedBranch = $service->branches->firstWhere('id', request('branch_id'));
@endphp

<div class="public-asset-book">
    <aside class="public-asset-book__sidebar">
        <div class="public-asset-book__sidebar-top">
            <a
                href="{{ route('service.book', ['serviceId' => $service->id, 'branch_id' => request('branch_id')]) }}"
                class="public-asset-book__back-link"
            >
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Assets</span>
            </a>
        </div>

        <section class="public-asset-book__sidebar-section">
            <div class="public-asset-book__steps">
                <div class="public-asset-book__step">
                    <span class="public-asset-book__step-number">1</span>
                    <div class="public-asset-book__step-text">
                        <strong>Choose Asset</strong>
                        <span>Selected asset for this service</span>
                    </div>
                </div>

                <div class="public-asset-book__step-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <div class="public-asset-book__step public-asset-book__step--active">
                    <span class="public-asset-book__step-number">2</span>
                    <div class="public-asset-book__step-text">
                        <strong>Choose Time</strong>
                        <span>Pick an available date and time</span>
                    </div>
                </div>
            </div>
        </section>
    </aside>

    <main class="public-asset-book__main">
        <header class="public-asset-book__service-header">
            <div class="public-asset-book__service-header-content">
                <p class="public-asset-book__step-label-top">Step 2 of 2</p>
                <h1 class="public-asset-book__service-title">Book {{ $asset->name }}</h1>

                <div class="public-asset-book__service-meta">
                    <span class="public-asset-book__meta-badge">
                        <i class="fa-solid fa-cube"></i>
                        <span>{{ $asset->name }}</span>
                    </span>

                    <span class="public-asset-book__meta-badge">
                        <i class="fa-solid fa-scissors"></i>
                        <span>{{ $service->name }}</span>
                    </span>

                    @if($selectedBranch)
                        <span class="public-asset-book__meta-badge">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ $selectedBranch->name }}</span>
                        </span>
                    @endif

                    @if($service->business)
                        <span class="public-asset-book__meta-badge">
                            <i class="fa-solid fa-shop"></i>
                            <span>{{ $service->business->name }}</span>
                        </span>
                    @endif

                    

                    @if($service->duration_minutes)
                        <span class="public-asset-book__meta-badge">
                            <i class="fa-regular fa-clock"></i>
                            <span>{{ $service->duration_minutes }} min</span>
                        </span>
                    @endif

                    @if(!is_null($service->price))
                        <span class="public-asset-book__meta-badge">
                            <i class="fa-solid fa-tag"></i>
                            <span>€{{ number_format((float) $service->price, 2) }}</span>
                        </span>
                    @endif
                </div>

                <div class="public-asset-book__legend">
                    <span class="public-asset-book__legend-dot"></span>
                    <span>Day has available slots</span>
                </div>

                <p class="public-asset-book__header-hint" id="calendarHint">
                    <i class="fa-solid fa-arrow-down"></i>
                    Choose a highlighted day below to see free time slots.
                </p>
            </div>
        </header>

        <div class="public-asset-book__layout">
            <section class="public-asset-book__calendar-panel">
                <div class="public-asset-book__calendar-nav">
                    <button id="prevMonth" aria-label="Previous month" class="public-asset-book__nav-btn">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <div class="public-asset-book__calendar-title" id="calTitle"></div>

                    <button id="nextMonth" aria-label="Next month" class="public-asset-book__nav-btn">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                <div class="public-asset-book__calendar-body">
                    <div class="public-asset-book__calendar-grid" id="calDow"></div>
                    <div class="public-asset-book__calendar-grid" id="calDays"></div>
                </div>

                <div class="public-asset-book__calendar-note public-asset-book__calendar-note--hidden" id="calendarAvailabilityNote">
                    No available slots found in this month.
                </div>
            </section>

            <aside class="public-asset-book__slots-panel">
                <div class="public-asset-book__slots-title" id="slotsTitle">Pick a date</div>

                <div id="slotsBody">
                    <div class="public-asset-book__slots-message">← Select a day on the calendar</div>
                </div>

                <div class="public-asset-book__confirm-strip" id="confirmStrip">
                    <div class="public-asset-book__confirm-row">
                        <span class="public-asset-book__confirm-label">Service</span>
                        <span class="public-asset-book__confirm-value">{{ $service->name }}</span>
                    </div>

                    <div class="public-asset-book__confirm-row">
                        <span class="public-asset-book__confirm-label">Asset</span>
                        <span class="public-asset-book__confirm-value">{{ $asset->name }}</span>
                    </div>

                    @if($selectedBranch)
                        <div class="public-asset-book__confirm-row">
                            <span class="public-asset-book__confirm-label">Branch</span>
                            <span class="public-asset-book__confirm-value">{{ $selectedBranch->name }}</span>
                        </div>
                    @endif

                    <div class="public-asset-book__confirm-row">
                        <span class="public-asset-book__confirm-label">Date</span>
                        <span class="public-asset-book__confirm-value" id="cfDate">–</span>
                    </div>

                    <div class="public-asset-book__confirm-row">
                        <span class="public-asset-book__confirm-label">Time</span>
                        <span class="public-asset-book__confirm-value" id="cfTime">–</span>
                    </div>

                    <div class="public-asset-book__confirm-row">
                        <span class="public-asset-book__confirm-label">Duration</span>
                        <span class="public-asset-book__confirm-value">{{ $service->duration_minutes }} min</span>
                    </div>

                    @if(!is_null($service->price))
                        <div class="public-asset-book__confirm-row">
                            <span class="public-asset-book__confirm-label">Price</span>
                            <span class="public-asset-book__confirm-value">€{{ number_format((float) $service->price, 2) }}</span>
                        </div>
                    @endif

                    <button class="public-asset-book__confirm-btn" id="bookBtn" onclick="submitBooking()">
                        Confirm booking
                    </button>
                </div>
            </aside>
        </div>

        <div class="public-asset-book__success-banner" id="successBanner">
            Appointment booked successfully. Redirecting...
        </div>
    </main>
</div>

<style>
    .public-asset-book {
        display: grid;
        grid-template-columns: 16.75rem 1fr;
        width: 100%;
        min-height: calc(100vh - 100px - 24px);
        border-top: 1px solid var(--color-border-light);
        border-bottom: 1px solid var(--color-border-light);
        overflow: hidden;
        background-color: var(--color-bg);
    }

    .public-asset-book__sidebar {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        height: 100%;
        padding: 1rem;
        overflow-y: auto;
        border-right: 1px solid var(--color-border-light);
        background-color: var(--color-bg);
    }

    .public-asset-book__sidebar-top {
        display: flex;
        align-items: center;
    }

    .public-asset-book__back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--color-text);
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.2s ease;
    }

    .public-asset-book__back-link:hover {
        color: var(--color-primary);
    }

    .public-asset-book__sidebar-section {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .public-asset-book__steps {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .public-asset-book__step {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        padding: 0.85rem;
        border: 1px solid var(--color-border-light);
        border-radius: 0.75rem;
        background-color: var(--color-bg);
        opacity: 0.75;
    }

    .public-asset-book__step--active {
        opacity: 1;
        border-color: var(--color-primary);
        box-shadow: 0 4px 14px var(--color-box-shadow);
    }

    .public-asset-book__step-number {
        width: 2rem;
        height: 2rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.95rem;
        font-weight: 700;
        border: 1px solid var(--color-border-light);
        background-color: var(--color-bg-complement);
        color: var(--color-text);
    }

    .public-asset-book__step--active .public-asset-book__step-number {
        background-color: var(--color-primary);
        border-color: var(--color-primary);
        color: var(--color-text-white);
    }

    .public-asset-book__step-text {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        min-width: 0;
    }

    .public-asset-book__step-text strong {
        font-size: 0.95rem;
        color: var(--color-text);
    }

    .public-asset-book__step-text span {
        font-size: 0.85rem;
        color: var(--color-text-unimportant-dark);
        line-height: 1.45;
    }

    .public-asset-book__step-dots {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.35rem;
        padding: 0.15rem 0;
    }

    .public-asset-book__step-dots span {
        width: 0.32rem;
        height: 0.32rem;
        border-radius: 999px;
        background-color: var(--color-text-unimportant-light);
    }

    .public-asset-book__main {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        min-width: 0;
        padding: 1rem;
        background-color: var(--color-bg);
    }

    .public-asset-book__service-header {
        border: 1px solid var(--color-border-light);
        border-radius: 0.85rem;
        background-color: var(--color-bg-complement);
    }

    .public-asset-book__service-header-content {
        padding: 1.25rem;
    }

    .public-asset-book__step-label-top {
        margin: 0 0 0.45rem;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--color-primary);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .public-asset-book__service-title {
        margin: 0;
        font-size: clamp(1.65rem, 3vw, 2.2rem);
        line-height: 1.15;
        color: var(--color-text);
    }

    .public-asset-book__service-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        margin-top: 1rem;
    }

    .public-asset-book__meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.58rem 0.85rem;
        border: 1px solid var(--color-border-light);
        border-radius: 999px;
        background-color: var(--color-bg);
        color: var(--color-text);
        font-size: 0.92rem;
        line-height: 1;
        max-width: 100%;
    }

    .public-asset-book__meta-badge i {
        color: var(--color-primary);
        flex-shrink: 0;
    }

    .public-asset-book__meta-badge span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .public-asset-book__legend {
        margin-top: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--color-text-unimportant-dark);
        font-size: 0.92rem;
    }

    .public-asset-book__legend-dot {
        width: 0.52rem;
        height: 0.52rem;
        border-radius: 999px;
        background-color: #2e9f5b;
        display: inline-block;
        flex-shrink: 0;
    }

    .public-asset-book__header-hint {
        margin: 0.55rem 0 0;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        color: var(--color-text-unimportant-dark);
        font-size: 0.92rem;
    }

    .public-asset-book__layout {
        display: grid;
        grid-template-columns: minmax(0, 560px) minmax(320px, 1fr);
        gap: 1rem;
        align-items: start;
    }

    .public-asset-book__calendar-panel,
    .public-asset-book__slots-panel {
        border: 1px solid var(--color-border-light);
        border-radius: 0.85rem;
        background-color: var(--color-bg-complement);
    }

    .public-asset-book__calendar-panel {
        max-width: 560px;
    }

    .public-asset-book__calendar-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.8rem 0.95rem;
        border-bottom: 1px solid var(--color-border-light);
    }

    .public-asset-book__nav-btn {
        width: 2rem;
        height: 2rem;
        border: 1px solid var(--color-border-light);
        border-radius: 0.55rem;
        background-color: var(--color-bg);
        color: var(--color-text-unimportant-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: border-color 0.2s ease, color 0.2s ease, background-color 0.2s ease;
    }

    .public-asset-book__nav-btn:hover {
        border-color: var(--color-primary);
        color: var(--color-primary);
        background-color: var(--color-bg-complement);
    }

    .public-asset-book__calendar-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--color-text);
    }

    .public-asset-book__calendar-body {
        padding: 0.45rem 0.6rem 0.65rem;
    }

    .public-asset-book__calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
    }

    .public-asset-book__dow {
        text-align: center;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--color-text-unimportant-light);
        padding: 0.45rem 0 0.35rem;
    }

    .public-asset-book__day {
        aspect-ratio: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 2px;
        border-radius: 0.55rem;
        border: 1px solid transparent;
        cursor: pointer;
        position: relative;
        color: var(--color-text);
        transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
        font-size: 0.84rem;
    }

    .public-asset-book__day:hover:not(.empty):not(.past):not(.closed) {
        background-color: var(--color-bg);
        border-color: #2e9f5b;
        color: #2e9f5b;
    }

    .public-asset-book__day.empty {
        cursor: default;
    }

    .public-asset-book__day.past,
    .public-asset-book__day.closed {
        color: var(--color-text-unimportant-light);
        cursor: not-allowed;
    }

    .public-asset-book__day.today {
        font-weight: 700;
    }

    .public-asset-book__day.has-slots::after {
        content: '';
        width: 0.34rem;
        height: 0.34rem;
        border-radius: 999px;
        background-color: #2e9f5b;
        position: absolute;
        bottom: 0.24rem;
    }

    .public-asset-book__day.selected {
        background-color: var(--color-primary) !important;
        color: var(--color-text-white) !important;
        border-color: var(--color-primary) !important;
    }

    .public-asset-book__calendar-note {
        padding: 0 0.95rem 0.85rem;
        color: var(--color-text-unimportant-dark);
        font-size: 0.9rem;
    }

    .public-asset-book__calendar-note--hidden {
        display: none;
    }

    .public-asset-book__slots-panel {
        padding: 1rem;
        position: sticky;
        top: 1rem;
        min-width: 0;
    }

    .public-asset-book__slots-title {
        font-size: 1.12rem;
        font-weight: 700;
        color: var(--color-text);
        margin-bottom: 0.95rem;
        letter-spacing: 0.01em;
    }

    .public-asset-book__slots-message,
    .public-asset-book__slots-loading {
        text-align: center;
        color: var(--color-text-unimportant-dark);
        padding: 1.5rem 0;
        font-size: 0.9rem;
    }

    .public-asset-book__slots-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.45rem;
        margin-bottom: 1rem;
    }

    .public-asset-book__slot-btn {
        padding: 0.72rem 0.35rem;
        border: 1px solid var(--color-border-light);
        border-radius: 0.55rem;
        background-color: var(--color-bg);
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
        color: var(--color-text);
        transition: all 0.15s ease;
    }

    .public-asset-book__slot-btn:hover {
        border-color: #2e9f5b;
        color: #2e9f5b;
        background-color: var(--color-bg-complement);
    }

    .public-asset-book__slot-btn.active {
        background-color: var(--color-primary);
        color: var(--color-text-white);
        border-color: var(--color-primary);
    }

    .public-asset-book__confirm-strip {
        border-top: 1px solid var(--color-border-light);
        padding-top: 1rem;
        display: none;
    }

    .public-asset-book__confirm-strip.show {
        display: block;
    }

    .public-asset-book__confirm-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        font-size: 0.88rem;
        padding: 0.23rem 0;
    }

    .public-asset-book__confirm-label {
        color: var(--color-text-unimportant-dark);
    }

    .public-asset-book__confirm-value {
        color: var(--color-text);
        font-weight: 600;
        text-align: right;
    }

    .public-asset-book__confirm-btn {
        margin-top: 1rem;
        width: 100%;
        min-height: 2.85rem;
        border: none;
        border-radius: 0.7rem;
        background-color: var(--color-primary);
        color: var(--color-text-white);
        font-size: 0.92rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease, opacity 0.2s ease;
    }

    .public-asset-book__confirm-btn:hover {
        background-color: var(--color-primary-hover);
    }

    .public-asset-book__confirm-btn:active {
        transform: scale(0.98);
    }

    .public-asset-book__confirm-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .public-asset-book__success-banner {
        display: none;
        border: 1px solid var(--color-border-light);
        border-radius: 0.85rem;
        background-color: var(--color-bg-complement);
        color: var(--color-text);
        padding: 1rem 1.25rem;
        font-weight: 600;
    }

    .public-asset-book__success-banner.show {
        display: block;
    }

    @keyframes publicAssetBookSpin {
        to { transform: rotate(360deg); }
    }

    .public-asset-book__spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid var(--color-border-light);
        border-top-color: var(--color-primary);
        border-radius: 50%;
        animation: publicAssetBookSpin .6s linear infinite;
        vertical-align: middle;
    }

    @media (max-width: 1100px) {
        .public-asset-book__layout {
            grid-template-columns: 1fr;
        }

        .public-asset-book__calendar-panel {
            max-width: none;
        }

        .public-asset-book__slots-panel {
            position: static;
        }
    }

    @media (max-width: 992px) {
        .public-asset-book {
            grid-template-columns: 1fr;
        }

        .public-asset-book__sidebar {
            border-right: none;
            border-bottom: 1px solid var(--color-border-light);
        }
    }

    @media (max-width: 768px) {
        .public-asset-book__main {
            padding: 0.75rem;
        }

        .public-asset-book__service-header-content,
        .public-asset-book__slots-panel {
            padding: 0.9rem;
        }

        .public-asset-book__calendar-nav {
            padding: 0.75rem 0.85rem;
        }

        .public-asset-book__calendar-body {
            padding: 0.4rem 0.4rem 0.6rem;
        }

        .public-asset-book__slots-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<script>
    (function () {
        const ASSET_ID   = {{ $asset->id }};
        const SERVICE_ID = {{ $service->id }};
        const DURATION   = {{ $service->duration_minutes }};
        const SLOTS_URL  = '{{ route('appointment.slots') }}';
        const STORE_URL  = '{{ route('appointment.store') }}';
        const CSRF       = '{{ csrf_token() }}';

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        let viewYear = today.getFullYear();
        let viewMonth = today.getMonth();
        let slotCache = {};
        let loadedMonths = new Set();
        let selectedDate = null;
        let selectedSlot = null;

        const DOW = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        function renderCalendar() {
            document.getElementById('calTitle').textContent =
                new Date(viewYear, viewMonth, 1).toLocaleDateString('en-GB', {
                    month: 'long',
                    year: 'numeric'
                });

            const dowEl = document.getElementById('calDow');
            dowEl.innerHTML = DOW.map(d => `<div class="public-asset-book__dow">${d}</div>`).join('');

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

                html += `<div class="${cls}" ${clickable ? `onclick="selectDate('${key}')"` : ''}>${d}</div>`;
            }

            document.getElementById('calDays').innerHTML = html;
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
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    }
                });

                const data = await res.json();
                Object.assign(slotCache, data);
            } catch (e) {
                console.error('Failed to load slots', e);
            }

            renderCalendar();
        }

        window.selectDate = function (key) {
            selectedDate = key;
            selectedSlot = null;
            renderCalendar();
            renderSlots();
        };

        function renderSlots() {
            if (!selectedDate) return;

            const slots = slotCache[selectedDate] ?? null;
            const d = new Date(selectedDate + 'T00:00:00');
            const label = d.toLocaleDateString('en-GB', {
                weekday: 'long',
                day: 'numeric',
                month: 'long'
            });

            document.getElementById('slotsTitle').textContent = label;

            if (!slots) {
                document.getElementById('slotsBody').innerHTML =
                    `<div class="public-asset-book__slots-loading"><span class="public-asset-book__spinner"></span></div>`;
                document.getElementById('confirmStrip').classList.remove('show');
                return;
            }

            if (slots.length === 0) {
                document.getElementById('slotsBody').innerHTML =
                    `<div class="public-asset-book__slots-message">No free slots for this day.</div>`;
                document.getElementById('confirmStrip').classList.remove('show');
                return;
            }

            const btns = slots.map(slot => {
                const active = slot === selectedSlot ? ' active' : '';
                return `<button class="public-asset-book__slot-btn${active}" onclick="selectSlot('${slot}')">${slot}</button>`;
            }).join('');

            document.getElementById('slotsBody').innerHTML =
                `<div class="public-asset-book__slots-grid">${btns}</div>`;

            document.getElementById('confirmStrip').classList.toggle('show', !!selectedSlot);

            if (selectedSlot) {
                const [h, m] = selectedSlot.split(':').map(Number);
                const endMin = h * 60 + m + DURATION;
                const endStr = `${String(Math.floor(endMin / 60)).padStart(2, '0')}:${String(endMin % 60).padStart(2, '0')}`;

                document.getElementById('cfDate').textContent = label;
                document.getElementById('cfTime').textContent = `${selectedSlot} – ${endStr}`;
            }
        }

        window.selectSlot = function (slot) {
            selectedSlot = slot;
            renderSlots();
        };

        window.submitBooking = async function () {
            if (!selectedDate || !selectedSlot) return;

            const btn = document.getElementById('bookBtn');
            btn.disabled = true;
            btn.textContent = 'Booking…';

            try {
                const res = await fetch(STORE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({
                        asset_id: ASSET_ID,
                        service_id: SERVICE_ID,
                        date: selectedDate,
                        start_at: selectedSlot,
                    }),
                });

                const data = await res.json();

                if (!res.ok) {
                    const msg = data.errors?.start_at?.[0] ?? data.message ?? 'Something went wrong.';
                    alert(msg);
                    btn.disabled = false;
                    btn.textContent = 'Confirm booking';
                    loadedMonths.delete(`${viewYear}-${viewMonth}`);
                    await loadMonth(viewYear, viewMonth);
                    renderSlots();
                    return;
                }

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

        document.getElementById('prevMonth').addEventListener('click', () => {
            if (viewYear === today.getFullYear() && viewMonth === today.getMonth()) return;
            viewMonth--;
            if (viewMonth < 0) {
                viewMonth = 11;
                viewYear--;
            }
            loadMonth(viewYear, viewMonth);
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            viewMonth++;
            if (viewMonth > 11) {
                viewMonth = 0;
                viewYear++;
            }
            loadMonth(viewYear, viewMonth);
        });

        function dateKey(d) {
            return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        }

        renderCalendar();
        loadMonth(viewYear, viewMonth);
    })();
</script>
@endsection