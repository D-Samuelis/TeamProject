@extends('web.layouts.app')

@section('title', 'Bexora | Auth')

@section('content')
    <div class="auth-page">

        <div class="auth-page__banner">

            <div class="auth-section">

                <div class="auth-section__header">
                    We need to know you!
                </div>

                <div class="auth-section__content">
                    In order to use this app you need to have an account and be logged in.
                    urobit tam dropdown pre vyber typu uctu a dat tam i-cko ako info kde hover
                    na to zobrazi info ze ak user zvoli PROVIDER typ tak sa automaticky posle
                    po zaregistrovani poziadavka na schvalenie, ale zaroven sa miesto create
                    account tlacitka zobrazi next step kde clovek zaregistruje svoju prevadzku...
                    az potom tlacitko create account v next stepe...
                </div>

            </div>

        </div>

        <div class="auth-page__form">

            <div class="auth-card" id="authCard">

                <div class="auth-card__header">

                </div>

                <div id="loginSection" class="auth-card__section {{ ($mode ?? 'login') === 'login' ? '' : 'hidden' }}">
                    @include('web.auth.auth_partials.login')
                </div>

                <div id="registerSection"
                    class="auth-card__section {{ ($mode ?? 'login') === 'register' ? '' : 'hidden' }}">
                    @include('web.auth.auth_partials.register')
                </div>

            </div>

        </div>

    </div>

    @vite('resources/js/pages/auth/entry.js')

@endsection
