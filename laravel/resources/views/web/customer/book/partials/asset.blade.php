@php
    $selectedBranch = $service->branches->firstWhere('id', request('branch_id'));
@endphp
<div class="public-asset-book">
    <aside class="public-asset-book__sidebar">
        <div class="public-asset-book__sidebar-top">
            <a
                href="{{ route('book.service', ['businessId' => $businessId, 'serviceId' => $service->id, 'target' => request('target')]) }}"
                class="public-asset-book__back-link"
            >
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Service</span>
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

    <div class="display-column">
        <x-ui.breadcrumbs />
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
        </main>
    </div>
</div>


<script>
    window.PUBLIC_ASSET_BOOK_DATA = {
        assetId: {{ $asset->id }},
        serviceId: {{ $service->id }},
        duration: {{ $service->duration_minutes }},
        slotsUrl: @json(route('appointment.slots')),
        storeUrl: @json(route('appointment.store')),
        csrf: @json(csrf_token()),
        rescheduleId: {{ request('reschedule') ? (int) request('reschedule') : 'null' }},
        updateUrlTemplate: @json(route('manage.appointment.reschedule', ['appointmentId' => '__ID__'])),
        appointmentDetailUrlTemplate: @json(route('manage.appointment.show', ['appointmentId' => '__ID__'])),
        myAppointmentsUrl: @json(route('myAppointments')),
    };
</script>
