@extends('web.layouts.app')

@section('title', 'Bexora | My Appointments')

@section('content')
<div class="appointments">
    <aside class="appointments__sidebar">
        <section class="calendar">
            <div class="calendar__header-controls">
                <div class="calendar__dropdown-group">
                    <select id="calendarMonth" class="calendar__select"></select>
                    <select id="calendarYear" class="calendar__select"></select>
                </div>
            </div>
            <div class="calendar__container" id="calendarContainer"></div>
        </section>

        <section class="appointments__filters">
            <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Status Filters</h3>
            <div id="filterList" class="dropdown__mini-list"></div>
        </section>

        <section class="appointments__pending">
            <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Pending Appointments</h3>
            <div id="pendingList" class="dropdown__mini-list"></div>
        </section>
    </aside>

    <div class="display-column">
        <x-ui.breadcrumbs />
        <main class="appointments__main">
            <div id="timelineView">
                <div class="timeline" id="timelineContainer"></div>
            </div>

            <div id="listView" class="hidden">
                {{-- NOVÝ HEADER (Prebratý z Business) --}}
                <header class="business__header-wrapper">
                    <div class="business__header-corner" id="business__header-corner"></div>

                    <div class="business__header-info">
                        <h2 class="business-header__title" id="listDateText">My Appointments</h2>
                        
                        <div class="business-info">
                            <div class="stat-item stat-item--all">
                                <i class="fa-solid fa-list-ul"></i>
                                <div id="listCount">0</div> Appointments
                            </div>
                            {{-- Tu môžeš pridať ďalšie štatistiky ak chceš (napr. Confirmed, Pending) --}}
                        </div>
                    </div>

                    <div class="business__header-right">
                        <div class="business__header-right-section_1"></div>
                        <div class="business__header-right-section_2">
                            <div class="list-view__search-wrapper">
                                <div class="search-container">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="text" id="appointmentSearchInput" placeholder="Search client or service...">
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="business__body-wrapper">
                    <div id="appointmentTableContainer" class="list-view__body-wrapper">
                        {{-- SEM TableRenderer vloží <table> s triedou appointments-table --}}
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    window.BE_DATA = {
        appointments: @json($appointments),
        user: @json(auth()->user()),
        csrf: "{{ csrf_token() }}"
    };
</script>

@vite('resources/js/pages/appointments/entry.js')
@endsection