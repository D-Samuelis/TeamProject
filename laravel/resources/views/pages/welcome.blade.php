@extends('layouts.app')

@section('title', 'Bexora | Book a Service')

@section('content')
<div class="welcome">
    <div class="welcome__hero">
        <h1 class="welcome__title">Book an appointment.</h1>
        <input id="aiBookingInput" type="text" class="welcome__input" placeholder="Text appended from textGenerator.js">
    </div>

    <div class="welcome__actions">
        <a href="/search" class="welcome-card welcome-card--manual">
            <h2 class="welcome-card__title">Manual booking</h2>
            <p class="welcome-card__description">Book your appointments manually with standard selection.</p>
            <div class="welcome-card__icon"><i class="fa-solid fa-filter"></i></div>
        </a>

        <a href="/my-appointments" class="welcome-card welcome-card--appointments">
            <h2 class="welcome-card__title">My appointments</h2>
            <p class="welcome-card__description">Manage all your appointments from one place with ease.</p>
            <div class="welcome-card__icon"><i class="fa-solid fa-calendar"></i></div>
        </a>
    </div>
</div>

@vite('resources/js/pages/welcome/entry.js')

@endsection
