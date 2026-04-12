@extends('web.layouts.app')

@section('title', 'Bexora | My Appointments')

@section('content')
<div class="appointments">
    <aside class="appointments__sidebar">
        {{-- Kalendár a filtre ponechávame tak, ako si ich pripravil --}}
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

            <div id="listView" class="">
                <div class="appointments__control-group">
                    <button class="button button__toggle-left" id="showTimeline"><i class="fa-solid fa-table-columns"></i></button>
                    <button class="button button__toggle-right active" id="showList"><i class="fa-solid fa-list"></i></button>
                </div>

                <div class="list-view-header">
                    <div class="list-view-header__left">
                        <h2 class="timeline-header__title" id="listDateText"></h2>
                        <div class="timeline-info">
                            <i class="fa-solid fa-list-ul"></i>
                            <span>Total Appointments:</span>
                            <span id="listCount">0</span>
                        </div>
                    </div>

                    <div class="business__search-wrapper">
                        <div class="business__search-container">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="appointmentSearchInput" placeholder="Search client or service...">
                        </div>
                    </div>
                </div>

                <div id="listContainer" class="list-container">
                    {{-- SEM príde vygenerovaná tabuľka cez TableRenderer --}}
                    <div id="appointmentTableContainer"></div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    window.BE_DATA = {
        // Tu sa uisti, že Controller posiela $appointments
        appointments: @json($appointments),
        user: @json(auth()->user()),
        routes: {
            
        },
        csrf: "{{ csrf_token() }}"
    };
</script>

@vite('resources/js/pages/appointments/entry.js')
@endsection