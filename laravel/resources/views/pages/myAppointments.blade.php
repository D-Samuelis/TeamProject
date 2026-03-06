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

        <section class="appointments__filters">
            <h3 class="appointments__subtitle">
                <i class="fa-solid fa-chevron-down"></i>
                Status Filters
            </h3>
            <div id="filterList" class="dropdown__mini-list">
                {{-- JS generated filters --}}
            </div>
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
        <div id="timelineView">
            <div class="timeline" id="timelineContainer">
                {{-- JS generates: 
                    View Switcher
                    Column Headers
                    Timeline
                    Columns with timeslots
                --}}
            </div>
        </div>

        <div id="listView" class="hidden">
            <div class="appointments__control-group">
                <button class="button button__toggle-left" id="showTimeline"><i class="fa-solid fa-table-columns"></i></button>
                <button class="button button__toggle-right active" id="showList"><i class="fa-solid fa-list"></i></button>
            </div>

            <div class="list-view-header">
                <div class="list-view-header__left">
                    <h2 class="timeline-header__title" id="listDateText">February 27, 2026</h2>
                    <div class="timeline-info">
                        <i class="fa-solid fa-list-ul"></i>
                        <span>Total Appointments:</span>
                        <span id="listCount">4</span>
                    </div>
                </div>
            </div>
        
            <div id="listContainer" class="list-container">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Client</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="listTableBody">
                        {{-- JS generated rows --}}
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

@vite('resources/js/pages/myAppointments/entry.js')
@endsection