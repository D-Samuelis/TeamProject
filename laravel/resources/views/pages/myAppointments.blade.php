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
            <h3 class="appointments__subtitle">Pending Appointments</h3>
            <div id="pendingList" class="appointments__mini-list">
                {{-- JS generated appointmets --}}
            </div>
        </section>
    </aside>

    <main class="appointments__main">
        <header class="timeline-header">
            <h2 class="timeline-header__title" id="selectedDateText">February 27, 2026</h2>
            <div class="timeline-info">
                <div class="timeline-info__icon"> <i class="fa-regular fa-calendar-days"></i> </div>
                <div class="timeline-info__text"> Appointments:</div>
                <div class="timeline-info__count">4</div>
            </div>
        </header>
        <div class="timeline" id="timelineContainer">
            {{-- JS generated time slots --}}
        </div>
    </main>

    <aside class="appointments__controls">
        <div class="appointments__control-group">
            <button class="button button--secondary" id="viewSwitcher">Switch to List View</button>
        </div>
        <div class="appointments__control-group">
            <h3 class="appointments__subtitle">Filters</h3>
            {{-- Filters --}}
        </div>
    </aside>
</div>

@vite('resources/js/pages/myAppointments/entry.js')
@endsection