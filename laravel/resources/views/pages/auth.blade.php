@extends('layouts.app')

@section('title', 'Bexora | Auth')

@section('content')
<div class="auth-page">
    <div class="auth-card" id="authCard">
        
        <div class="auth-card__header">
            <button id="switchToLogin" class="auth-card__tab auth-card__tab--active">Login</button>
            <button id="switchToRegister" class="auth-card__tab">Register</button>
        </div>

        <div id="loginSection" class="auth-card__section {{ $errors->has('name') || $errors->has('password_confirmation') ? 'hidden' : '' }}">
            @include('partials.auth_partials.login')
        </div>

        <div id="registerSection" class="auth-card__section {{ $errors->has('name') || $errors->has('password_confirmation') ? '' : 'hidden' }}">
            @include('partials.auth_partials.register')
        </div>

    </div>
</div>

@vite('resources/js/pages/auth/entry.js')

@endsection