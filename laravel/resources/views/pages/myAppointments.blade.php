@extends('layouts.app')

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

        <section class="appointments__pending">
            <h3 class="appointments__subtitle">
                <i class="fa-solid fa-chevron-down"></i>
                Pending Appointments
            </h3>
            <div id="pendingList" class="dropdown__mini-list">
                {{-- JS generated appointmets --}}
            </div>
        </section>
    </aside>

    <main class="appointments__main">
        <header class="timeline-header">
            <div class="timeline-header-main">
                <h2 class="timeline-header__title" id="selectedDateText">February 27, 2026</h2>
                <div class="timeline-info">
                    <div class="timeline-info__icon"> <i class="fa-regular fa-calendar-days"></i> </div>
                    <div class="timeline-info__text"> Appointments:</div>
                    <div class="timeline-info__count">4</div>
                </div>
            </div>
            <div class="timeline-header-controls">
                <div class="appointments__control-group">
                    <button class="button button__toggle-left active" id="showTimeline"><i class="fa-solid fa-table-columns"></i></button>
                    <button class="button button__toggle-right" id="showList"><i class="fa-solid fa-list"></i></button>
                </div>
            </div>
        </header>
        <div id="timelineView">
            <div class="timeline" id="timelineContainer">
                {{-- JS generated time slots --}}
            </div>
        </div>

        <div id="listView" class="hidden">
            <div id="listContainer">
                This shit will be fatched later and dislayed as table...
            </div>
        </div>
    </main>

    <aside class="appointments__controls">
        <section class="appointments__control-group">
            <h3 class="appointments__subtitle">
                <i class="fa-solid fa-chevron-down"></i>
                Status Filters
            </h3>
            <div id="filterList" class="dropdown__mini-list">
                {{-- JS generated filters --}}
            </div>
        </section>
    </aside>
</div>

@vite('resources/js/pages/myAppointments/entry.js')
@endsection