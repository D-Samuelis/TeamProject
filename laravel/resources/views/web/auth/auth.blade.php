@extends('web.layouts.app')

@section('title', 'Bexora | Auth')

@section('content')
    <div class="auth-page">

        <div class="auth-page__form">

            <div class="auth-page__intro">
                <h1 class="auth-page__intro-title">Make reservations feel easy.</h1>
                <p class="auth-page__intro-text">
                    Sign in or register to book appointments and manage services in one place.
                </p>
            </div>

            <div class="auth-card" id="authCard">
                <div id="loginSection" class="auth-card__section {{ ($mode ?? 'login') === 'login' ? '' : 'hidden' }}">
                    @include('web.auth.partials.login')
                </div>

                <div id="registerSection"
                    class="auth-card__section {{ ($mode ?? 'login') === 'register' ? '' : 'hidden' }}">
                    @include('web.auth.partials.register')
                </div>

            </div>

        </div>

    </div>

    @vite('resources/js/pages/auth/entry.js')

@endsection
